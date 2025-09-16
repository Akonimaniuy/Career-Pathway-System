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

// Define routes (without 'cpsproject' prefix)
$router->add('', ['controller' => 'HomeController', 'action' => 'index']);
$router->add('users', ['controller' => 'UserController', 'action' => 'index']);
$router->add('user/{id:\d+}', ['controller' => 'UserController', 'action' => 'show']);
$router->add('about', ['controller' => 'HomeController', 'action' => 'about']);
$router->add('pathway', ['controller' => 'HomeController', 'action' => 'Pathway']);
$router->add('assessment', ['controller' => 'HomeController', 'action' => 'Assessment']);
$router->add('login', ['controller' => 'AuthController', 'action' => 'login']);
$router->add('register', ['controller' => 'AuthController', 'action' => 'register']);
$router->add('logout', ['controller' => 'AuthController', 'action' => 'logout']);

// Dispatch the request
$router->dispatch($requestUri);