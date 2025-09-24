<?php
// filepath: c:\wamp64\www\cpsproject\models\UserModel.php
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

    /**
     * Create a new user
     */
    public function createUser($name, $email, $password, $role = 'user')
    {
        $hashedPassword = password_hash($password, AUTH_PWD_ALGO);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, role, created_at) 
            VALUES (:name, :email, :password, :role, NOW())
        ");
        
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role
        ]);
        
        return (int)$this->db->lastInsertId();
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get user by ID
     */
    public function getUserById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $data)
    {
        $sql = "UPDATE users SET ";
        $updates = [];
        $params = ['id' => $userId];
        
        $allowedFields = ['name', 'email', 'bio', 'phone', 'location', 'website', 'linkedin', 'github', 'profile_image'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = :$field";
                $params[$field] = $data[$field];
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
     * Update user password
     */
    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, AUTH_PWD_ALGO);
        
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute(['password' => $hashedPassword, 'id' => $userId]);
    }

    /**
     * Get user skills
     */
    public function getUserSkills($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM user_skills 
            WHERE user_id = :user_id 
            ORDER BY proficiency_level DESC, skill_name ASC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add user skill
     */
    public function addSkill($userId, $skillName, $proficiencyLevel = 'beginner', $yearsExperience = 0)
    {
        $stmt = $this->db->prepare("
            INSERT INTO user_skills (user_id, skill_name, proficiency_level, years_experience) 
            VALUES (:user_id, :skill_name, :proficiency_level, :years_experience)
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'skill_name' => $skillName,
            'proficiency_level' => $proficiencyLevel,
            'years_experience' => $yearsExperience
        ]);
    }

    /**
     * Update user skill
     */
    public function updateSkill($skillId, $proficiencyLevel, $yearsExperience)
    {
        $stmt = $this->db->prepare("
            UPDATE user_skills 
            SET proficiency_level = :proficiency_level, years_experience = :years_experience 
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'proficiency_level' => $proficiencyLevel,
            'years_experience' => $yearsExperience,
            'id' => $skillId
        ]);
    }

    /**
     * Delete user skill
     */
    public function deleteSkill($skillId, $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM user_skills WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $skillId, 'user_id' => $userId]);
    }

    /**
     * Get user experiences
     */
    public function getUserExperiences($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM user_experiences 
            WHERE user_id = :user_id 
            ORDER BY is_current DESC, start_date DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add user experience
     */
    public function addExperience($userId, $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO user_experiences 
            (user_id, company, position, description, start_date, end_date, is_current, location) 
            VALUES (:user_id, :company, :position, :description, :start_date, :end_date, :is_current, :location)
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'company' => $data['company'],
            'position' => $data['position'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'is_current' => $data['is_current'] ?? false,
            'location' => $data['location'] ?? null
        ]);
    }

    /**
     * Update user experience
     */
    public function updateExperience($experienceId, $userId, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE user_experiences 
            SET company = :company, position = :position, description = :description, 
                start_date = :start_date, end_date = :end_date, is_current = :is_current, location = :location
            WHERE id = :id AND user_id = :user_id
        ");
        
        return $stmt->execute([
            'company' => $data['company'],
            'position' => $data['position'],
            'description' => $data['description'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'is_current' => $data['is_current'] ?? false,
            'location' => $data['location'] ?? null,
            'id' => $experienceId,
            'user_id' => $userId
        ]);
    }

    /**
     * Delete user experience
     */
    public function deleteExperience($experienceId, $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM user_experiences WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $experienceId, 'user_id' => $userId]);
    }

    /**
     * Get user education
     */
    public function getUserEducation($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM user_education 
            WHERE user_id = :user_id 
            ORDER BY is_current DESC, start_date DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add user education
     */
    public function addEducation($userId, $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO user_education 
            (user_id, institution, degree, field_of_study, start_date, end_date, is_current, grade, description) 
            VALUES (:user_id, :institution, :degree, :field_of_study, :start_date, :end_date, :is_current, :grade, :description)
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'institution' => $data['institution'],
            'degree' => $data['degree'] ?? null,
            'field_of_study' => $data['field_of_study'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'is_current' => $data['is_current'] ?? false,
            'grade' => $data['grade'] ?? null,
            'description' => $data['description'] ?? null
        ]);
    }

    /**
     * Update user education
     */
    public function updateEducation($educationId, $userId, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE user_education 
            SET institution = :institution, degree = :degree, field_of_study = :field_of_study,
                start_date = :start_date, end_date = :end_date, is_current = :is_current,
                grade = :grade, description = :description
            WHERE id = :id AND user_id = :user_id
        ");
        
        return $stmt->execute([
            'institution' => $data['institution'],
            'degree' => $data['degree'] ?? null,
            'field_of_study' => $data['field_of_study'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'is_current' => $data['is_current'] ?? false,
            'grade' => $data['grade'] ?? null,
            'description' => $data['description'] ?? null,
            'id' => $educationId,
            'user_id' => $userId
        ]);
    }

    /**
     * Delete user education
     */
    public function deleteEducation($educationId, $userId)
    {
        $stmt = $this->db->prepare("DELETE FROM user_education WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $educationId, 'user_id' => $userId]);
    }

    /**
     * Get user's career interests
     */
    public function getUserCareerInterests($userId)
    {
        $stmt = $this->db->prepare("
            SELECT uci.*, p.name as title, p.description
            FROM user_career_interests uci
            JOIN pathways p ON uci.career_path_id = p.id
            WHERE uci.user_id = :user_id
            ORDER BY uci.interest_level DESC, p.name ASC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Add career interest
     */
    public function addCareerInterest($userId, $careerPathId, $interestLevel = 'medium', $notes = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO user_career_interests (user_id, career_path_id, interest_level, notes) 
            VALUES (:user_id, :career_path_id, :interest_level, :notes)
            ON DUPLICATE KEY UPDATE interest_level = :interest_level, notes = :notes
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'career_path_id' => $careerPathId,
            'interest_level' => $interestLevel,
            'notes' => $notes
        ]);
    }

    /**
     * Remove career interest
     */
    public function removeCareerInterest($userId, $careerPathId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM user_career_interests 
            WHERE user_id = :user_id AND career_path_id = :career_path_id
        ");
        
        return $stmt->execute([
            'user_id' => $userId,
            'career_path_id' => $careerPathId
        ]);
    }

    /**
     * Get total number of users
     */
    public function getUserCount()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Get total number of admin users
     */
    public function getAdminCount()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    /**
     * Get recent user registrations
     */
    public function getRecentRegistrations($days = 7)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM users 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
        ");
        $stmt->execute(['days' => $days]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function getAllUsersWithRole()
    {
        $stmt = $this->db->query("SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}