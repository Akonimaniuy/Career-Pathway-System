<?php
// filepath: c:\wamp64\www\cpsproject\controllers\UserController.php
namespace controllers;

use core\Controller;
use core\Session;
use core\Auth;
use core\CSRF;
use models\UserModel;

class UserController extends Controller
{
    protected $userModel;
    protected $auth;

    public function __construct($params = [])
    {
        parent::__construct($params);
        Session::start();
        $this->userModel = new UserModel();
        $this->auth = new Auth();
        $this->auth->requireAuth();
    }

    /**
     * User profile dashboard
     */
    public function index()
    {
        $user = $this->auth->user();
        $skills = $this->userModel->getUserSkills($user['id']);
        $experiences = $this->userModel->getUserExperiences($user['id']);
        $education = $this->userModel->getUserEducation($user['id']);
        $careerInterests = $this->userModel->getUserCareerInterests($user['id']);

        $this->render('dashboard', [
            'title' => 'My Dashboard',
            'user' => $user,
            'skills' => $skills,
            'experiences' => $experiences,
            'education' => $education,
            'careerInterests' => $careerInterests
        ]);
    }

    /**
     * Edit profile form
     */
    public function profile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->updateProfile();
        }

        $user = $this->auth->user();
        $this->render('profile', [
            'title' => 'Edit Profile',
            'user' => $user
        ]);
    }

    /**
     * Update profile
     */
    private function updateProfile()
    {
        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/user/profile');
            exit;
        }

        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'bio' => trim($_POST['bio'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'website' => trim($_POST['website'] ?? ''),
            'linkedin' => trim($_POST['linkedin'] ?? ''),
            'github' => trim($_POST['github'] ?? '')
        ];

        // Basic validation
        if (empty($data['name']) || empty($data['email'])) {
            Session::setFlash('error', 'Name and email are required');
            header('Location: /cpsproject/user/profile');
            exit;
        }

        // Check if email is already taken by another user
        $existingUser = $this->userModel->getUserByEmail($data['email']);
        if ($existingUser && $existingUser['id'] !== $this->auth->id()) {
            Session::setFlash('error', 'Email already in use by another account');
            header('Location: /cpsproject/user/profile');
            exit;
        }

        if ($this->userModel->updateProfile($this->auth->id(), $data)) {
            Session::setFlash('success', 'Profile updated successfully');
        } else {
            Session::setFlash('error', 'Failed to update profile');
        }

        header('Location: /cpsproject/user/profile');
        exit;
    }

    /**
     * Skills management
     */
    public function skills()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleSkillAction();
        }

        $skills = $this->userModel->getUserSkills($this->auth->id());
        $this->render('skills', [
            'title' => 'My Skills',
            'skills' => $skills
        ]);
    }

    /**
     * Handle skill actions (add, update, delete)
     */
    private function handleSkillAction()
    {
        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/user/skills');
            exit;
        }

        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'add':
                $skillName = trim($_POST['skill_name'] ?? '');
                $proficiency = $_POST['proficiency_level'] ?? 'beginner';
                $years = max(0, (int)($_POST['years_experience'] ?? 0));

                if (empty($skillName)) {
                    Session::setFlash('error', 'Skill name is required');
                } elseif ($this->userModel->addSkill($this->auth->id(), $skillName, $proficiency, $years)) {
                    Session::setFlash('success', 'Skill added successfully');
                } else {
                    Session::setFlash('error', 'Failed to add skill');
                }
                break;

            case 'update':
                $skillId = (int)($_POST['skill_id'] ?? 0);
                $proficiency = $_POST['proficiency_level'] ?? 'beginner';
                $years = max(0, (int)($_POST['years_experience'] ?? 0));

                if ($this->userModel->updateSkill($skillId, $proficiency, $years)) {
                    Session::setFlash('success', 'Skill updated successfully');
                } else {
                    Session::setFlash('error', 'Failed to update skill');
                }
                break;

            case 'delete':
                $skillId = (int)($_POST['skill_id'] ?? 0);
                
                if ($this->userModel->deleteSkill($skillId, $this->auth->id())) {
                    Session::setFlash('success', 'Skill deleted successfully');
                } else {
                    Session::setFlash('error', 'Failed to delete skill');
                }
                break;
        }

        header('Location: /cpsproject/user/skills');
        exit;
    }

    /**
     * Experience management
     */
    public function experience()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleExperienceAction();
        }

        $experiences = $this->userModel->getUserExperiences($this->auth->id());
        $this->render('experience', [
            'title' => 'Work Experience',
            'experiences' => $experiences
        ]);
    }

    /**
     * Handle experience actions
     */
    private function handleExperienceAction()
    {
        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/user/experience');
            exit;
        }

        $action = $_POST['action'] ?? '';
        
        $data = [
            'company' => trim($_POST['company'] ?? ''),
            'position' => trim($_POST['position'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'is_current' => isset($_POST['is_current']),
            'location' => trim($_POST['location'] ?? '')
        ];

        // Clear end date if current position
        if ($data['is_current']) {
            $data['end_date'] = null;
        }

        switch ($action) {
            case 'add':
                if (empty($data['company']) || empty($data['position'])) {
                    Session::setFlash('error', 'Company and position are required');
                } elseif ($this->userModel->addExperience($this->auth->id(), $data)) {
                    Session::setFlash('success', 'Experience added successfully');
                } else {
                    Session::setFlash('error', 'Failed to add experience');
                }
                break;

            case 'update':
                $experienceId = (int)($_POST['experience_id'] ?? 0);
                
                if (empty($data['company']) || empty($data['position'])) {
                    Session::setFlash('error', 'Company and position are required');
                } elseif ($this->userModel->updateExperience($experienceId, $this->auth->id(), $data)) {
                    Session::setFlash('success', 'Experience updated successfully');
                } else {
                    Session::setFlash('error', 'Failed to update experience');
                }
                break;

            case 'delete':
                $experienceId = (int)($_POST['experience_id'] ?? 0);
                
                if ($this->userModel->deleteExperience($experienceId, $this->auth->id())) {
                    Session::setFlash('success', 'Experience deleted successfully');
                } else {
                    Session::setFlash('error', 'Failed to delete experience');
                }
                break;
        }

        header('Location: /cpsproject/user/experience');
        exit;
    }

    /**
     * Education management
     */
    public function education()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->handleEducationAction();
        }

        $education = $this->userModel->getUserEducation($this->auth->id());
        $this->render('education', [
            'title' => 'Education',
            'education' => $education
        ]);
    }

    /**
     * Handle education actions
     */
    private function handleEducationAction()
    {
        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/user/education');
            exit;
        }

        $action = $_POST['action'] ?? '';
        
        $data = [
            'institution' => trim($_POST['institution'] ?? ''),
            'degree' => trim($_POST['degree'] ?? ''),
            'field_of_study' => trim($_POST['field_of_study'] ?? ''),
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'is_current' => isset($_POST['is_current']),
            'grade' => trim($_POST['grade'] ?? ''),
            'description' => trim($_POST['description'] ?? '')
        ];

        // Clear end date if currently studying
        if ($data['is_current']) {
            $data['end_date'] = null;
        }

        switch ($action) {
            case 'add':
                if (empty($data['institution'])) {
                    Session::setFlash('error', 'Institution name is required');
                } elseif ($this->userModel->addEducation($this->auth->id(), $data)) {
                    Session::setFlash('success', 'Education added successfully');
                } else {
                    Session::setFlash('error', 'Failed to add education');
                }
                break;

            case 'update':
                $educationId = (int)($_POST['education_id'] ?? 0);
                
                if (empty($data['institution'])) {
                    Session::setFlash('error', 'Institution name is required');
                } elseif ($this->userModel->updateEducation($educationId, $this->auth->id(), $data)) {
                    Session::setFlash('success', 'Education updated successfully');
                } else {
                    Session::setFlash('error', 'Failed to update education');
                }
                break;

            case 'delete':
                $educationId = (int)($_POST['education_id'] ?? 0);
                
                if ($this->userModel->deleteEducation($educationId, $this->auth->id())) {
                    Session::setFlash('success', 'Education deleted successfully');
                } else {
                    Session::setFlash('error', 'Failed to delete education');
                }
                break;
        }

        header('Location: /cpsproject/user/education');
        exit;
    }

    /**
     * Career interests
     */
    public function careerInterests()
    {
        $careerInterests = $this->userModel->getUserCareerInterests($this->auth->id());
        
        $this->render('career_interests', [
            'title' => 'Career Interests',
            'careerInterests' => $careerInterests
        ]);
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->updatePassword();
        }

        $this->render('change_password', [
            'title' => 'Change Password'
        ]);
    }

    /**
     * Update password
     */
    private function updatePassword()
    {
        if (!CSRF::validate($_POST[CSRF::FIELD] ?? '')) {
            Session::setFlash('error', 'Invalid CSRF token');
            header('Location: /cpsproject/user/change-password');
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            Session::setFlash('error', 'All fields are required');
            header('Location: /cpsproject/user/change-password');
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            Session::setFlash('error', 'New passwords do not match');
            header('Location: /cpsproject/user/change-password');
            exit;
        }

        if (strlen($newPassword) < 8) {
            Session::setFlash('error', 'Password must be at least 8 characters long');
            header('Location: /cpsproject/user/change-password');
            exit;
        }

        // Verify current password
        $user = $this->userModel->getUserById($this->auth->id());
        if (!password_verify($currentPassword, $user['password'])) {
            Session::setFlash('error', 'Current password is incorrect');
            header('Location: /cpsproject/user/change-password');
            exit;
        }

        // Update password
        if ($this->userModel->updatePassword($this->auth->id(), $newPassword)) {
            Session::setFlash('success', 'Password changed successfully');
        } else {
            Session::setFlash('error', 'Failed to change password');
        }

        header('Location: /cpsproject/user/change-password');
        exit;
    }
}