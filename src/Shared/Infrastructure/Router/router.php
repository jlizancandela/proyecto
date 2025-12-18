<?php

use Bramus\Router\Router;
use Shared\Infrastructure\Middleware\AuthMiddleware;
use Usuarios\Presentation\UserApiController;
use Usuarios\Presentation\UserController;
use Usuarios\Presentation\ProfileController;
use Usuarios\Presentation\AuthController;
use Shared\Presentation\HomeController;
use Shared\Presentation\AdminController;
use Reservas\Presentation\BookingController;
use Reservas\Presentation\BookingApiController;
use Reservas\Presentation\MyBookingsController;
use Reservas\Presentation\PdfExportController;
use Servicios\Presentation\ServiceApiController;
use Especialistas\Presentation\EspecialistaApiController;

require_once __DIR__ . '/../dependencies.php';

$router = new Router();

$router->before('GET|POST|PUT|DELETE', '/admin/.*', function () {
    AuthMiddleware::requireAdmin();
});

$router->before('GET|POST|PUT|DELETE', '/user/.*', function () {
    AuthMiddleware::requireAuth();
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

$router->get('/admin/users', function () use ($latte, $userService) {
    $controller = new AdminController($latte, $userService);
    echo $controller->usersManagement();
});

$router->get('/user', function () use ($latte, $reservaService) {
    $controller = new UserController($latte, $reservaService);
    echo $controller->index();
});

$router->get('/user/profile', function () use ($latte) {
    $controller = new ProfileController($latte);
    echo $controller->index();
});

$router->get('/user/reservas', function () use ($latte, $reservaService) {
    $controller = new MyBookingsController($latte, $reservaService);
    echo $controller->index();
});

$router->get('/user/reservas/nueva', function () use ($latte) {
    $controller = new BookingController($latte);
    echo $controller->index();
});

$router->post('/user/reservas/cancel/(\d+)', function ($bookingId) use ($latte, $reservaService) {
    $controller = new MyBookingsController($latte, $reservaService);
    $controller->cancel((int)$bookingId);
});

$router->get('/user/reservas/modify/(\d+)', function ($bookingId) use ($latte, $reservaService) {
    $controller = new MyBookingsController($latte, $reservaService);
    $controller->modify((int)$bookingId);
});

$router->get('/user/reservas/pdf', function () use ($latte, $reservaService) {
    $controller = new PdfExportController($latte, $reservaService);
    $controller->exportReservas();
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

$router->get('/api/services', function () use ($servicioRepository) {
    $controller = new ServiceApiController($servicioRepository);
    $controller->getAll();
});

$router->get('/api/especialistas/disponibles', function () use ($especialistaRepository) {
    $controller = new EspecialistaApiController($especialistaRepository);
    $controller->getDisponibles();
});

$router->get('/api/reservas', function () use ($reservaService) {
    $controller = new BookingApiController($reservaService);
    $controller->getReservas();
});

$router->post('/api/reservas', function () use ($reservaService) {
    $controller = new BookingApiController($reservaService);
    $controller->createReserva();
});

$router->get('/api/me', function () use ($latte, $userService) {
    $controller = new UserApiController($latte, $userService);
    $controller->getCurrentUser();
});

$router->run();
