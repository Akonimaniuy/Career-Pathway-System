<?php
// filepath: c:\wamp64\www\cpsproject\controllers\CareerPathController.php
namespace controllers;

use core\Controller;
use core\Session;
use core\Auth;
use core\CSRF;
use models\CareerPathModel;
use models\UserModel;

class CareerPathController extends Controller
{
    protected $careerPathModel;
    protected $userModel;
    protected $auth;

    public function __construct($params = [])
    {
        parent::__construct($params);
        Session::start();
        $this->careerPathModel = new CareerPathModel();
        $this->userModel = new UserModel();
        $this->auth = new Auth();
    }

    /**
     * List all career paths
     */
    public function index()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $filters = [
            'industry' => $_GET['industry'] ?? '',
            'experience_level' => $_GET['experience_level'] ?? '',
            'remote_friendly' => !empty($_GET['remote_friendly']),
            'search' => trim($_GET['search'] ?? '')
        ];

        $careerPaths = $this->careerPathModel->getAll($filters, $limit, $offset);
        $totalCareerPaths = $this->careerPathModel->getCount($filters);
        $totalPages = ceil($totalCareerPaths / $limit);

        // Get filter options
        $industries = $this->careerPathModel->getIndustries();

        $this->render('index', [
            'title' => 'Career Paths',
            'careerPaths' => $careerPaths,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCareerPaths' => $totalCareerPaths,
            'filters' => $filters,
            'industries' => $industries
        ]);
    }

    /**
     * Show single career path
     */
    public function show($id)
    {
        $careerPath = $this->careerPathModel->getById($id);
        
        if (!$careerPath || $careerPath['status'] !== 'published') {
            echo "Career path not found";
            return;
        }

        // Increment view count
        $this->careerPathModel->incrementViews($id);

        // Check if user has shown interest
        $userInterest = null;
        if ($this->auth->check()) {
            $interests = $this->userModel->getUserCareerInterests($this->auth->id());
            foreach ($interests as $interest) {
                if ($interest['career_path_id'] == $id) {
                    $userInterest = $interest;
                    break;
                }
            }
        }

        $this->render('show', [
            'title' => $careerPath['title'],
            'careerPath' => $careerPath,
            'userInterest' => $userInterest
        ]);
    }

    /**
     * Add/remove career interest
     */
    public function toggleInterest()
    {
        $this->auth->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/career-paths');
            exit;
        }

        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/career-paths');
            exit;
        }

        $careerPathId = (int)($_POST['career_path_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if (!$careerPathId) {
            Session::setFlash('error', 'Invalid career path');
            header('Location: /cpsproject/career-paths');
            exit;
        }

        if ($action === 'add') {
            $interestLevel = $_POST['interest_level'] ?? 'medium';
            $notes = trim($_POST['notes'] ?? '');

            if ($this->userModel->addCareerInterest($this->auth->id(), $careerPathId, $interestLevel, $notes)) {
                Session::setFlash('success', 'Career interest added successfully');
            } else {
                Session::setFlash('error', 'Failed to add career interest');
            }
        } elseif ($action === 'remove') {
            if ($this->userModel->removeCareerInterest($this->auth->id(), $careerPathId)) {
                Session::setFlash('success', 'Career interest removed successfully');
            } else {
                Session::setFlash('error', 'Failed to remove career interest');
            }
        }

        header('Location: /cpsproject/career-path/' . $careerPathId);
        exit;
    }

    /**
     * Admin: Create career path form
     */
    public function create()
    {
        $this->auth->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }

        $this->render('create', [
            'title' => 'Create Career Path'
        ]);
    }

    /**
     * Admin: Store new career path
     */
    public function store()
    {
        $this->auth->requireAdmin();

        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/career-paths/create');
            exit;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'requirements' => trim($_POST['requirements'] ?? ''),
            'salary_range' => trim($_POST['salary_range'] ?? ''),
            'growth_potential' => trim($_POST['growth_potential'] ?? ''),
            'industry' => trim($_POST['industry'] ?? ''),
            'experience_level' => $_POST['experience_level'] ?? 'entry',
            'remote_friendly' => isset($_POST['remote_friendly']),
            'status' => $_POST['status'] ?? 'draft',
            'created_by' => $this->auth->id()
        ];

        // Handle skills (comma-separated to array)
        $skillsInput = trim($_POST['skills_needed'] ?? '');
        if (!empty($skillsInput)) {
            $data['skills_needed'] = array_map('trim', explode(',', $skillsInput));
        }

        // Validation
        if (empty($data['title']) || empty($data['description'])) {
            Session::setFlash('error', 'Title and description are required');
            header('Location: /cpsproject/career-paths/create');
            exit;
        }

        if ($this->careerPathModel->create($data)) {
            Session::setFlash('success', 'Career path created successfully');
            header('Location: /cpsproject/admin/career-paths');
        } else {
            Session::setFlash('error', 'Failed to create career path');
            header('Location: /cpsproject/career-paths/create');
        }
        exit;
    }

    /**
     * Admin: Edit career path form
     */
    public function edit($id)
    {
        $this->auth->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->update($id);
        }

        $careerPath = $this->careerPathModel->getById($id);
        if (!$careerPath) {
            Session::setFlash('error', 'Career path not found');
            header('Location: /cpsproject/admin/career-paths');
            exit;
        }

        $this->render('edit', [
            'title' => 'Edit Career Path',
            'careerPath' => $careerPath
        ]);
    }

    /**
     * Admin: Update career path
     */
    public function update($id)
    {
        $this->auth->requireAdmin();

        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/career-path/' . $id . '/edit');
            exit;
        }

        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'requirements' => trim($_POST['requirements'] ?? ''),
            'salary_range' => trim($_POST['salary_range'] ?? ''),
            'growth_potential' => trim($_POST['growth_potential'] ?? ''),
            'industry' => trim($_POST['industry'] ?? ''),
            'experience_level' => $_POST['experience_level'] ?? 'entry',
            'remote_friendly' => isset($_POST['remote_friendly']),
            'status' => $_POST['status'] ?? 'draft'
        ];

        // Handle skills
        $skillsInput = trim($_POST['skills_needed'] ?? '');
        if (!empty($skillsInput)) {
            $data['skills_needed'] = array_map('trim', explode(',', $skillsInput));
        }

        // Validation
        if (empty($data['title']) || empty($data['description'])) {
            Session::setFlash('error', 'Title and description are required');
            header('Location: /cpsproject/career-path/' . $id . '/edit');
            exit;
        }

        if ($this->careerPathModel->update($id, $data)) {
            Session::setFlash('success', 'Career path updated successfully');
            header('Location: /cpsproject/career-path/' . $id);
        } else {
            Session::setFlash('error', 'Failed to update career path');
            header('Location: /cpsproject/career-path/' . $id . '/edit');
        }
        exit;
    }

    /**
     * Admin: Delete career path
     */
    public function delete($id)
    {
        $this->auth->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/admin/career-paths');
            exit;
        }

        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/admin/career-paths');
            exit;
        }

        if ($this->careerPathModel->delete($id)) {
            Session::setFlash('success', 'Career path deleted successfully');
        } else {
            Session::setFlash('error', 'Failed to delete career path');
        }

        header('Location: /cpsproject/admin/career-paths');
        exit;
    }
}