<?php


// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH);

// Autoload classes
spl_autoload_register(function ($className) {
    $className = str_replace('\\', '/', $className);
    // echo "Autoloading class: $className\n"; // Debugging line
    $file = APP_PATH . '/' . $className . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Include configuration
require_once APP_PATH . '/config/database.php';
require_once APP_PATH . '/config/auth.php';

// new debug config + helper
if (file_exists(APP_PATH . '/config/debug.php')) {
    require_once APP_PATH . '/config/debug.php';
}
if (file_exists(APP_PATH . '/core/Debug.php')) {
    require_once APP_PATH . '/core/Debug.php';
    \core\Debug::init();
    \core\Debug::startOutputBuffer();
}

require_once APP_PATH . '/core/Session.php';
require_once APP_PATH . '/core/CSRF.php';
require_once APP_PATH . '/core/Auth.php';

// Start secure session
\core\Session::start();

// initialize router
$router = new core\Router();

// pass router to debug for route display
if (class_exists('\\core\\Debug')) {
    \core\Debug::setRouter($router);
}

// Get the requested URL path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove the base path '/cpsproject' from the request URI
$basePath = '/cpsproject';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}
$requestUri = trim($requestUri, '/');


// File: Add these routes to index.php (append to existing routes)

// Existing routes...
$router->add('', ['controller' => 'HomeController', 'action' => 'index']);
$router->add('users', ['controller' => 'UserController', 'action' => 'index']);
$router->add('user/{id:\d+}', ['controller' => 'UserController', 'action' => 'show']);
$router->add('about', ['controller' => 'HomeController', 'action' => 'about']);
$router->add('pathway', ['controller' => 'HomeController', 'action' => 'pathway']);
$router->add('assessment', ['controller' => 'AssessmentController', 'action' => 'index']);
$router->add('login', ['controller' => 'AuthController', 'action' => 'login']);
$router->add('register', ['controller' => 'AuthController', 'action' => 'register']);
$router->add('logout', ['controller' => 'AuthController', 'action' => 'logout']);

// Admin routes
$router->add('admin', ['controller' => 'AdminController', 'action' => 'index']);
$router->add('admin/users', ['controller' => 'AdminController', 'action' => 'users']);
$router->add('admin/user/{id:\d+}/edit', ['controller' => 'AdminController', 'action' => 'editUser']);
$router->add('admin/user/{id:\d+}/delete', ['controller' => 'AdminController', 'action' => 'deleteUser']);

// NEW PATHWAY ADMIN ROUTES
$router->add('admin/categories', ['controller' => 'AdminController', 'action' => 'categories']);
$router->add('admin/categories/create', ['controller' => 'AdminController', 'action' => 'createCategory']);
$router->add('admin/categories/{id:\d+}/edit', ['controller' => 'AdminController', 'action' => 'editCategory']);
$router->add('admin/categories/{id:\d+}/delete', ['controller' => 'AdminController', 'action' => 'deleteCategory']);

$router->add('admin/pathways', ['controller' => 'AdminController', 'action' => 'pathways']);
$router->add('admin/pathways/create', ['controller' => 'AdminController', 'action' => 'createPathway']);
$router->add('admin/pathways/{id:\d+}/edit', ['controller' => 'AdminController', 'action' => 'editPathway']);
$router->add('admin/pathways/{id:\d+}/delete', ['controller' => 'AdminController', 'action' => 'deletePathway']);

// ASSESSMENT ROUTES
$router->add('assessment/start', ['controller' => 'AssessmentController', 'action' => 'start']);
$router->add('assessment/question/{id:\d+}', ['controller' => 'AssessmentController', 'action' => 'question']);
$router->add('assessment/answer/{id:\d+}', ['controller' => 'AssessmentController', 'action' => 'answer']);
$router->add('assessment/results/{id:\d+}', ['controller' => 'AssessmentController', 'action' => 'results']);
$router->add('assessment/pathways/{id:\d+}', ['controller' => 'AssessmentController', 'action' => 'getPathways']);


// File: Updated routes to add to index.php (add these to your existing routes)

// Existing routes remain the same...

// Add these new admin routes for question management:
$router->add('admin/questions', ['controller' => 'AdminController', 'action' => 'questions']);
$router->add('admin/questions/create', ['controller' => 'AdminController', 'action' => 'createQuestion']);
$router->add('admin/questions/{id:\d+}/edit', ['controller' => 'AdminController', 'action' => 'editQuestion']);
$router->add('admin/questions/{id:\d+}/delete', ['controller' => 'AdminController', 'action' => 'deleteQuestion']);
$router->add('admin/pathways/{id:\d+}/questions', ['controller' => 'AdminController', 'action' => 'pathwayQuestions']);

// Updated admin navbar to include questions link


// The HomeController class has been moved to its own file: controllers/HomeController.php
// Dispatch the request
$router->dispatch($requestUri);