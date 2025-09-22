<?php
// filepath: c:\wamp64\www\cpsproject\models\PostModel.php
namespace models;

use core\Model;
use core\Database;
use PDO;

class PostModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all published posts with pagination
     */
    public function getAll($filters = [], $limit = 12, $offset = 0)
    {
        $sql = "SELECT p.*, u.name as author_name FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.status = 'published'";
        $params = [];

        // Apply filters
        if (!empty($filters['category'])) {
            $sql .= " AND p.category = :category";
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE :search OR p.body LIKE :search OR p.excerpt LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['featured'])) {
            $sql .= " AND p.featured = 1";
        }

        $sql .= " ORDER BY p.featured DESC, p.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Decode JSON tags
        foreach ($posts as &$post) {
            if ($post['tags']) {
                $post['tags'] = json_decode($post['tags'], true);
            }
        }

        return $posts;
    }

    /**
     * Get post count with filters
     */
    public function getCount($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM posts p WHERE p.status = 'published'";
        $params = [];

        if (!empty($filters['category'])) {
            $sql .= " AND p.category = :category";
            $params['category'] = $filters['category'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE :search OR p.body LIKE :search OR p.excerpt LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['featured'])) {
            $sql .= " AND p.featured = 1";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /**
     * Get post by ID or slug
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.name as author_name 
            FROM posts p 
            LEFT JOIN users u ON p.user_id = u.id 
            WHERE p.id = :id OR p.slug = :slug
        ");
        $stmt->execute(['id' => $id, 'slug' => $id]);
        
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($post && $post['tags']) {
            $post['tags'] = json_decode($post['tags'], true);
        }
        
        return $post;
    }

    /**
     * Create new post
     */
    public function create(array $data)
    {
        // Generate slug from title
        $slug = $this->generateSlug($data['title']);

        $stmt = $this->db->prepare("
            INSERT INTO posts 
            (title, slug, body, excerpt, featured_image, status, category, tags, featured, user_id) 
            VALUES 
            (:title, :slug, :body, :excerpt, :featured_image, :status, :category, :tags, :featured, :user_id)
        ");

        $tagsJson = isset($data['tags']) && is_array($data['tags']) 
                   ? json_encode($data['tags']) 
                   : null;

        $result = $stmt->execute([
            'title' => $data['title'],
            'slug' => $slug,
            'body' => $data['body'],
            'excerpt' => $data['excerpt'] ?? $this->generateExcerpt($data['body']),
            'featured_image' => $data['featured_image'] ?? null,
            'status' => $data['status'] ?? 'draft',
            'category' => $data['category'] ?? null,
            'tags' => $tagsJson,
            'featured' => $data['featured'] ?? false,
            'user_id' => $data['user_id'] ?? null
        ]);

        return $result ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * Update post
     */
    public function update($id, array $data)
    {
        // Generate new slug if title changed
        if (isset($data['title'])) {
            $data['slug'] = $this->generateSlug($data['title'], $id);
        }

        // Generate excerpt if not provided
        if (isset($data['body']) && empty($data['excerpt'])) {
            $data['excerpt'] = $this->generateExcerpt($data['body']);
        }

        $sql = "UPDATE posts SET ";
        $updates = [];
        $params = ['id' => $id];
        
        $allowedFields = ['title', 'slug', 'body', 'excerpt', 'featured_image', 'status', 'category', 'tags', 'featured'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = :$field";
                if ($field === 'tags' && is_array($data[$field])) {
                    $params[$field] = json_encode($data[$field]);
                } else {
                    $params[$field] = $data[$field];
                }
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql .= implode(', ', $updates) . " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Delete post
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Increment view count
     */
    public function incrementViews($id)
    {
        $stmt = $this->db->prepare("UPDATE posts SET views = views + 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get featured posts
     */
    public function getFeatured($limit = 6)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.name as author_name 
            FROM posts p 
            LEFT JOIN users u ON p.user_id = u.id 
            WHERE p.status = 'published' AND p.featured = 1 
            ORDER BY p.created_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($posts as &$post) {
            if ($post['tags']) {
                $post['tags'] = json_decode($post['tags'], true);
            }
        }
        
        return $posts;
    }

    /**
     * Get unique categories
     */
    public function getCategories()
    {
        $stmt = $this->db->query("
            SELECT DISTINCT category 
            FROM posts 
            WHERE status = 'published' AND category IS NOT NULL 
            ORDER BY category
        ");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get posts by user
     */
    public function getByUser($userId, $includeUnpublished = false)
    {
        $sql = "SELECT * FROM posts WHERE user_id = :user_id";
        
        if (!$includeUnpublished) {
            $sql .= " AND status = 'published'";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($posts as &$post) {
            if ($post['tags']) {
                $post['tags'] = json_decode($post['tags'], true);
            }
        }
        
        return $posts;
    }

    /**
     * Admin methods - get all including drafts
     */
    public function getAllForAdmin($limit = 20, $offset = 0, $search = '')
    {
        $sql = "SELECT p.*, u.name as author_name FROM posts p 
                LEFT JOIN users u ON p.user_id = u.id";
        $params = [];

        if ($search) {
            $sql .= " WHERE p.title LIKE :search OR p.body LIKE :search";
            $params['search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generate URL-friendly slug
     */
    private function generateSlug($title, $excludeId = null)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $slug = trim($slug, '-');
        
        // Check if slug exists
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Check if slug exists
     */
    private function slugExists($slug, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM posts WHERE slug = :slug";
        $params = ['slug' => $slug];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Generate excerpt from body
     */
    private function generateExcerpt($body, $length = 200)
    {
        $text = strip_tags($body);
        if (strlen($text) <= $length) {
            return $text;
        }
        
        $excerpt = substr($text, 0, $length);
        $lastSpace = strrpos($excerpt, ' ');
        
        if ($lastSpace !== false) {
            $excerpt = substr($excerpt, 0, $lastSpace);
        }
        
        return $excerpt . '...';
    }
}