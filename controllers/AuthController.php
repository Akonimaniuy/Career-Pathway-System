<?php
namespace controllers;

use core\Controller;
use core\Session;
use core\CSRF;
use core\Auth;
use models\UserModel;

class AuthController extends Controller
{
    protected $auth;
    protected $userModel;

    public function __construct($params = [])
    {
        parent::__construct($params);
        Session::start();
        $this->auth = new Auth();
        $this->userModel = new UserModel();
    }

    public function login()
    {
        // GET: show form, POST: attempt login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF
            if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
                $this->render('login', ['error' => 'Invalid CSRF token']);
                return;
            }

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = !empty($_POST['remember']);

            if ($this->auth->attempt($email, $password, $remember)) {
                // redirect to home or intended
                header('Location: /cpsproject');
                exit();
            } else {
                $this->render('login', ['error' => 'Invalid credentials or account locked']);
                return;
            }
        }

        $this->render('login');
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST[CSRF::FIELD]) || !CSRF::validate($_POST[CSRF::FIELD])) {
                $this->render('register', ['error' => 'Invalid CSRF token']);
                return;
            }

            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password2 = $_POST['password2'] ?? '';
            $name = trim($_POST['name'] ?? '');

            if ($password !== $password2) {
                $this->render('register', ['error' => 'Passwords do not match']);
                return;
            }

            if ($this->userModel->findByEmail($email)) {
                $this->render('register', ['error' => 'Email already registered']);
                return;
            }

            // basic validation (expand as needed)
            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
                $this->render('register', ['error' => 'Invalid input; password must be >= 8 chars']);
                return;
            }

            $userId = $this->userModel->createUser(['email' => $email, 'password' => $password, 'name' => $name]);

            if ($userId) {
                // Auto-login after registration
                $this->auth->attempt($email, $password, false);
                header('Location: /cpsproject');
                exit();
            } else {
                $this->render('register', ['error' => 'Failed to create user']);
            }
        }

        $this->render('register');
    }

    public function logout()
    {
        $this->auth->logout();
        header('Location: /cpsproject');
        exit();
    }
}