<?php
// File: models/FileUploadModel.php
namespace models;

use core\Model;
use core\Database;
use PDO;

class FileUploadModel extends Model
{
    protected $db;
    protected $uploadPath;
    protected $allowedTypes;
    protected $maxFileSize;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance()->getConnection();
        $this->uploadPath = APP_PATH . '/uploads/';
        $this->allowedTypes = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'excel' => ['xlsx', 'xls', 'csv'],
            'document' => ['pdf', 'doc', 'docx']
        ];
        $this->maxFileSize = 10 * 1024 * 1024; // 10MB
        
        // Create upload directories if they don't exist
        $this->createUploadDirectories();
    }

    private function createUploadDirectories()
    {
        $directories = [
            $this->uploadPath,
            $this->uploadPath . 'profiles/',
            $this->uploadPath . 'pathways/',
            $this->uploadPath . 'templates/',
            $this->uploadPath . 'temp/'
        ];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    public function uploadFile($file, $uploadType, $userId)
    {
        if (!$this->validateFile($file, $uploadType)) {
            return ['success' => false, 'error' => 'Invalid file'];
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = $this->generateUniqueFilename($extension);
        $subfolder = $this->getSubfolder($uploadType);
        $filepath = $this->uploadPath . $subfolder . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'error' => 'Failed to upload file'];
        }

        // Save to database
        $fileId = $this->saveFileRecord($userId, $file['name'], $filename, $filepath, $file['size'], $file['type'], $uploadType);

        if (!$fileId) {
            unlink($filepath); // Clean up file if DB save fails
            return ['success' => false, 'error' => 'Failed to save file record'];
        }

        return [
            'success' => true,
            'file_id' => $fileId,
            'filename' => $filename,
            'filepath' => $subfolder . '/' . $filename, // Relative path for storage
            'full_path' => $filepath
        ];
    }

    public function uploadProfilePhoto($file, $userId)
    {
        if (!$this->validateFile($file, 'image')) {
            return ['success' => false, 'error' => 'Invalid image file'];
        }

        // Remove old profile photo if exists
        $this->removeOldProfilePhoto($userId);

        $result = $this->uploadFile($file, 'profile_photo', $userId);
        
        if ($result['success']) {
            // Update user profile photo path
            $stmt = $this->db->prepare("UPDATE users SET profile_photo = :photo WHERE id = :id");
            $stmt->execute(['photo' => $result['filepath'], 'id' => $userId]);
        }

        return $result;
    }

    public function uploadPathwayImage($file, $userId)
    {
        return $this->uploadFile($file, 'pathway_image', $userId);
    }

    public function uploadQuestionTemplate($file, $userId)
    {
        if (!$this->validateFile($file, 'excel')) {
            return ['success' => false, 'error' => 'Invalid Excel file'];
        }

        return $this->uploadFile($file, 'question_template', $userId);
    }

    public function generateExcelTemplate($pathwayId = null)
    {
        require_once APP_PATH . '/vendor/autoload.php'; // Assuming PhpSpreadsheet is installed
        
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set headers
            $headers = [
                'A1' => 'Question Text',
                'B1' => 'Difficulty Level',
                'C1' => 'Option 1',
                'D1' => 'Option 2', 
                'E1' => 'Option 3',
                'F1' => 'Option 4',
                'G1' => 'Correct Answer (1-4)',
                'H1' => 'Question Type'
            ];

            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }

            // Add sample data
            $sheet->setCellValue('A2', 'What is the capital of France?');
            $sheet->setCellValue('B2', 'easy');
            $sheet->setCellValue('C2', 'Paris');
            $sheet->setCellValue('D2', 'London');
            $sheet->setCellValue('E2', 'Berlin');
            $sheet->setCellValue('F2', 'Madrid');
            $sheet->setCellValue('G2', '1');
            $sheet->setCellValue('H2', 'multiple_choice');

            // Auto-size columns
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $filename = 'question_template_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filepath = $this->uploadPath . 'templates/' . $filename;

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($filepath);

            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to generate template: ' . $e->getMessage()
            ];
        }
    }

    public function parseQuestionTemplate($filepath, $pathwayId)
    {
        require_once APP_PATH . '/vendor/autoload.php';
        
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filepath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            
            $questions = [];
            $errors = [];

            for ($row = 2; $row <= $highestRow; $row++) {
                $questionText = trim($sheet->getCell("A$row")->getValue());
                $difficulty = trim($sheet->getCell("B$row")->getValue());
                $option1 = trim($sheet->getCell("C$row")->getValue());
                $option2 = trim($sheet->getCell("D$row")->getValue());
                $option3 = trim($sheet->getCell("E$row")->getValue());
                $option4 = trim($sheet->getCell("F$row")->getValue());
                $correctAnswer = trim($sheet->getCell("G$row")->getValue());
                $questionType = trim($sheet->getCell("H$row")->getValue()) ?: 'multiple_choice';

                // Skip empty rows
                if (empty($questionText)) continue;

                // Validate data
                if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
                    $errors[] = "Row $row: Invalid difficulty level '$difficulty'";
                    continue;
                }

                if (!in_array($correctAnswer, ['1', '2', '3', '4'])) {
                    $errors[] = "Row $row: Correct answer must be 1, 2, 3, or 4";
                    continue;
                }

                if (empty($option1) || empty($option2)) {
                    $errors[] = "Row $row: At least 2 options are required";
                    continue;
                }

                $options = [
                    ['text' => $option1, 'is_correct' => $correctAnswer == '1'],
                    ['text' => $option2, 'is_correct' => $correctAnswer == '2']
                ];

                if (!empty($option3)) {
                    $options[] = ['text' => $option3, 'is_correct' => $correctAnswer == '3'];
                }
                if (!empty($option4)) {
                    $options[] = ['text' => $option4, 'is_correct' => $correctAnswer == '4'];
                }

                $questions[] = [
                    'pathway_id' => $pathwayId,
                    'question_text' => $questionText,
                    'difficulty_level' => $difficulty,
                    'question_type' => $questionType,
                    'options' => $options
                ];
            }

            return [
                'success' => true,
                'questions' => $questions,
                'errors' => $errors,
                'total_questions' => count($questions)
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to parse template: ' . $e->getMessage()
            ];
        }
    }

    private function validateFile($file, $type)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if ($file['size'] > $this->maxFileSize) {
            return false;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if ($type === 'image' && !in_array($extension, $this->allowedTypes['image'])) {
            return false;
        }

        if ($type === 'excel' && !in_array($extension, $this->allowedTypes['excel'])) {
            return false;
        }

        return true;
    }

    private function generateUniqueFilename($extension)
    {
        return uniqid() . '_' . time() . '.' . $extension;
    }

    private function getSubfolder($uploadType)
    {
        $subfolders = [
            'profile_photo' => 'profiles',
            'pathway_image' => 'pathways',
            'question_template' => 'templates'
        ];

        return $subfolders[$uploadType] ?? 'temp';
    }

    private function saveFileRecord($userId, $originalFilename, $storedFilename, $filepath, $fileSize, $mimeType, $uploadType)
    {
        $stmt = $this->db->prepare("
            INSERT INTO file_uploads (user_id, original_filename, stored_filename, file_path, file_size, mime_type, upload_type) 
            VALUES (:user_id, :original_filename, :stored_filename, :file_path, :file_size, :mime_type, :upload_type)
        ");

        $success = $stmt->execute([
            'user_id' => $userId,
            'original_filename' => $originalFilename,
            'stored_filename' => $storedFilename,
            'file_path' => $filepath,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'upload_type' => $uploadType
        ]);

        return $success ? $this->db->lastInsertId() : false;
    }

    private function removeOldProfilePhoto($userId)
    {
        $stmt = $this->db->prepare("SELECT profile_photo FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['profile_photo']) {
            $oldPath = $this->uploadPath . $user['profile_photo'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
    }

    public function getFileInfo($fileId)
    {
        $stmt = $this->db->prepare("SELECT * FROM file_uploads WHERE id = :id");
        $stmt->execute(['id' => $fileId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteFile($fileId)
    {
        $file = $this->getFileInfo($fileId);
        if (!$file) return false;

        // Delete physical file
        if (file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }

        // Delete database record
        $stmt = $this->db->prepare("DELETE FROM file_uploads WHERE id = :id");
        return $stmt->execute(['id' => $fileId]);
    }
}