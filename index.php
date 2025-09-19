<?php
// File: index.php

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
?>
// Move the HomeController class to its own file: controllers/HomeController.php