<?php
// File: controllers/UploadController.php
namespace controllers;

use core\Controller;
use core\Session;
use core\Auth;
use core\CSRF;
use models\FileUploadModel;
use models\PathwayModel;
use \Exception;

class UploadController extends Controller
{
    private $fileUploadModel;
    protected $auth;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->fileUploadModel = new FileUploadModel();

        Session::start();
        $this->auth = new Auth();
        $this->auth->requireAuth();
    }

    public function pathwayImage()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
            echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
            return;
        }

        if (empty($_FILES['pathway_image']['name'])) {
            echo json_encode(['success' => false, 'error' => 'No file uploaded']);
            return;
        }

        $userId = $this->auth->id();
        $result = $this->fileUploadModel->uploadPathwayImage($_FILES['pathway_image'], $userId);

        echo json_encode($result);
    }

    public function downloadTemplate()
    {
        if (!$this->auth->user() || $this->auth->user()['role'] !== 'admin') {
            http_response_code(403);
            echo "Access denied";
            return;
        }

        $pathwayId = $_GET['pathway_id'] ?? null;
        $result = $this->fileUploadModel->generateExcelTemplate($pathwayId);

        if (!$result['success']) {
            echo "Failed to generate template: " . $result['error'];
            return;
        }

        // Set headers for file download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $result['filename'] . '"');
        header('Content-Length: ' . filesize($result['filepath']));

        readfile($result['filepath']);
        
        // Clean up temporary file
        unlink($result['filepath']);
    }

    public function previewQuestions()
    {
        header('Content-Type: application/json');

        if (!$this->auth->user() || $this->auth->user()['role'] !== 'admin') {
            echo json_encode(['success' => false, 'error' => 'Access denied']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
            echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
            return;
        }

        if (empty($_FILES['template']['name'])) {
            echo json_encode(['success' => false, 'error' => 'No file uploaded']);
            return;
        }

        $pathwayId = $_POST['pathway_id'] ?? null;
        if (!$pathwayId) {
            echo json_encode(['success' => false, 'error' => 'Pathway ID is required']);
            return;
        }

        $userId = $this->auth->id();
        $uploadResult = $this->fileUploadModel->uploadQuestionTemplate($_FILES['template'], $userId);

        if (!$uploadResult['success']) {
            echo json_encode($uploadResult);
            return;
        }

        // Parse the template
        $parseResult = $this->fileUploadModel->parseQuestionTemplate($uploadResult['full_path'], $pathwayId);
        
        // Clean up uploaded file after parsing
        unlink($uploadResult['full_path']);

        echo json_encode($parseResult);
    }
}