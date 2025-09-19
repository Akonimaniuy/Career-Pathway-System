<?php
// File: controllers/AdminController.php (Extended with pathway management)
namespace controllers;

use core\Controller;
use core\Session;
use core\Auth;
use core\CSRF;
use models\UserModel;
use models\CategoryModel;
use models\PathwayModel;

class AdminController extends Controller
{
    private $userModel;
    private $categoryModel;
    private $pathwayModel;
    protected $auth;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->userModel = new UserModel();
        $this->categoryModel = new CategoryModel();
        $this->pathwayModel = new PathwayModel();

        Session::start();
        $this->auth = new Auth();
        $this->requireAdmin();
    }

    private function requireAdmin()
    {
        if (!$this->auth->check()) {
            header('Location: /cpsproject/login');
            exit();
        }

        $user = $this->auth->user();
        if (!$user || ($user['role'] ?? 'user') !== 'admin') {
            $this->render('access_denied', [
                'title' => 'Access Denied',
                'message' => 'You need admin privileges to access this area.'
            ]);
            exit();
        }
    }

    // Dashboard
    public function index()
    {
        try {
            $stats = $this->getAdminStats();
            $this->render('index', ['title' => 'Admin Dashboard', 'stats' => $stats]);
        } catch (Exception $e) {
            $stats = ['total_users' => 0, 'admin_users' => 0, 'recent_registrations' => 0, 'total_pathways' => 0, 'total_categories' => 0];
            $this->render('index', ['title' => 'Admin Dashboard', 'stats' => $stats]);
        }
    }

    // User Management (existing methods)
    public function users()
    {
        try {
            $users = $this->userModel->getAllUsersWithRole();
            $this->render('users', ['title' => 'Manage Users', 'users' => $users]);
        } catch (Exception $e) {
            $this->render('users', ['title' => 'Manage Users', 'users' => [], 'error' => 'Unable to load users']);
        }
    }

    public function editUser($id)
    {
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            echo "User not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] ?? 'user';

            if (!in_array($role, ['user', 'admin'])) {
                $role = 'user';
            }

            $success = $this->userModel->updateUser($id, [
                'name' => $name,
                'email' => $email,
                'role' => $role
            ]);

            if ($success) {
                header('Location: /cpsproject/admin/users');
                exit();
            } else {
                $error = 'Failed to update user';
            }
        }
        
        $this->render('edit_user', ['title' => 'Edit User', 'user' => $user, 'error' => $error ?? null]);
    }

    public function deleteUser($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/admin/users');
            exit();
        }

        if ($id == $this->auth->id()) {
            header('Location: /cpsproject/admin/users?error=cannot_delete_self');
            exit();
        }

        $success = $this->userModel->deleteUser($id);
        
        if ($success) {
            header('Location: /cpsproject/admin/users?success=user_deleted');
        } else {
            header('Location: /cpsproject/admin/users?error=delete_failed');
        }
        exit();
    }

    // Category Management
    public function categories()
    {
        try {
            $categories = $this->categoryModel->getCategoriesWithPathwayCount();
            $this->render('categories', ['title' => 'Manage Categories', 'categories' => $categories]);
        } catch (Exception $e) {
            $this->render('categories', ['title' => 'Manage Categories', 'categories' => [], 'error' => 'Unable to load categories']);
        }
    }

    public function createCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
                $this->render('create_category', ['error' => 'Invalid CSRF token', 'title' => 'Create Category']);
                return;
            }

            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($name)) {
                $this->render('create_category', ['error' => 'Category name is required', 'title' => 'Create Category']);
                return;
            }

            try {
                $this->categoryModel->createCategory(['name' => $name, 'description' => $description]);
                header('Location: /cpsproject/admin/categories?success=category_created');
                exit();
            } catch (Exception $e) {
                $this->render('create_category', ['error' => 'Failed to create category', 'title' => 'Create Category']);
            }
        }

        $this->render('create_category', ['title' => 'Create Category']);
    }

    public function editCategory($id)
    {
        $category = $this->categoryModel->getCategoryById($id);
        
        if (!$category) {
            echo "Category not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
                $this->render('edit_category', ['error' => 'Invalid CSRF token', 'title' => 'Edit Category', 'category' => $category]);
                return;
            }

            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if (empty($name)) {
                $this->render('edit_category', ['error' => 'Category name is required', 'title' => 'Edit Category', 'category' => $category]);
                return;
            }

            try {
                $this->categoryModel->updateCategory($id, ['name' => $name, 'description' => $description]);
                header('Location: /cpsproject/admin/categories?success=category_updated');
                exit();
            } catch (Exception $e) {
                $this->render('edit_category', ['error' => 'Failed to update category', 'title' => 'Edit Category', 'category' => $category]);
            }
        }
        
        $this->render('edit_category', ['title' => 'Edit Category', 'category' => $category]);
    }

    public function deleteCategory($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/admin/categories');
            exit();
        }

        try {
            $this->categoryModel->deleteCategory($id);
            header('Location: /cpsproject/admin/categories?success=category_deleted');
        } catch (Exception $e) {
            header('Location: /cpsproject/admin/categories?error=delete_failed');
        }
        exit();
    }

    // Pathway Management
    public function pathways()
    {
        try {
            $pathways = $this->pathwayModel->getAllPathways();
            $this->render('pathways', ['title' => 'Manage Pathways', 'pathways' => $pathways]);
        } catch (Exception $e) {
            $this->render('pathways', ['title' => 'Manage Pathways', 'pathways' => [], 'error' => 'Unable to load pathways']);
        }
    }

    public function createPathway()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
                $categories = $this->categoryModel->getAllCategories();
                $this->render('create_pathway', ['error' => 'Invalid CSRF token', 'title' => 'Create Pathway', 'categories' => $categories]);
                return;
            }

            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $categoryId = $_POST['category_id'] ?? '';
            $imageUrl = trim($_POST['image_url'] ?? '');

            if (empty($name) || empty($categoryId)) {
                $categories = $this->categoryModel->getAllCategories();
                $this->render('create_pathway', ['error' => 'Name and category are required', 'title' => 'Create Pathway', 'categories' => $categories]);
                return;
            }

            try {
                $this->pathwayModel->createPathway([
                    'name' => $name, 
                    'description' => $description, 
                    'category_id' => $categoryId,
                    'image_url' => $imageUrl
                ]);
                header('Location: /cpsproject/admin/pathways?success=pathway_created');
                exit();
            } catch (Exception $e) {
                $categories = $this->categoryModel->getAllCategories();
                $this->render('create_pathway', ['error' => 'Failed to create pathway', 'title' => 'Create Pathway', 'categories' => $categories]);
            }
        }

        $categories = $this->categoryModel->getAllCategories();
        $this->render('create_pathway', ['title' => 'Create Pathway', 'categories' => $categories]);
    }

    public function editPathway($id)
    {
        $pathway = $this->pathwayModel->getPathwayById($id);
        
        if (!$pathway) {
            echo "Pathway not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
                $categories = $this->categoryModel->getAllCategories();
                $this->render('edit_pathway', ['error' => 'Invalid CSRF token', 'title' => 'Edit Pathway', 'pathway' => $pathway, 'categories' => $categories]);
                return;
            }

            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $categoryId = $_POST['category_id'] ?? '';
            $imageUrl = trim($_POST['image_url'] ?? '');

            if (empty($name) || empty($categoryId)) {
                $categories = $this->categoryModel->getAllCategories();
                $this->render('edit_pathway', ['error' => 'Name and category are required', 'title' => 'Edit Pathway', 'pathway' => $pathway, 'categories' => $categories]);
                return;
            }

            try {
                $this->pathwayModel->updatePathway($id, [
                    'name' => $name, 
                    'description' => $description, 
                    'category_id' => $categoryId,
                    'image_url' => $imageUrl
                ]);
                header('Location: /cpsproject/admin/pathways?success=pathway_updated');
                exit();
            } catch (Exception $e) {
                $categories = $this->categoryModel->getAllCategories();
                $this->render('edit_pathway', ['error' => 'Failed to update pathway', 'title' => 'Edit Pathway', 'pathway' => $pathway, 'categories' => $categories]);
            }
        }
        
        $categories = $this->categoryModel->getAllCategories();
        $this->render('edit_pathway', ['title' => 'Edit Pathway', 'pathway' => $pathway, 'categories' => $categories]);
    }

    public function deletePathway($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/admin/pathways');
            exit();
        }

        try {
            $this->pathwayModel->deletePathway($id);
            header('Location: /cpsproject/admin/pathways?success=pathway_deleted');
        } catch (Exception $e) {
            header('Location: /cpsproject/admin/pathways?error=delete_failed');
        }
        exit();
    }

    // AJAX endpoint for getting pathways by category
    public function getPathwaysByCategory($categoryId)
    {
        header('Content-Type: application/json');
        try {
            $pathways = $this->pathwayModel->getPathwaysByCategory($categoryId);
            echo json_encode(['success' => true, 'pathways' => $pathways]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Failed to load pathways']);
        }
        exit();
    }

    private function getAdminStats()
    {
        try {
            return [
                'total_users' => $this->userModel->getUserCount(),
                'admin_users' => $this->userModel->getAdminCount(),
                'recent_registrations' => $this->userModel->getRecentRegistrations(7),
                'total_pathways' => $this->pathwayModel->getPathwayStats(),
                'total_categories' => count($this->categoryModel->getAllCategories())
            ];
        } catch (Exception $e) {
            return [
                'total_users' => 0,
                'admin_users' => 0,
                'recent_registrations' => 0,
                'total_pathways' => 0,
                'total_categories' => 0
            ];
        }
    }
}