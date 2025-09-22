<?php
// File: models/CategoryModel.php - Complete Version
namespace models;

use core\Model;
use core\Database;
use PDO;

class CategoryModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllCategories()
    {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCategoriesWithPathwayCount()
    {
        $stmt = $this->db->query("
            SELECT c.*, COUNT(p.id) as pathway_count 
            FROM categories c 
            LEFT JOIN pathways p ON c.id = p.category_id 
            GROUP BY c.id 
            ORDER BY c.name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createCategory($data)
    {
        $stmt = $this->db->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description']
        ]);
        return $this->db->lastInsertId();
    }

    public function updateCategory($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE categories SET name = :name, description = :description WHERE id = :id");
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'id' => $id
        ]);
    }

    public function deleteCategory($id)
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
