<?php
// File: controllers/AdminController.php
namespace controllers;

use core\Controller;
use core\Session;
use core\Auth;
use models\UserModel;

class AdminController extends Controller
{
    private $userModel;
    protected $auth;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->userModel = new UserModel();

        // Ensure session started
        Session::start();

        // Create Auth and require admin login for every action in this controller
        $this->auth = new Auth();
        $this->requireAdmin();
    }

    /**
     * Require admin authentication - redirect if not admin
     */
    private function requireAdmin()
    {
        if (!$this->auth->check()) {
            // Not logged in at all
            header('Location: /cpsproject/login');
            exit();
        }

        $user = $this->auth->user();
        if (!$user || ($user['role'] ?? 'user') !== 'admin') {
            // Logged in but not admin - show access denied
            $this->render('access_denied', [
                'title' => 'Access Denied',
                'message' => 'You need admin privileges to access this area.'
            ]);
            exit();
        }
    }

    /**
     * Admin dashboard
     */
    public function index()
    {
        $stats = $this->getAdminStats();
        
        $data = [
            'title' => 'Admin Dashboard',
            'stats' => $stats
        ];
        
        $this->render('index', $data);
    }

    /**
     * Manage all users (admin view)
     */
    public function users()
    {
        $users = $this->userModel->getAllUsersWithRole();
        
        $data = [
            'title' => 'Manage Users',
            'users' => $users
        ];
        
        $this->render('users', $data);
    }

    /**
     * Edit user (admin can edit any user)
     */
    public function editUser($id)
    {
        $user = $this->userModel->getUserById($id);
        
        if (!$user) {
            echo "User not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle form submission
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] ?? 'user';

            // Validate role
            if (!in_array($role, ['user', 'admin'])) {
                $role = 'user';
            }

            // Update user
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
        
        $data = [
            'title' => 'Edit User',
            'user' => $user,
            'error' => $error ?? null
        ];
        
        $this->render('edit_user', $data);
    }

    /**
     * Delete user (admin only)
     */
    public function deleteUser($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cpsproject/admin/users');
            exit();
        }

        // Don't allow admin to delete themselves
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

    /**
     * Get basic admin statistics
     */
    private function getAdminStats()
    {
        return [
            'total_users' => $this->userModel->getUserCount(),
            'admin_users' => $this->userModel->getAdminCount(),
            'recent_registrations' => $this->userModel->getRecentRegistrations(7), // last 7 days
        ];
    }
}