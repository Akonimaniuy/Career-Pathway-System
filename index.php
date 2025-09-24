<?php
// File: index.php - Updated to work with your existing core files

// Define constants first (before any includes)
define('APP_PATH', __DIR__);
define('APP_DEBUG', true); // Keep debug mode on
define('DEBUG_SHOW_TOOLBAR', false); // Set this to false to hide the toolbar
define('DEBUG_LOG_PATH', APP_PATH . '/storage/debug.log');
define('DEBUG_ALLOW_IPS', ''); // Empty means allow all IPs in debug mode

// Authentication constants
define('AUTH_USER_TABLE', 'users');
define('AUTH_TOKENS_TABLE', 'auth_tokens');
define('AUTH_SESSION_NAME', 'cps_session');
define('AUTH_REMEMBER_COOKIE', 'cps_remember');
define('AUTH_REMEMBER_TOKEN_BYTES', 32);
define('AUTH_REMEMBER_EXPIRE', 2592000); // 30 days
define('AUTH_COOKIE_SECURE', !empty($_SERVER['HTTPS']));
define('AUTH_COOKIE_SAMESITE', 'Lax');
define('AUTH_COOKIE_LIFETIME', 0);
define('AUTH_PWD_ALGO', PASSWORD_DEFAULT);
define('AUTH_PWD_OPTIONS', []);
define('AUTH_MAX_ATTEMPTS', 5);
define('AUTH_LOCKOUT_MINUTES', 15);

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'config/database.php';

// Initialize debug system
require_once 'core/Debug.php';
\core\Debug::init();
\core\Debug::startOutputBuffer();
 
// Use Composer's autoloader
require_once APP_PATH . '/vendor/autoload.php';

// Create router instance
$router = new core\Router();

// Set router for debug (if debug is enabled)
if (class_exists('\\core\\Debug')) {
    \core\Debug::setRouter($router);
}

// Get the request URI
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$requestUri = parse_url($requestUri, PHP_URL_PATH);

// Remove project folder from URI
$requestUri = str_replace('/cpsproject/', '', $requestUri);
$requestUri = str_replace('/cpsproject', '', $requestUri);
$requestUri = trim($requestUri, '/');

// Define all your routes
$router->add('', ['controller' => 'HomeController', 'action' => 'index']);

// User profile routes
$router->add('user', ['controller' => 'UserController', 'action' => 'index']);
$router->add('user/profile', ['controller' => 'UserController', 'action' => 'profile']);
$router->add('user/skills', ['controller' => 'UserController', 'action' => 'skills']);
$router->add('user/experience', ['controller' => 'UserController', 'action' => 'experience']);
$router->add('user/education', ['controller' => 'UserController', 'action' => 'education']);
$router->add('user/career-interests', ['controller' => 'UserController', 'action' => 'careerInterests']);
$router->add('user/change-password', ['controller' => 'UserController', 'action' => 'changePassword']);

// Static pages
$router->add('about', ['controller' => 'HomeController', 'action' => 'about']);
$router->add('contact', ['controller' => 'HomeController', 'action' => 'contact']);

// Assessment routes
$router->add('assessment', ['controller' => 'AssessmentController', 'action' => 'index']);
$router->add('assessment/start', ['controller' => 'AssessmentController', 'action' => 'start']);
$router->add('assessment/question/{id:\d+}', ['controller' => 'AssessmentController', 'action' => 'question']);
$router->add('assessment/answer/{id:\d+}', ['controller' => 'AssessmentController', 'action' => 'answer']);
$router->add('assessment/results/{id:\d+}', ['controller' => 'AssessmentController', 'action' => 'results']);
$router->add('assessment/pathways/{id:\d+}', ['controller' => 'AssessmentController', 'action' => 'getPathways']);
$router->add('assessment/history', ['controller' => 'AssessmentController', 'action' => 'history']);

// Authentication routes
$router->add('login', ['controller' => 'AuthController', 'action' => 'login']);
$router->add('register', ['controller' => 'AuthController', 'action' => 'register']);
$router->add('logout', ['controller' => 'AuthController', 'action' => 'logout']);

// Profile routes
$router->add('profile', ['controller' => 'ProfileController', 'action' => 'index']);
$router->add('profile/edit', ['controller' => 'ProfileController', 'action' => 'edit']);
$router->add('profile/upload-photo', ['controller' => 'ProfileController', 'action' => 'uploadPhoto']);
$router->add('profile/change-password', ['controller' => 'ProfileController', 'action' => 'changePassword']);

