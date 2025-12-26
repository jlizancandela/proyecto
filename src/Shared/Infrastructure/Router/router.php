<?php

use Bramus\Router\Router;
use Shared\Infrastructure\Middleware\AuthMiddleware;
use Usuarios\Presentation\UserApiController;
use Usuarios\Presentation\UserController;
use Usuarios\Presentation\ProfileController;
use Usuarios\Presentation\AuthController;
use Shared\Presentation\HomeController;
use Shared\Presentation\AdminController;
use Shared\Presentation\SpecialistController;
use Reservas\Presentation\BookingController;
use Reservas\Presentation\BookingApiController;
use Reservas\Presentation\BookingAdminApiController;
use Reservas\Presentation\MyBookingsController;
use Reservas\Presentation\PdfExportController;
use Servicios\Presentation\ServiceApiController;
use Especialistas\Presentation\EspecialistaApiController;

require_once __DIR__ . '/../dependencies.php';

$router = new Router();

$router->before('GET|POST|PUT|DELETE', '/admin/api/.*', function () {
    AuthMiddleware::apiRequireAdmin();
});

$router->before('GET|POST|PUT|DELETE', '/admin/.*', function () {
    AuthMiddleware::requireAdmin();
});

$router->before('GET|POST|PUT|DELETE', '/user/.*', function () {
    AuthMiddleware::requireAuth();
});

$router->before('GET|POST|PUT|DELETE', '/specialist/.*', function () {
    AuthMiddleware::requireSpecialist();
});

$router->before('GET|POST|PUT|DELETE', '/api/me', function () {
    AuthMiddleware::apiRequireAuth();
});

$router->before('GET|POST|PUT|DELETE', '/api/reservas.*', function () {
    AuthMiddleware::apiRequireAuth();
});

$router->get('/', function () use ($latte, $emailService) {
    $controller = new HomeController($latte, $emailService);
    echo $controller->index();
});

$router->post('/contacto', function () use ($latte, $emailService) {
    $controller = new HomeController($latte, $emailService);
    $controller->contact();
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

$router->get('/admin/users', function () use ($latte, $userService, $servicioService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new AdminController($latte, $userService, $servicioService, null, $especialistaServicioRepository, $especialistaRepository);
    echo $controller->usersManagement();
});

$router->get('/admin/services', function () use ($latte, $servicioService) {
    $controller = new AdminController($latte, null, $servicioService);
    echo $controller->servicesManagement();
});

$router->get('/admin/bookings', function () use ($latte, $userService, $servicioService, $reservaService, $especialistaRepository) {
    $controller = new AdminController($latte, $userService, $servicioService, $reservaService, null, $especialistaRepository);
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

$router->get('/specialist', function () use ($latte, $especialistaRepository, $reservaRepository) {
    $controller = new SpecialistController($latte, $especialistaRepository, $reservaRepository);
    echo $controller->index();
});

$router->get('/specialist/bookings', function () use ($latte, $especialistaRepository, $reservaRepository) {
    $controller = new SpecialistController($latte, $especialistaRepository, $reservaRepository);
    echo $controller->bookings();
});

$router->get('/specialist/profile', function () use ($latte, $especialistaRepository, $reservaRepository) {
    $controller = new SpecialistController($latte, $especialistaRepository, $reservaRepository);
    echo $controller->profile();
});

$router->get('/admin/bookings/pdf', function () use ($latte, $reservaService) {
    $controller = new PdfExportController($latte, $reservaService);
    $controller->exportAdminReservas();
});

$router->get('/admin/users/pdf', function () use ($latte, $reservaService, $userService) {
    $controller = new PdfExportController($latte, $reservaService, $userService);
    $controller->exportAdminUsers();
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

// Endpoint para estadísticas de ocupación de especialistas
$router->get('/admin/api/stats/specialist-occupancy', function () use ($especialistaRepository, $reservaService) {
    header('Content-Type: application/json');
    try {
        $especialistas = $especialistaRepository->getAllEspecialistasWithUserData();

        $labels = [];
        $data = [];
        $colors = [
            'rgba(255, 99, 132, 0.8)',
            'rgba(54, 162, 235, 0.8)',
            'rgba(255, 206, 86, 0.8)',
            'rgba(75, 192, 192, 0.8)',
            'rgba(153, 102, 255, 0.8)',
            'rgba(255, 159, 64, 0.8)',
            'rgba(199, 199, 199, 0.8)',
            'rgba(83, 102, 255, 0.8)',
            'rgba(255, 99, 255, 0.8)',
            'rgba(99, 255, 132, 0.8)'
        ];

        foreach ($especialistas as $especialista) {
            $labels[] = $especialista['nombre'] . ' ' . $especialista['apellidos'];

            // Count active bookings for this specialist
            $bookings = $reservaService->getAllReservasWithFilters([
                'especialista' => $especialista['id'],
                'estado' => null // All states except we'll filter
            ], 1000, 0);

            // Count only active bookings (not cancelled)
            $activeCount = count(array_filter($bookings, function ($booking) {
                return $booking->estado !== 'Cancelada';
            }));

            $data[] = $activeCount;
        }

        echo json_encode([
            'success' => true,
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels))
        ], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error al obtener estadísticas de ocupación'
        ], JSON_PRETTY_PRINT);
    }
});

// Endpoint para estadísticas de servicios más solicitados
$router->get('/admin/api/stats/popular-services', function () use ($reservaService, $servicioService) {
    header('Content-Type: application/json');
    try {
        $servicios = $servicioService->getAllServices();

        $labels = [];
        $data = [];

        foreach ($servicios as $servicio) {
            $labels[] = $servicio->getNombreServicio();

            // Count bookings for this service
            $bookings = $reservaService->getAllReservasWithFilters([
                'servicio' => $servicio->getIdServicio()
            ], 1000, 0);

            // Count only active bookings (not cancelled)
            $activeCount = count(array_filter($bookings, function ($booking) {
                return $booking->estado !== 'Cancelada';
            }));

            $data[] = $activeCount;
        }

        echo json_encode([
            'success' => true,
            'labels' => $labels,
            'data' => $data
        ], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error al obtener estadísticas de servicios'
        ], JSON_PRETTY_PRINT);
    }
});

// Endpoint para KPIs del día actual
$router->get('/admin/api/stats/today-kpis', function () use ($reservaService) {
    header('Content-Type: application/json');
    try {
        $today = date('Y-m-d');

        // Get today's bookings
        $todayBookings = $reservaService->getAllReservasWithFilters([
            'fecha_desde' => $today,
            'fecha_hasta' => $today
        ], 1000, 0);

        // Count active bookings (not cancelled)
        $activeBookings = array_filter($todayBookings, function ($booking) {
            return $booking->estado !== 'Cancelada';
        });

        $totalBookings = count($activeBookings);

        // Calculate estimated revenue
        $estimatedRevenue = array_reduce($activeBookings, function ($total, $booking) {
            return $total + ($booking->servicio_precio ?? 0);
        }, 0);

        echo json_encode([
            'success' => true,
            'totalBookings' => $totalBookings,
            'estimatedRevenue' => number_format($estimatedRevenue, 2, '.', '')
        ], JSON_PRETTY_PRINT);
    } catch (\Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Error al obtener KPIs del día'
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

$router->post('/admin/api/services/(\d+)/activate', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->activateService((int)$id);
});

$router->post('/admin/api/services/(\d+)/deactivate', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->deactivateService((int)$id);
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
