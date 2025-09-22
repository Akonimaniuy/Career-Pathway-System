<?php
// File: models/PathwayModel.php - Complete Version
namespace models;

use core\Model;
use core\Database;
use PDO;

class PathwayModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllPathways()
    {
        $stmt = $this->db->query("
            SELECT p.*, c.name as category_name 
            FROM pathways p 
            JOIN categories c ON p.category_id = c.id 
            ORDER BY c.name ASC, p.name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPathwaysByCategory($categoryId)
    {
        $stmt = $this->db->prepare("
            SELECT p.* FROM pathways p 
            WHERE p.category_id = :category_id 
            ORDER BY p.name ASC
        ");
        $stmt->execute(['category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPathwayById($id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name 
            FROM pathways p 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createPathway($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO pathways (category_id, name, description, image_url) 
            VALUES (:category_id, :name, :description, :image_url)
        ");
        $stmt->execute([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'image_url' => $data['image_url']
        ]);
        return $this->db->lastInsertId();
    }

    public function updatePathway($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE pathways 
            SET category_id = :category_id, name = :name, description = :description, image_url = :image_url 
            WHERE id = :id
        ");
        return $stmt->execute([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'image_url' => $data['image_url'],
            'id' => $id
        ]);
    }

    public function deletePathway($id)
    {
        $stmt = $this->db->prepare("DELETE FROM pathways WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getPathwayStats()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM pathways");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}