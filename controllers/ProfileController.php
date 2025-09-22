<?php
// File: controllers/ProfileController.php
namespace controllers;

use core\Controller;
use core\Session;
use core\Auth;
use core\CSRF;
use models\UserModel;
use models\FileUploadModel;
use \Exception;

class ProfileController extends Controller
{
    private $userModel;
    private $fileUploadModel;
    protected $auth;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->userModel = new UserModel();
        $this->fileUploadModel = new FileUploadModel();

        Session::start();
        $this->auth = new Auth();
        $this->auth->requireAuth(); // All profile actions require login
    }

    public function index()
    {
        $userId = $this->auth->id();
        $profile = $this->userModel->getUserProfile($userId);

        if (!$profile) {
            echo "Profile not found";
            return;
        }

        $this->render('index', [
            'title' => 'My Profile',
            'profile' => $profile
        ]);
    }

    public function edit()
    {
        $userId = $this->auth->id();
        $profile = $this->userModel->getUserProfile($userId);

        if (!$profile) {
            echo "Profile not found";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
                $this->render('edit', [
                    'error' => 'Invalid CSRF token',
                    'title' => 'Edit Profile',
                    'profile' => $profile
                ]);
                return;
            }

            $updateData = [
                'name' => trim($_POST['name'] ?? ''),
                'birth_date' => $_POST['birth_date'] ?? null,
                'address' => trim($_POST['address'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'bio' => trim($_POST['bio'] ?? '')
            ];

            // Handle profile photo upload
            if (!empty($_FILES['profile_photo']['name'])) {
                $uploadResult = $this->fileUploadModel->uploadProfilePhoto($_FILES['profile_photo'], $userId);
                
                if (!$uploadResult['success']) {
                    $this->render('edit', [
                        'error' => 'Failed to upload profile photo: ' . $uploadResult['error'],
                        'title' => 'Edit Profile',
                        'profile' => $profile
                    ]);
                    return;
                }
                
                $updateData['profile_photo'] = $uploadResult['filepath'];
            }

            // Update profile
            if ($this->userModel->updateUserProfile($userId, $updateData)) {
                header('Location: /cpsproject/profile?success=profile_updated');
                exit();
            } else {
                $this->render('edit', [
                    'error' => 'Failed to update profile',
                    'title' => 'Edit Profile',
                    'profile' => $profile
                ]);
                return;
            }
        }

        $this->render('edit', [
            'title' => 'Edit Profile',
            'profile' => $profile
        ]);
    }

    public function uploadPhoto()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
            return;
        }

        if (empty($_FILES['photo']['name'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No file uploaded']);
            return;
        }

        $userId = $this->auth->id();
        $uploadResult = $this->fileUploadModel->uploadProfilePhoto($_FILES['photo'], $userId);

        header('Content-Type: application/json');
        echo json_encode($uploadResult);
    }

    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
                $this->render('change_password', [
                    'error' => 'Invalid CSRF token',
                    'title' => 'Change Password'
                ]);
                return;
            }

            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validate input
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                $this->render('change_password', [
                    'error' => 'All fields are required',
                    'title' => 'Change Password'
                ]);
                return;
            }

            if ($newPassword !== $confirmPassword) {
                $this->render('change_password', [
                    'error' => 'New passwords do not match',
                    'title' => 'Change Password'
                ]);
                return;
            }

            if (strlen($newPassword) < 8) {
                $this->render('change_password', [
                    'error' => 'New password must be at least 8 characters long',
                    'title' => 'Change Password'
                ]);
                return;
            }

            // Verify current password
            $userId = $this->auth->id();
            $user = $this->userModel->getUserById($userId);
            
            if (!password_verify($currentPassword, $user['password_hash'])) {
                $this->render('change_password', [
                    'error' => 'Current password is incorrect',
                    'title' => 'Change Password'
                ]);
                return;
            }

            // Update password
            if ($this->userModel->resetUserPassword($userId, $newPassword)) {
                header('Location: /cpsproject/profile?success=password_changed');
                exit();
            } else {
                $this->render('change_password', [
                    'error' => 'Failed to change password',
                    'title' => 'Change Password'
                ]);
                return;
            }
        }

        $this->render('change_password', [
            'title' => 'Change Password'
        ]);
    }
}