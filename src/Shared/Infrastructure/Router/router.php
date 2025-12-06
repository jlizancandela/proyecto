<?php

use Bramus\Router\Router;
use Shared\Infrastructure\Middleware\AuthMiddleware;
use Usuarios\Presentation\UserApiController;
use Usuarios\Presentation\AuthController;
use Shared\Presentation\HomeController;
use Shared\Presentation\AdminController;

use Shared\Presentation\UsersManagementController;

require_once __DIR__ . '/../dependencies.php';

$router = new Router();

$router->before('GET|POST|PUT|DELETE', '/admin/.*', function () {
    AuthMiddleware::requireAdmin();
});

$router->get('/', function () use ($latte) {
    $controller = new HomeController($latte);
    echo $controller->index();
});

$router->get('/login', function () use ($latte, $authService) {
    $controller = new AuthController($latte, $authService);
    echo $controller->showLogin();
});

$router->post('/login', function () use ($latte, $authService) {
    $controller = new AuthController($latte, $authService);
    $controller->login();
});

$router->get('/register', function () use ($latte, $authService) {
    $controller = new AuthController($latte, $authService);
    echo $controller->showRegister();
});

$router->post('/register', function () use ($latte, $authService) {
    $controller = new AuthController($latte, $authService);
    $controller->register();
});

$router->get('/logout', function () use ($latte, $authService) {
    $controller = new AuthController($latte, $authService);
    $controller->logout();
});

$router->get('/admin', function () use ($latte) {
    $controller = new AdminController($latte);
    echo $controller->index();
});

$router->get('/admin/users', function () use ($latte) {
    $controller = new UsersManagementController($latte);
    echo $controller->index();
});

$router->get('/admin/api/users', function () use ($latte, $userService) {
    $controller = new UserApiController($latte, $userService);
    $controller->getAllUsers();
});

$router->post('/admin/api/users', function () use ($latte, $userService) {
    $controller = new UserApiController($latte, $userService);
    $controller->createUser();
});

$router->get('/admin/api/users/(\d+)', function ($id) use ($latte, $userService) {
    $controller = new UserApiController($latte, $userService);
    $controller->getUserById((int)$id);
});

$router->put('/admin/api/users/(\d+)', function ($id) use ($latte, $userService) {
    $controller = new UserApiController($latte, $userService);
    $controller->updateUser((int)$id);
});

$router->delete('/admin/api/users/(\d+)', function ($id) use ($latte, $userService) {
    $controller = new UserApiController($latte, $userService);
    $controller->deleteUser((int)$id);
});

$router->run();
