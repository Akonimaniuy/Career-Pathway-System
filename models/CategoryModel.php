<?php
// File: models/CategoryModel.php
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
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCategory(array $data)
    {
        $stmt = $this->db->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateCategory($id, array $data)
    {
        $stmt = $this->db->prepare("UPDATE categories SET name = :name, description = :description WHERE id = :id");
        return $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'id' => $id
        ]);
    }

    public function deleteCategory($id)
    {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
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
}


// File: models/PathwayModel.php
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

    public function getPathwayById($id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name 
            FROM pathways p 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPathwaysByCategory($categoryId)
    {
        $stmt = $this->db->prepare("SELECT * FROM pathways WHERE category_id = :category_id ORDER BY name ASC");
        $stmt->execute(['category_id' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPathway(array $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO pathways (category_id, name, description, image_url) 
            VALUES (:category_id, :name, :description, :image_url)
        ");
        $stmt->execute([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'image_url' => $data['image_url'] ?? null
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updatePathway($id, array $data)
    {
        $stmt = $this->db->prepare("
            UPDATE pathways 
            SET category_id = :category_id, name = :name, description = :description, image_url = :image_url 
            WHERE id = :id
        ");
        return $stmt->execute([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'image_url' => $data['image_url'] ?? null,
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
        return (int) $result['count'];
    }
}


// File: models/AssessmentModel.php
namespace models;

use core\Model;
use core\Database;
use PDO;

class AssessmentModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }

    // Assessment Sessions
    public function createSession($userId, $categoryId, $selectedPathways)
    {
        $stmt = $this->db->prepare("
            INSERT INTO assessment_sessions (user_id, category_id, selected_pathways) 
            VALUES (:user_id, :category_id, :selected_pathways)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'category_id' => $categoryId,
            'selected_pathways' => json_encode($selectedPathways)
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getSessionById($sessionId)
    {
        $stmt = $this->db->prepare("SELECT * FROM assessment_sessions WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $sessionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSessionStatus($sessionId, $status, $currentQuestionId = null)
    {
        if ($currentQuestionId) {
            $stmt = $this->db->prepare("
                UPDATE assessment_sessions 
                SET status = :status, current_question_id = :current_question_id 
                WHERE id = :id
            ");
            $stmt->execute([
                'status' => $status,
                'current_question_id' => $currentQuestionId,
                'id' => $sessionId
            ]);
        } else {
            $stmt = $this->db->prepare("
                UPDATE assessment_sessions 
                SET status = :status, completed_at = NOW() 
                WHERE id = :id
            ");
            $stmt->execute([
                'status' => $status,
                'id' => $sessionId
            ]);
        }
        return $stmt->rowCount() > 0;
    }

    // Questions and Answers
    public function getNextQuestion($sessionId)
    {
        $session = $this->getSessionById($sessionId);
        if (!$session)
            return null;

        $selectedPathways = json_decode($session['selected_pathways'], true);
        $answeredQuestions = $this->getAnsweredQuestionIds($sessionId);

        // Adaptive logic: Get pathway scores to determine focus
        $pathwayScores = $this->getSessionPathwayScores($sessionId);

        // Determine which pathway to focus on next
        $focusPathway = $this->determineFocusPathway($selectedPathways, $pathwayScores);

        // Build query to get next question
        $placeholders = implode(',', array_fill(0, count($selectedPathways), '?'));
        $excludePlaceholders = '';
        $params = $selectedPathways;

        if (!empty($answeredQuestions)) {
            $excludePlaceholders = ' AND q.id NOT IN (' . implode(',', array_fill(0, count($answeredQuestions), '?')) . ')';
            $params = array_merge($params, $answeredQuestions);
        }

        // Prioritize focus pathway if determined
        if ($focusPathway) {
            $sql = "
                SELECT q.*, p.name as pathway_name 
                FROM assessment_questions q 
                JOIN pathways p ON q.pathway_id = p.id 
                WHERE q.pathway_id = ? $excludePlaceholders 
                ORDER BY q.difficulty_level ASC, RAND() 
                LIMIT 1
            ";
            array_unshift($params, $focusPathway);
        } else {
            $sql = "
                SELECT q.*, p.name as pathway_name 
                FROM assessment_questions q 
                JOIN pathways p ON q.pathway_id = p.id 
                WHERE q.pathway_id IN ($placeholders) $excludePlaceholders 
                ORDER BY q.difficulty_level ASC, RAND() 
                LIMIT 1
            ";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getQuestionWithOptions($questionId)
    {
        $stmt = $this->db->prepare("
            SELECT q.*, p.name as pathway_name 
            FROM assessment_questions q 
            JOIN pathways p ON q.pathway_id = p.id 
            WHERE q.id = :id 
            LIMIT 1
        ");
        $stmt->execute(['id' => $questionId]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($question) {
            $stmt = $this->db->prepare("SELECT * FROM answer_options WHERE question_id = :question_id ORDER BY id ASC");
            $stmt->execute(['question_id' => $questionId]);
            $question['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $question;
    }

    public function saveAnswer($sessionId, $questionId, $selectedOptionId)
    {
        // Check if answer is correct
        $stmt = $this->db->prepare("SELECT is_correct FROM answer_options WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $selectedOptionId]);
        $option = $stmt->fetch(PDO::FETCH_ASSOC);

        $isCorrect = $option ? $option['is_correct'] : false;

        $stmt = $this->db->prepare("
            INSERT INTO user_answers (session_id, question_id, selected_option_id, is_correct) 
            VALUES (:session_id, :question_id, :selected_option_id, :is_correct)
        ");
        return $stmt->execute([
            'session_id' => $sessionId,
            'question_id' => $questionId,
            'selected_option_id' => $selectedOptionId,
            'is_correct' => $isCorrect ? 1 : 0
        ]);
    }

    public function getAnsweredQuestionIds($sessionId)
    {
        $stmt = $this->db->prepare("SELECT question_id FROM user_answers WHERE session_id = :session_id");
        $stmt->execute(['session_id' => $sessionId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'question_id');
    }

    public function getSessionPathwayScores($sessionId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                q.pathway_id,
                COUNT(*) as total_questions,
                SUM(CASE WHEN ua.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers,
                (SUM(CASE WHEN ua.is_correct = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as percentage
            FROM user_answers ua
            JOIN assessment_questions q ON ua.question_id = q.id
            WHERE ua.session_id = :session_id
            GROUP BY q.pathway_id
        ");
        $stmt->execute(['session_id' => $sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function determineFocusPathway($selectedPathways, $pathwayScores)
    {
        if (empty($pathwayScores) || count($pathwayScores) < 2) {
            return null; // Not enough data yet
        }

        // Find pathway with lowest score that needs more questions
        $lowestScore = null;
        $focusPathway = null;

        foreach ($pathwayScores as $score) {
            if ($score['total_questions'] >= 3) { // Only consider if we have enough questions
                if ($lowestScore === null || $score['percentage'] < $lowestScore) {
                    $lowestScore = $score['percentage'];
                    $focusPathway = $score['pathway_id'];
                }
            }
        }

        return $focusPathway;
    }

    public function generateResults($sessionId)
    {
        $session = $this->getSessionById($sessionId);
        if (!$session)
            return false;

        $selectedPathways = json_decode($session['selected_pathways'], true);
        $pathwayScores = $this->getSessionPathwayScores($sessionId);

        // Clear existing results
        $stmt = $this->db->prepare("DELETE FROM assessment_results WHERE session_id = :session_id");
        $stmt->execute(['session_id' => $sessionId]);

        // Insert new results
        foreach ($pathwayScores as $score) {
            $stmt = $this->db->prepare("
                INSERT INTO assessment_results (session_id, pathway_id, correct_answers, total_questions, percentage) 
                VALUES (:session_id, :pathway_id, :correct_answers, :total_questions, :percentage)
            ");
            $stmt->execute([
                'session_id' => $sessionId,
                'pathway_id' => $score['pathway_id'],
                'correct_answers' => $score['correct_answers'],
                'total_questions' => $score['total_questions'],
                'percentage' => $score['percentage']
            ]);
        }

        return true;
    }

    public function getSessionResults($sessionId)
    {
        $stmt = $this->db->prepare("
            SELECT ar.*, p.name as pathway_name, p.description as pathway_description
            FROM assessment_results ar
            JOIN pathways p ON ar.pathway_id = p.id
            WHERE ar.session_id = :session_id
            ORDER BY ar.percentage DESC
        ");
        $stmt->execute(['session_id' => $sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuestionCount($sessionId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM user_answers WHERE session_id = :session_id");
        $stmt->execute(['session_id' => $sessionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['count'];
    }
}
?>