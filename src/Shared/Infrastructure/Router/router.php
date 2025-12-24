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
use Reservas\Presentation\BookingAdminApiController;
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

$router->get('/login', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showLogin();
});

$router->post('/login', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->login();
});

$router->get('/register', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showRegister();
});

$router->post('/register', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->register();
});

$router->get('/logout', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->logout();
});

$router->get('/forgot-password', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showForgotPasswordForm();
});

$router->post('/forgot-password', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->sendResetLink();
});

$router->get('/reset-password', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showResetPasswordForm();
});

$router->post('/reset-password', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->resetPassword();
});

$router->get('/reactivate', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showReactivate();
});

$router->post('/reactivate', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->reactivate();
});

$router->get('/admin', function () use ($latte) {
    $controller = new AdminController($latte);
    echo $controller->index();
});

$router->get('/admin/users', function () use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new AdminController($latte, $userService, null, null, $especialistaServicioRepository, $especialistaRepository);
    echo $controller->usersManagement();
});

$router->get('/admin/services', function () use ($latte, $servicioService) {
    $controller = new AdminController($latte, null, $servicioService);
    echo $controller->servicesManagement();
});

$router->get('/admin/bookings', function () use ($latte, $reservaService) {
    $controller = new AdminController($latte, null, null, $reservaService);
    echo $controller->bookingsManagement();
});

$router->get('/user', function () use ($latte, $reservaService) {
    $controller = new UserController($latte, $reservaService);
    echo $controller->index();
});

$router->get('/user/profile', function () use ($latte, $userService) {
    $controller = new ProfileController($latte, $userService);
    echo $controller->index();
});

$router->post('/user/profile/update', function () use ($latte, $userService) {
    $controller = new ProfileController($latte, $userService);
    $controller->update();
});

$router->post('/user/profile/delete', function () use ($latte, $userService) {
    $controller = new ProfileController($latte, $userService);
    $controller->delete();
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

$router->get('/admin/bookings/pdf', function () use ($latte, $reservaService) {
    $controller = new PdfExportController($latte, $reservaService);
    $controller->exportAdminReservas();
});

$router->get('/admin/api/users', function () use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->getAllUsers();
});

// Endpoint para obtener especialistas con id_especialista
$router->get('/admin/api/especialistas', function () use ($especialistaRepository) {
    header('Content-Type: application/json');
    try {
        $especialistas = $especialistaRepository->getAllEspecialistasWithUserData();
        echo json_encode([
            'success' => true,
            'especialistas' => $especialistas
        ], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error al obtener especialistas'
        ], JSON_PRETTY_PRINT);
    }
});

$router->post('/admin/api/users', function () use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->createUser();
});

$router->get('/admin/api/users/(\d+)', function ($id) use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->getUserById((int)$id);
});

$router->post('/admin/api/users/(\d+)', function ($id) use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->updateUser((int)$id);
});

$router->put('/admin/api/users/(\d+)', function ($id) use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->updateUser((int)$id);
});

$router->delete('/admin/api/users/(\d+)', function ($id) use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->deleteUser((int)$id);
});

$router->get('/api/services', function () use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->getAll();
});


$router->get('/admin/api/services', function () use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->getAll();
});
$router->get('/admin/api/services/(\d+)', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->getServiceById((int)$id);
});

$router->post('/admin/api/services', function () use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->createService();
});

$router->put('/admin/api/services/(\d+)', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->updateService((int)$id);
});

$router->delete('/admin/api/services/(\d+)', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->deleteService((int)$id);
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

$router->get('/api/me', function () use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->getCurrentUser();
});

$router->get('/admin/api/reservas', function () use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->getAllBookings();
});

$router->get('/admin/api/reservas/(\d+)', function ($id) use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->getBookingById((int)$id);
});

$router->post('/admin/api/reservas', function () use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->createBooking();
});

$router->put('/admin/api/reservas/(\d+)', function ($id) use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->updateBooking((int)$id);
});

$router->delete('/admin/api/reservas/(\d+)', function ($id) use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->deleteBooking((int)$id);
});

$router->run();
