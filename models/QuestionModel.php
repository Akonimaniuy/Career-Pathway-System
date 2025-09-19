<?php
// File: models/QuestionModel.php (New model for managing assessment questions)
namespace models;

use core\Model;
use core\Database;
use PDO;

class QuestionModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }

    public function getQuestionsByPathway($pathwayId)
    {
        $stmt = $this->db->prepare("
            SELECT q.*, p.name as pathway_name 
            FROM assessment_questions q 
            JOIN pathways p ON q.pathway_id = p.id 
            WHERE q.pathway_id = :pathway_id 
            ORDER BY q.difficulty_level ASC, q.id ASC
        ");
        $stmt->execute(['pathway_id' => $pathwayId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function createQuestion($data)
    {
        $this->db->beginTransaction();
        
        try {
            // Create question
            $stmt = $this->db->prepare("
                INSERT INTO assessment_questions (pathway_id, question_text, question_type, difficulty_level) 
                VALUES (:pathway_id, :question_text, :question_type, :difficulty_level)
            ");
            $stmt->execute([
                'pathway_id' => $data['pathway_id'],
                'question_text' => $data['question_text'],
                'question_type' => $data['question_type'] ?? 'multiple_choice',
                'difficulty_level' => $data['difficulty_level'] ?? 'medium'
            ]);
            
            $questionId = $this->db->lastInsertId();
            
            // Add options if provided
            if (!empty($data['options'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO answer_options (question_id, option_text, is_correct) 
                    VALUES (:question_id, :option_text, :is_correct)
                ");
                
                foreach ($data['options'] as $option) {
                    $stmt->execute([
                        'question_id' => $questionId,
                        'option_text' => $option['text'],
                        'is_correct' => $option['is_correct'] ? 1 : 0
                    ]);
                }
            }
            
            $this->db->commit();
            return $questionId;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function updateQuestion($questionId, $data)
    {
        $this->db->beginTransaction();
        
        try {
            // Update question
            $stmt = $this->db->prepare("
                UPDATE assessment_questions 
                SET question_text = :question_text, 
                    question_type = :question_type, 
                    difficulty_level = :difficulty_level 
                WHERE id = :id
            ");
            $stmt->execute([
                'question_text' => $data['question_text'],
                'question_type' => $data['question_type'],
                'difficulty_level' => $data['difficulty_level'],
                'id' => $questionId
            ]);
            
            // Update options if provided
            if (!empty($data['options'])) {
                // Delete existing options
                $stmt = $this->db->prepare("DELETE FROM answer_options WHERE question_id = :question_id");
                $stmt->execute(['question_id' => $questionId]);
                
                // Add new options
                $stmt = $this->db->prepare("
                    INSERT INTO answer_options (question_id, option_text, is_correct) 
                    VALUES (:question_id, :option_text, :is_correct)
                ");
                
                foreach ($data['options'] as $option) {
                    $stmt->execute([
                        'question_id' => $questionId,
                        'option_text' => $option['text'],
                        'is_correct' => $option['is_correct'] ? 1 : 0
                    ]);
                }
            }
            
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function deleteQuestion($questionId)
    {
        // Options will be deleted automatically due to CASCADE
        $stmt = $this->db->prepare("DELETE FROM assessment_questions WHERE id = :id");
        return $stmt->execute(['id' => $questionId]);
    }

    public function getQuestionStats()
    {
        $stmt = $this->db->query("
            SELECT 
                p.name as pathway_name,
                COUNT(q.id) as question_count,
                AVG(CASE WHEN q.difficulty_level = 'easy' THEN 1 WHEN q.difficulty_level = 'medium' THEN 2 ELSE 3 END) as avg_difficulty
            FROM pathways p 
            LEFT JOIN assessment_questions q ON p.id = q.pathway_id 
            GROUP BY p.id, p.name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}