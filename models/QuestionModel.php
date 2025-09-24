<?php
// File: models/QuestionModel.php
namespace models;

use core\Model;
use core\Database;
use PDO;
use Exception;

class QuestionModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all questions with pathway and category names.
     */
    public function getAllQuestions()
    {
        $stmt = $this->db->query("
            SELECT 
                q.id, 
                q.question_text, 
                q.difficulty_level, 
                p.name as pathway_name, 
                c.name as category_name,
                q.pathway_id
            FROM assessment_questions q
            JOIN pathways p ON q.pathway_id = p.id
            JOIN categories c ON p.category_id = c.id
            ORDER BY c.name, p.name, q.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get question statistics.
     */
    public function getQuestionStats()
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN difficulty_level = 'easy' THEN 1 ELSE 0 END) as easy,
                SUM(CASE WHEN difficulty_level = 'medium' THEN 1 ELSE 0 END) as medium,
                SUM(CASE WHEN difficulty_level = 'hard' THEN 1 ELSE 0 END) as hard
            FROM assessment_questions
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single question with its options.
     */
    public function getQuestionWithOptions($id)
    {
        $questionStmt = $this->db->prepare("SELECT * FROM assessment_questions WHERE id = :id");
        $questionStmt->execute(['id' => $id]);
        $question = $questionStmt->fetch(PDO::FETCH_ASSOC);

        if ($question) {
            $optionsStmt = $this->db->prepare("SELECT * FROM answer_options WHERE question_id = :question_id ORDER BY id");
            $optionsStmt->execute(['question_id' => $id]);
            $question['options'] = $optionsStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $question;
    }

    /**
     * Create a new question and its options.
     */
    public function createQuestion($data)
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                INSERT INTO assessment_questions (pathway_id, question_text, question_type, difficulty_level)
                VALUES (:pathway_id, :question_text, :question_type, :difficulty_level)
            ");
            $stmt->execute([
                'pathway_id' => $data['pathway_id'],
                'question_text' => $data['question_text'],
                'question_type' => $data['question_type'],
                'difficulty_level' => $data['difficulty_level']
            ]);
            $questionId = $this->db->lastInsertId();

            $optionStmt = $this->db->prepare("
                INSERT INTO answer_options (question_id, option_text, is_correct)
                VALUES (:question_id, :option_text, :is_correct)
            ");
            foreach ($data['options'] as $option) {
                $optionStmt->execute([
                    'question_id' => $questionId,
                    'option_text' => $option['text'],
                    'is_correct' => $option['is_correct'] ? 1 : 0
                ]);
            }

            $this->db->commit();
            return $questionId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing question and its options.
     */
    public function updateQuestion($id, $data)
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("
                UPDATE assessment_questions 
                SET question_text = :question_text, question_type = :question_type, difficulty_level = :difficulty_level
                WHERE id = :id
            ");
            $stmt->execute([
                'id' => $id,
                'question_text' => $data['question_text'],
                'question_type' => $data['question_type'],
                'difficulty_level' => $data['difficulty_level']
            ]);

            // Delete old options and insert new ones
            $deleteStmt = $this->db->prepare("DELETE FROM answer_options WHERE question_id = :question_id");
            $deleteStmt->execute(['question_id' => $id]);

            $optionStmt = $this->db->prepare("
                INSERT INTO answer_options (question_id, option_text, is_correct)
                VALUES (:question_id, :option_text, :is_correct)
            ");
            foreach ($data['options'] as $option) {
                $optionStmt->execute([
                    'question_id' => $id,
                    'option_text' => $option['text'],
                    'is_correct' => $option['is_correct'] ? 1 : 0
                ]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Delete a question and its options.
     */
    public function deleteQuestion($id)
    {
        $stmt = $this->db->prepare("DELETE FROM assessment_questions WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}