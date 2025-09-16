<?php
// Quick CLI smoke tests for the project.
// Run: php .\tests\run_tests.php

require __DIR__ . '/../config/database.php';
require __DIR__ . '/../core/Database.php';
require __DIR__ . '/../core/Router.php';
require __DIR__ . '/../core/Model.php';

require __DIR__ . '/TestRunner.php';

$runner = new TestRunner();

// Test 1: Database connection
echo "Running DB connection test...\n";
try {
    $db = \core\Database::getInstance()->getConnection();
    $runner->assert($db instanceof PDO, 'Database connection established');
} catch (Throwable $e) {
    $runner->assert(false, 'Database connection failed: ' . $e->getMessage());
}

// Test 2: Router match + routes
echo "Running Router test...\n";
$router = new core\Router();
$router->add('foo', ['controller' => 'HomeController', 'action' => 'index']);
$router->add('user/{id:\d+}', ['controller' => 'UserController', 'action' => 'show']);
$matched = $router->match('user/123');
$runner->assert($matched === true, 'Router matches user/123');
$routes = $router->getRoutes();
$runner->assert(is_array($routes) && count($routes) >= 2, 'Router getRoutes returns array');

// Test 3: UserModel basic (if DB present)
echo "Running UserModel test (requires users table)...\n";
try {
    require __DIR__ . '/../models/UserModel.php';
    $um = new models\UserModel();
    $all = $um->getAllUsers(); // returns array (may be empty)
    $runner->assert(is_array($all), 'UserModel::getAllUsers returned array');
} catch (Throwable $e) {
    $runner->assert(false, 'UserModel test error: ' . $e->getMessage());
}

$ok = $runner->summary();
exit($ok ? 0 : 1);