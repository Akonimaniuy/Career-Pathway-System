<?php
// filepath: c:\wamp64\www\cpsproject\models\CommentModel.php
namespace models;

use core\Model;
use core\Database;
use PDO;

class CommentModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get comments for a post
     */
    public function getByPost($postId, $includeUnapproved = false)
    {
        $sql = "SELECT c.*, u.name as user_name 
                FROM comments c 
                LEFT JOIN users u ON c.user_id = u.id 
                WHERE c.post_id = :post_id";
        
        if (!$includeUnapproved) {
            $sql .= " AND c.status = 'approved'";
        }
        
        $sql .= " ORDER BY c.created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['post_id' => $postId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create new comment
     */
    public function create($postId, $userId, $content, $parentId = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO comments (post_id, user_id, parent_id, content, status) 
            VALUES (:post_id, :user_id, :parent_id, :content, 'pending')
        ");
        
        return $stmt->execute([
            'post_id' => $postId,
            'user_id' => $userId,
            'parent_id' => $parentId,
            'content' => $content
        ]);
    }

    /**
     * Update comment status
     */
    public function updateStatus($commentId, $status)
    {
        $stmt = $this->db->prepare("UPDATE comments SET status = :status WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $commentId]);
    }

    /**
     * Delete comment
     */
    public function delete($commentId)
    {
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id = :id");
        return $stmt->execute(['id' => $commentId]);
    }

    /**
     * Get pending comments for admin
     */
    public function getPending($limit = 50)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as user_name, p.title as post_title 
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id 
            LEFT JOIN posts p ON c.post_id = p.id 
            WHERE c.status = 'pending' 
            ORDER BY c.created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}