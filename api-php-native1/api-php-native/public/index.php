<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../src/Router.php';
require_once __DIR__ . '/../src/Controllers/UserController.php';

use Src\Router;
use Src\Controllers\UserController;

$router = new Router();
$userController = new UserController();

// ✅ Tambahkan dua route:
$router->add('GET', '/api/v1/users', [$userController, 'index']);
$router->add('GET', '/api/v1/users/{id}', [$userController, 'show']);

// Jalankan router
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$path = '/' . trim(str_replace($base, '', $uri), '/');

$router->dispatch($_SERVER['REQUEST_METHOD'], $path);
