<?php
// File: models/CareerPathModel.php - Corrected Version
namespace models;

use core\Model;
use core\Database;
use PDO;

class CareerPathModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->query("
            SELECT p.*, c.name as category_name FROM pathways p
            JOIN categories c ON p.category_id = c.id 
            ORDER BY c.name ASC, p.name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($categoryId)
    {
        $stmt = $this->db->prepare("
            SELECT p.* FROM pathways p
            WHERE p.category_id = :category_id 
            ORDER BY p.name ASC
        ");
        $stmt->execute(['category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
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

    public function create($data)
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

    public function update($id, $data)
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

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM pathways WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getCount($filters = [])
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM pathways");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function getIndustries()
    {
        // This is a placeholder.
        return [];
    }

    public function incrementViews($id)
    {
        $stmt = $this->db->prepare("UPDATE pathways SET views = views + 1 WHERE id = :id"); // Note: 'views' column does not exist on pathways table
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get featured career paths
     */
    public function getFeatured($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name 
            FROM pathways p
            JOIN categories c ON p.category_id = c.id
            ORDER BY p.id DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}