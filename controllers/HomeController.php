<?php
// filepath: c:\wamp64\www\cpsproject\controllers\HomeController.php
namespace controllers;

use core\Controller;
use core\Session;
use core\Auth;
use models\CareerPathModel;
use models\PostModel;

class HomeController extends Controller
{
    protected $auth;

    public function __construct($params = [])
    {
        parent::__construct($params);
        Session::start();
        $this->auth = new Auth();
    }

    /**
     * Home page with featured content
     */
    public function index()
    {
        $careerPathModel = new CareerPathModel();
        $postModel = new PostModel();

        // Get featured content
        $featuredCareerPaths = $careerPathModel->getFeatured(6);
        $featuredPosts = $postModel->getFeatured(3);

        // Get user's career interests if logged in
        $userCareerInterests = [];
        if ($this->auth->check()) {
            $userModel = new \models\UserModel();
            $userCareerInterests = $userModel->getUserCareerInterests($this->auth->id());
        }

        $this->render('index', [
            'title' => 'Welcome to Career Path System',
            'featuredCareerPaths' => $featuredCareerPaths,
            'featuredPosts' => $featuredPosts,
            'userCareerInterests' => $userCareerInterests,
            'isLoggedIn' => $this->auth->check()
        ]);
    }

    /**
     * About page
     */
    public function about()
    {
        $this->render('about', [
            'title' => 'About Us'
        ]);
    }

    /**
     * Contact page
     */
    public function contact()
    {
        $this->render('contact', [
            'title' => 'Contact Us'
        ]);
    }
}