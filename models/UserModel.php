<?php
// File: models/UserModel.php (Updated with admin methods)
namespace models;

use core\Model;
use core\Database;
use PDO;

class UserModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllUsers()
    {
        $stmt = $this->db->query("SELECT id, name, email FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsersWithRole()
    {
        $stmt = $this->db->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser(array $data)
    {
        // required: email, password, name (optional), role (optional)
        $password = $data['password'];
        $hash = password_hash($password, AUTH_PWD_ALGO, AUTH_PWD_OPTIONS);

        $stmt = $this->db->prepare("INSERT INTO users (name, email, role, password_hash, created_at) VALUES (:name, :email, :role, :ph, NOW())");
        $stmt->execute([
            'name' => $data['name'] ?? null,
            'email' => $data['email'],
            'role' => $data['role'] ?? 'user',
            'ph' => $hash
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updateUser($id, array $data)
    {
        $stmt = $this->db->prepare("UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id");
        return $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'id' => $id
        ]);
    }

    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getUserCount()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    public function getAdminCount()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }

    public function getRecentRegistrations($days = 7)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)");
        $stmt->execute(['days' => $days]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }
}