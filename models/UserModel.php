<?php
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
        $stmt = $this->db->query("SELECT id, name, email FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT id, name, email, created_at FROM users WHERE id = :id LIMIT 1");
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
        // required: email, password, name (optional)
        $password = $data['password'];
        $hash = password_hash($password, AUTH_PWD_ALGO, AUTH_PWD_OPTIONS);

        $stmt = $this->db->prepare("INSERT INTO users (name, email, password_hash, created_at) VALUES (:name, :email, :ph, NOW())");
        $stmt->execute([
            'name' => $data['name'] ?? null,
            'email' => $data['email'],
            'ph' => $hash
        ]);
        return (int)$this->db->lastInsertId();
    }
}