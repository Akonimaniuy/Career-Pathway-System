<?php
// File: models/AssessmentModel.php (Updated for existing database)
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

    // Create assessment session with selected pathways as JSON
    public function createSession($userId, $categoryId, $pathwayIds)
    {
        $stmt = $this->db->prepare("
            INSERT INTO assessment_sessions (user_id, category_id, selected_pathways, status) 
            VALUES (:user_id, :category_id, :selected_pathways, 'in_progress')
        ");
        
        $stmt->execute([
            'user_id' => $userId,
            'category_id' => $categoryId,
            'selected_pathways' => json_encode($pathwayIds)
        ]);
        
        return $this->db->lastInsertId();
    }

    public function getSessionById($sessionId)
    {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as category_name 
            FROM assessment_sessions s 
            JOIN categories c ON s.category_id = c.id 
            WHERE s.id = :id
        ");
        $stmt->execute(['id' => $sessionId]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($session && $session['selected_pathways']) {
            $session['selected_pathways'] = json_decode($session['selected_pathways'], true);
        }
        
        return $session;
    }

    // Adaptive assessment logic - get next question based on performance
    public function getNextQuestion($sessionId)
    {
        $session = $this->getSessionById($sessionId);
        if (!$session) return null;
        
        $pathwayIds = $session['selected_pathways'];
        if (empty($pathwayIds)) return null;
        
        // Get answered questions
        $answeredQuestions = $this->getAnsweredQuestions($sessionId);
        $answeredIds = array_column($answeredQuestions, 'question_id');
        
        // Calculate performance per pathway
        $pathwayPerformance = $this->calculatePathwayPerformance($sessionId, $pathwayIds);
        
        // Determine which pathway to focus on next
        $targetPathwayId = $this->selectTargetPathway($pathwayPerformance, $pathwayIds);
        
        // Get next question from target pathway
        $whereClause = "q.pathway_id = ? AND q.id NOT IN (SELECT question_id FROM user_answers WHERE session_id = ?)";
        $params = [$targetPathwayId, $sessionId];
        
        $stmt = $this->db->prepare("
            SELECT q.*, p.name as pathway_name 
            FROM assessment_questions q 
            JOIN pathways p ON q.pathway_id = p.id 
            WHERE $whereClause
            ORDER BY 
                CASE q.difficulty_level 
                    WHEN 'easy' THEN 1 
                    WHEN 'medium' THEN 2 
                    WHEN 'hard' THEN 3 
                END, 
                RAND() 
            LIMIT 1
        ");
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
        ");
        $stmt->execute(['id' => $questionId]);
        $question = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($question) {
            $stmt = $this->db->prepare("
                SELECT * FROM answer_options 
                WHERE question_id = :question_id 
                ORDER BY id ASC
            ");
            $stmt->execute(['question_id' => $questionId]);
            $question['options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $question;
    }

    public function saveAnswer($sessionId, $questionId, $selectedOptionId)
    {
        // Check if answer is correct
        $stmt = $this->db->prepare("SELECT is_correct FROM answer_options WHERE id = :id");
        $stmt->execute(['id' => $selectedOptionId]);
        $option = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $isCorrect = $option ? $option['is_correct'] : false;
        
        // Save answer
        $stmt = $this->db->prepare("
            INSERT INTO user_answers (session_id, question_id, selected_option_id, is_correct) 
            VALUES (:session_id, :question_id, :selected_option_id, :is_correct)
            ON DUPLICATE KEY UPDATE 
                selected_option_id = VALUES(selected_option_id),
                is_correct = VALUES(is_correct),
                answered_at = CURRENT_TIMESTAMP
        ");
        
        return $stmt->execute([
            'session_id' => $sessionId,
            'question_id' => $questionId,
            'selected_option_id' => $selectedOptionId,
            'is_correct' => $isCorrect
        ]);
    }

    public function getQuestionCount($sessionId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM user_answers WHERE session_id = :id");
        $stmt->execute(['id' => $sessionId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function updateSessionStatus($sessionId, $status)
    {
        $stmt = $this->db->prepare("
            UPDATE assessment_sessions 
            SET status = :status, completed_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        return $stmt->execute(['status' => $status, 'id' => $sessionId]);
    }

    public function generateResults($sessionId)
    {
        $session = $this->getSessionById($sessionId);
        if (!$session) return false;
        
        $pathwayIds = $session['selected_pathways'];
        
        foreach ($pathwayIds as $pathwayId) {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_questions,
                    SUM(CASE WHEN ua.is_correct = 1 THEN 1 ELSE 0 END) as correct_answers
                FROM user_answers ua
                JOIN assessment_questions q ON ua.question_id = q.id
                WHERE ua.session_id = :session_id AND q.pathway_id = :pathway_id
            ");
            $stmt->execute([
                'session_id' => $sessionId,
                'pathway_id' => $pathwayId
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $totalQuestions = $result['total_questions'];
            $correctAnswers = $result['correct_answers'];
            $percentage = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;
            
            // Save result
            $stmt = $this->db->prepare("
                INSERT INTO assessment_results (session_id, pathway_id, total_questions, correct_answers, percentage) 
                VALUES (:session_id, :pathway_id, :total_questions, :correct_answers, :percentage)
                ON DUPLICATE KEY UPDATE
                    total_questions = VALUES(total_questions),
                    correct_answers = VALUES(correct_answers),
                    percentage = VALUES(percentage)
            ");
            $stmt->execute([
                'session_id' => $sessionId,
                'pathway_id' => $pathwayId,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'percentage' => $percentage
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
            ORDER BY ar.percentage DESC, ar.correct_answers DESC
        ");
        $stmt->execute(['session_id' => $sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Helper methods
    private function getAnsweredQuestions($sessionId)
    {
        $stmt = $this->db->prepare("SELECT question_id, is_correct FROM user_answers WHERE session_id = :session_id ORDER BY answered_at ASC");
        $stmt->execute(['session_id' => $sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function calculatePathwayPerformance($sessionId, $pathwayIds)
    {
        $performance = [];
        
        foreach ($pathwayIds as $pathwayId) {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN ua.is_correct = 1 THEN 1 ELSE 0 END) as correct
                FROM user_answers ua
                JOIN assessment_questions q ON ua.question_id = q.id
                WHERE ua.session_id = :session_id AND q.pathway_id = :pathway_id
            ");
            $stmt->execute([
                'session_id' => $sessionId,
                'pathway_id' => $pathwayId
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $total = $result['total'];
            $correct = $result['correct'];
            
            $performance[$pathwayId] = [
                'total' => $total,
                'correct' => $correct,
                'percentage' => $total > 0 ? ($correct / $total) * 100 : 0,
                'wrong_streak' => $this->getWrongStreak($sessionId, $pathwayId)
            ];
        }
        
        return $performance;
    }

    private function getWrongStreak($sessionId, $pathwayId)
    {
        $stmt = $this->db->prepare("
            SELECT ua.is_correct
            FROM user_answers ua
            JOIN assessment_questions q ON ua.question_id = q.id
            WHERE ua.session_id = :session_id AND q.pathway_id = :pathway_id
            ORDER BY ua.answered_at DESC
            LIMIT 3
        ");
        $stmt->execute([
            'session_id' => $sessionId,
            'pathway_id' => $pathwayId
        ]);
        
        $recent = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $wrongStreak = 0;
        
        foreach ($recent as $isCorrect) {
            if ($isCorrect == 0) {
                $wrongStreak++;
            } else {
                break;
            }
        }
        
        return $wrongStreak;
    }

    private function selectTargetPathway($performance, $pathwayIds)
    {
        // Adaptive logic: 
        // 1. If a pathway has 3+ wrong in a row, reduce its priority
        // 2. Focus on pathways with fewer questions asked
        // 3. Distribute questions evenly across pathways
        
        $weights = [];
        $totalQuestions = array_sum(array_column($performance, 'total'));
        $avgQuestions = count($pathwayIds) > 0 ? $totalQuestions / count($pathwayIds) : 0;
        
        foreach ($pathwayIds as $pathwayId) {
            $perf = $performance[$pathwayId];
            
            $weight = 100; // base weight
            
            // Reduce weight if performing poorly (wrong streak)
            if ($perf['wrong_streak'] >= 3) {
                $weight *= 0.3;
            }
            
            // Increase weight for pathways with fewer questions
            if ($perf['total'] < $avgQuestions) {
                $weight *= 1.5;
            }
            
            $weights[$pathwayId] = $weight;
        }
        
        // Select pathway with highest weight
        $maxWeight = max($weights);
        $selectedPathways = array_keys($weights, $maxWeight);
        
        return $selectedPathways[0]; // Return first pathway with max weight
    }
}