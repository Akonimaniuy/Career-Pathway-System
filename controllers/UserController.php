<?php
namespace controllers;

use core\Controller;
use models\UserModel;
use core\Session;
use core\Auth;

class UserController extends Controller
{
    private $userModel;
    protected $auth;

    public function __construct($params = [])
    {
        parent::__construct($params);
        $this->userModel = new UserModel();

        // ensure session started (index.php should already start it; this is safe)
        Session::start();

        // create Auth and require login for every action in this controller
        $this->auth = new Auth();
        $this->auth->requireAuth(); // redirect to /cpsproject/login if not logged in
    }

    public function index()
    {
        $users = $this->userModel->getAllUsers();
        
        $data = [
            'title' => 'Users',
            'users' => $users
        ];
        
        $this->render('index', $data);
    }

    public function show($id)
    {
        $user = $this->userModel->getUserById($id);
        
        if ($user) {
            $data = [
                'title' => 'User Details',
                'user' => $user
            ];
            
            $this->render('show', $data);
        } else {
            echo "User not found";
        }
    }
}