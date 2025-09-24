<?php
// File: controllers/AssessmentController.php
namespace controllers;

use core\Controller;
use core\Session;
use core\Auth;
use core\CSRF;
use models\CategoryModel;
use models\CareerPathModel;
use models\AssessmentModel;
use \Exception;

class AssessmentController extends Controller
{
    private $categoryModel;
    private $pathwayModel;
    private $assessmentModel;
    protected $auth;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->categoryModel = new CategoryModel();
        $this->pathwayModel = new CareerPathModel();
        $this->assessmentModel = new AssessmentModel();

        Session::start();
        $this->auth = new Auth();
        $this->auth->requireAuth(); // Assessment requires login
    }

    // Assessment selection page
    public function index()
    {
        $categories = $this->categoryModel->getAllCategories();
        $this->render('index', [
        // This view is now public. The actual assessment start/questions will be protected.
        // The view file for 'index' needs to be created or updated. Let's assume it's 'assessment/index.php'
            'title' => 'Assessment',
            'message' => 'Select a category and at least 2 pathways to begin your assessment.',
            'categories' => $categories
        ]);
    }

    // Start assessment (handle form submission)
    public function start()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/assessment');
            exit();
        }

        if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
            $categories = $this->categoryModel->getAllCategories();
            $this->render('index', [
                'error' => 'Invalid CSRF token',
                'categories' => $categories,
                'title' => 'Assessment'
            ]);
            return;
        }

        $categoryId = $_POST['category_id'] ?? '';
        $selectedPathways = $_POST['pathways'] ?? [];

        // Validation
        if (empty($categoryId) || empty($selectedPathways) || count($selectedPathways) < 2) {
            $categories = $this->categoryModel->getAllCategories();
            $this->render('index', [
                'error' => 'Please select a category and at least 2 pathways.',
                'categories' => $categories,
                'title' => 'Assessment'
            ]);
            return;
        }

        try {
            // Create assessment session
            $sessionId = $this->assessmentModel->createSession(
                $this->auth->id(),
                $categoryId,
                $selectedPathways
            );

            // Redirect to first question
            header('Location: /cpsproject/assessment/question/' . $sessionId);
            exit();

        } catch (Exception $e) {
            $categories = $this->categoryModel->getAllCategories();
            $this->render('index', [
                'error' => 'Failed to start assessment. Please try again.',
                'categories' => $categories,
                'title' => 'Assessment'
            ]);
        }
    }

    // Show current question
    public function question($sessionId)
    {
        $session = $this->assessmentModel->getSessionById($sessionId);
        
        // Validate session
        if (!$session || $session['user_id'] != $this->auth->id() || $session['status'] !== 'in_progress') {
            header('Location: /cpsproject/assessment?error=invalid_session');
            exit();
        }

        // Get next question
        $question = $this->assessmentModel->getNextQuestion($sessionId);
        
        if (!$question) {
            // No more questions - complete assessment
            $this->completeAssessment($sessionId);
            return;
        }

        // Get question with options
        $questionData = $this->assessmentModel->getQuestionWithOptions($question['id']);
        $questionCount = $this->assessmentModel->getQuestionCount($sessionId);

        $this->render('question', [
            'title' => 'Assessment Question',
            'session' => $session,
            'question' => $questionData,
            'questionNumber' => $questionCount + 1,
            'sessionId' => $sessionId
        ]);
    }

    // Handle answer submission
    public function answer($sessionId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/assessment/question/' . $sessionId);
            exit();
        }

        if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
            header('Location: /cpsproject/assessment/question/' . $sessionId . '?error=invalid_token');
            exit();
        }

        $session = $this->assessmentModel->getSessionById($sessionId);
        
        if (!$session || $session['user_id'] != $this->auth->id()) {
            header('Location: /cpsproject/assessment?error=invalid_session');
            exit();
        }

        $questionId = $_POST['question_id'] ?? '';
        $selectedOption = $_POST['answer'] ?? '';

        if (empty($questionId) || empty($selectedOption)) {
            header('Location: /cpsproject/assessment/question/' . $sessionId . '?error=no_answer');
            exit();
        }

        try {
            // Save the answer
            $this->assessmentModel->saveAnswer($sessionId, $questionId, $selectedOption);
            
            // Check if we should continue or finish
            $questionCount = $this->assessmentModel->getQuestionCount($sessionId);
            $maxQuestions = 15; // Configurable maximum questions per assessment

            if ($questionCount >= $maxQuestions) {
                $this->completeAssessment($sessionId);
                return;
            }

            // Continue to next question
            header('Location: /cpsproject/assessment/question/' . $sessionId);
            exit();

        } catch (Exception $e) {
            header('Location: /cpsproject/assessment/question/' . $sessionId . '?error=save_failed');
            exit();
        }
    }

    // Complete assessment and show results
    private function completeAssessment($sessionId)
    {
        try {
            // Update session status
            $this->assessmentModel->updateSessionStatus($sessionId, 'completed');
            
            // Generate results
            $this->assessmentModel->generateResults($sessionId);
            
            // Redirect to results
            header('Location: /cpsproject/assessment/results/' . $sessionId);
            exit();

        } catch (Exception $e) {
            header('Location: /cpsproject/assessment?error=completion_failed');
            exit();
        }
    }

    // Show assessment results
    public function results($sessionId)
    {
        $session = $this->assessmentModel->getSessionById($sessionId);
        
        if (!$session || $session['user_id'] != $this->auth->id() || $session['status'] !== 'completed') {
            header('Location: /cpsproject/assessment?error=invalid_session');
            exit();
        }

        $results = $this->assessmentModel->getSessionResults($sessionId);
        $totalQuestions = $this->assessmentModel->getQuestionCount($sessionId);

        $this->render('results', [
            'title' => 'Assessment Results',
            'session' => $session,
            'results' => $results,
            'totalQuestions' => $totalQuestions,
            'recommendation' => !empty($results) ? $results[0] : null // Highest scoring pathway
        ]);
    }

    // AJAX endpoint for getting pathways by category
    public function getPathwaysByCategory($categoryId)
    {
        header('Content-Type: application/json');
        
        try {
            $pathways = $this->pathwayModel->getByCategory($categoryId);
            echo json_encode(['success' => true, 'pathways' => $pathways]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to load pathways']);
        }
        exit();
    }

    // User's assessment history
    public function history()
    {
        $userId = $this->auth->id();
        
        // Get user's completed assessments (you can add this method to AssessmentModel)
        // For now, redirect to main assessment page
        header('Location: /cpsproject/assessment');
        exit();
    }
}