// Career path routes
$router->add('career-paths', ['controller' => 'CareerPathController', 'action' => 'index']);
$router->add('career-path/{id:\d+}', ['controller' => 'CareerPathController', 'action' => 'show']);
$router->add('career-paths/create', ['controller' => 'CareerPathController', 'action' => 'create']);
$router->add('career-path/{id:\d+}/edit', ['controller' => 'CareerPathController', 'action' => 'edit']);
$router->add('career-path/{id:\d+}/delete', ['controller' => 'CareerPathController', 'action' => 'delete']);
$router->add('career-path/toggle-interest', ['controller' => 'CareerPathController', 'action' => 'toggleInterest']);

// Post/Blog routes
$router->add('posts', ['controller' => 'PostController', 'action' => 'index']);
$router->add('post/{id:\d+}', ['controller' => 'PostController', 'action' => 'show']);
$router->add('posts/create', ['controller' => 'PostController', 'action' => 'create']);
$router->add('post/{id:\d+}/edit', ['controller' => 'PostController', 'action' => 'edit']);
$router->add('post/{id:\d+}/delete', ['controller' => 'PostController', 'action' => 'delete']);
$router->add('post/add-comment', ['controller' => 'PostController', 'action' => 'addComment']);

// Upload routes
$router->add('upload/pathway-image', ['controller' => 'UploadController', 'action' => 'pathwayImage']);
$router->add('upload/download-template', ['controller' => 'UploadController', 'action' => 'downloadTemplate']);
$router->add('upload/preview-questions', ['controller' => 'UploadController', 'action' => 'previewQuestions']);

// Admin routes
$router->add('admin', ['controller' => 'AdminController', 'action' => 'index']);
$router->add('admin/users', ['controller' => 'AdminController', 'action' => 'users']);
$router->add('admin/user/{id:\d+}/edit', ['controller' => 'AdminController', 'action' => 'editUser']);
$router->add('admin/user/{id:\d+}/delete', ['controller' => 'AdminController', 'action' => 'deleteUser']);

// Category Management Routes
$router->add('admin/categories', ['controller' => 'AdminController', 'action' => 'categories']);
$router->add('admin/categories/create', ['controller' => 'AdminController', 'action' => 'createCategory']);
$router->add('admin/categories/{id:\d+}/edit', ['controller' => 'AdminController', 'action' => 'editCategory']);
$router->add('admin/categories/{id:\d+}/delete', ['controller' => 'AdminController', 'action' => 'deleteCategory']);

// Pathway Management Routes
$router->add('admin/pathways', ['controller' => 'AdminController', 'action' => 'pathways']);
$router->add('admin/pathways/create', ['controller' => 'AdminController', 'action' => 'createPathway']);
$router->add('admin/pathways/{id:\d+}/edit', ['controller' => 'AdminController', 'action' => 'editPathway']);
$router->add('admin/pathways/{id:\d+}/delete', ['controller' => 'AdminController', 'action' => 'deletePathway']);

// Question Management Routes
$router->add('admin/questions', ['controller' => 'AdminController', 'action' => 'questions']);
$router->add('admin/questions/create', ['controller' => 'AdminController', 'action' => 'createQuestion']);
$router->add('admin/questions/{id:\d+}/edit', ['controller' => 'AdminController', 'action' => 'editQuestion']);
$router->add('admin/questions/bulk-import', ['controller' => 'AdminController', 'action' => 'bulkImport']);
$router->add('admin/questions/download-template', ['controller' => 'UploadController', 'action' => 'downloadTemplate']);
$router->add('admin/questions/{id:\d+}/delete', ['controller' => 'AdminController', 'action' => 'deleteQuestion']);

// AJAX endpoint for pathways by category
$router->add('admin/pathways-by-category/{id:\d+}', ['controller' => 'AdminController', 'action' => 'getPathwaysByCategory']);

try {
    // Dispatch the request
    $router->dispatch($requestUri);
} catch (Exception $e) {
    // Log the error
    if (class_exists('\\core\\Debug')) {
        \core\Debug::log('Application Exception: ' . $e->getMessage(), ['exception' => $e]);
    }
    
    error_log("Application Error: " . $e->getMessage());
    
    if (APP_DEBUG) {
        echo "<h2>Application Error</h2>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo "<h2>Something went wrong</h2>";
        echo "<p>Please try again later.</p>";
    }
} catch (Error $e) {
    // Fatal error handling
    if (class_exists('\\core\\Debug')) {
        \core\Debug::log('Fatal Error: ' . $e->getMessage(), ['error' => $e]);
    }
    
    error_log("Fatal Error: " . $e->getMessage());
    
    if (APP_DEBUG) {
        echo "<h2>Fatal Error</h2>";
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo "<h2>System Error</h2>";
        echo "<p>Please contact the administrator.</p>";
    }
}