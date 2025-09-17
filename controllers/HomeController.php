<?php
namespace controllers;


use core\Controller;
use core\Session;
use core\Auth;
use core\Debug;


class HomeController extends Controller
{

    protected $auth;

    public function __construct($params = [])
    {
        parent::__construct($params);

        // ensure session started (index.php should already start it; this is safe)
        
    }
    
    public function index()
    {
       
        $data = [
            'title' => 'Welcome to wow',
            'message' => 'This is a simple PHP MVC framework without .htaccess'
            
        ];
        
        $this->render('index', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'About HopWeb',
            'message' => 'Learn more about our simple MVC framework'
        ];
        
        $this->render('about', $data);
    }
    public function pathway()
    {
        $data = [
            'title' => 'Explore Pathways',
            'message' => 'Discover career pathways that match your interests and skills. Browse the cards to learn more about each field and explore opportunities.'
        ];
        
        $this->render('pathway', $data);
    }
    
    public function assessment()
    {
        $data = [
            'title' => 'Assessment',
            'message' => 'Take assessments to find suitable career paths'
        ];
        
        $this->render('assessment', $data);
    }
}