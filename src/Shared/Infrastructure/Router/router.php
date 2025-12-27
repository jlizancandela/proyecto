<?php

/**
 * Main application router configuration.
 *
 * This file defines all HTTP routes for the application using Bramus Router.
 * Routes are organized by functionality and protected with authentication middleware.
 *
 * Route Groups:
 * - Public Routes: Landing page, contact form
 * - Authentication Routes: Login, register, password reset, reactivation
 * - Admin Routes: User management, service management, booking management
 * - User Routes: Profile, bookings, new booking
 * - Specialist Routes: Dashboard, bookings, profile
 * - PDF Export Routes: Admin reports
 * - Admin API Routes: CRUD operations for users, services, bookings
 * - Public API Routes: Services, specialists, bookings
 * - Stats API Routes: Dashboard statistics and KPIs
 *
 * @package app-reservas
 */

use Bramus\Router\Router;
use Shared\Infrastructure\Middleware\AuthMiddleware;
use Usuarios\Presentation\UserApiController;
use Usuarios\Presentation\UserController;
use Usuarios\Presentation\ProfileController;
use Usuarios\Presentation\AuthController;
use Shared\Presentation\HomeController;
use Shared\Presentation\AdminController;
use Shared\Presentation\SpecialistController;
use Shared\Presentation\StatsApiController;
use Reservas\Presentation\BookingController;
use Reservas\Presentation\BookingApiController;
use Reservas\Presentation\BookingAdminApiController;
use Reservas\Presentation\MyBookingsController;
use Reservas\Presentation\PdfExportController;
use Servicios\Presentation\ServiceApiController;
use Especialistas\Presentation\EspecialistaApiController;

require_once __DIR__ . '/../dependencies.php';

$router = new Router();

// =============================================================================
//  GLOBAL MIDDLEWARE (Applied before route handlers)
// =============================================================================

/**
 * Protect all /admin/api/* routes - Require admin authentication for API
 */
$router->before('GET|POST|PUT|DELETE', '/admin/api/.*', function () {
    AuthMiddleware::apiRequireAdmin();
});

/**
 * Protect all /admin/* routes - Require admin authentication for web pages
 */
$router->before('GET|POST|PUT|DELETE', '/admin/.*', function () {
    AuthMiddleware::requireAdmin();
});

/**
 * Protect all /user/* routes - Require user authentication
 */
$router->before('GET|POST|PUT|DELETE', '/user/.*', function () {
    AuthMiddleware::requireAuth();
});

/**
 * Protect all /specialist/* routes - Require specialist role
 */
$router->before('GET|POST|PUT|DELETE', '/specialist/.*', function () {
    AuthMiddleware::requireSpecialist();
});

/**
 * Protect /api/me endpoint - Require authentication for current user data
 */
$router->before('GET|POST|PUT|DELETE', '/api/me', function () {
    AuthMiddleware::apiRequireAuth();
});

/**
 * Protect /api/reservas* endpoints - Require authentication for bookings API
 */
$router->before('GET|POST|PUT|DELETE', '/api/reservas.*', function () {
    AuthMiddleware::apiRequireAuth();
});

// =============================================================================
//  PUBLIC ROUTES
// =============================================================================

/**
 * GET / - Landing page
 */
$router->get('/', function () use ($latte, $emailService) {
    $controller = new HomeController($latte, $emailService);
    echo $controller->index();
});

/**
 * POST /contacto - Contact form submission
 */
$router->post('/contacto', function () use ($latte, $emailService) {
    $controller = new HomeController($latte, $emailService);
    $controller->contact();
});

// =============================================================================
//  AUTHENTICATION ROUTES
// =============================================================================

/**
 * GET /login - Show login form
 */
$router->get('/login', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showLogin();
});

/**
 * POST /login - Process login
 */
$router->post('/login', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->login();
});

/**
 * GET /register - Show registration form
 */
$router->get('/register', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showRegister();
});

/**
 * POST /register - Process registration
 */
$router->post('/register', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->register();
});

/**
 * GET /logout - Logout user
 */
$router->get('/logout', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->logout();
});

/**
 * GET /forgot-password - Show forgot password form
 */
$router->get('/forgot-password', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showForgotPasswordForm();
});

/**
 * POST /forgot-password - Send password reset link
 */
$router->post('/forgot-password', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->sendResetLink();
});

/**
 * GET /reset-password - Show reset password form
 */
$router->get('/reset-password', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showResetPasswordForm();
});

/**
 * POST /reset-password - Process password reset
 */
$router->post('/reset-password', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->resetPassword();
});

/**
 * GET /reactivate - Show account reactivation form
 */
$router->get('/reactivate', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showReactivate();
});

/**
 * POST /reactivate - Process account reactivation
 */
$router->post('/reactivate', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->reactivate();
});

// =============================================================================
//  ADMIN ROUTES (Web Pages)
// =============================================================================

/**
 * GET /admin - Admin dashboard
 */
$router->get('/admin', function () use ($latte) {
    $controller = new AdminController($latte);
    echo $controller->index();
});

/**
 * GET /admin/users - User management page
 */
$router->get('/admin/users', function () use ($latte, $userService, $servicioService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new AdminController($latte, $userService, $servicioService, null, $especialistaServicioRepository, $especialistaRepository);
    echo $controller->usersManagement();
});

/**
 * GET /admin/services - Service management page
 */
$router->get('/admin/services', function () use ($latte, $servicioService) {
    $controller = new AdminController($latte, null, $servicioService);
    echo $controller->servicesManagement();
});

/**
 * GET /admin/bookings - Booking management page
 */
$router->get('/admin/bookings', function () use ($latte, $userService, $servicioService, $reservaService, $especialistaRepository) {
    $controller = new AdminController($latte, $userService, $servicioService, $reservaService, null, $especialistaRepository);
    echo $controller->bookingsManagement();
});

// =============================================================================
//  USER ROUTES
// =============================================================================

/**
 * GET /user - User dashboard
 */
$router->get('/user', function () use ($latte, $reservaService) {
    $controller = new UserController($latte, $reservaService);
    echo $controller->index();
});

/**
 * GET /user/profile - User profile page
 */
$router->get('/user/profile', function () use ($latte, $userService) {
    $controller = new ProfileController($latte, $userService);
    echo $controller->index();
});

/**
 * POST /user/profile/update - Update user profile
 */
$router->post('/user/profile/update', function () use ($latte, $userService) {
    $controller = new ProfileController($latte, $userService);
    $controller->update();
});

/**
 * POST /user/profile/delete - Delete user account
 */
$router->post('/user/profile/delete', function () use ($latte, $userService) {
    $controller = new ProfileController($latte, $userService);
    $controller->delete();
});

/**
 * GET /user/reservas - User bookings list
 */
$router->get('/user/reservas', function () use ($latte, $reservaService) {
    $controller = new MyBookingsController($latte, $reservaService);
    echo $controller->index();
});

/**
 * GET /user/reservas/nueva - New booking form (Preact app)
 */
$router->get('/user/reservas/nueva', function () use ($latte) {
    $controller = new BookingController($latte);
    echo $controller->index();
});

/**
 * POST /user/reservas/cancel/{id} - Cancel a booking
 */
$router->post('/user/reservas/cancel/(\\d+)', function ($bookingId) use ($latte, $reservaService) {
    $controller = new MyBookingsController($latte, $reservaService);
    $controller->cancel((int)$bookingId);
});

/**
 * GET /user/reservas/modify/{id} - Modify a booking
 */
$router->get('/user/reservas/modify/(\\d+)', function ($bookingId) use ($latte, $reservaService) {
    $controller = new MyBookingsController($latte, $reservaService);
    $controller->modify((int)$bookingId);
});

/**
 * GET /user/reservas/pdf - Export user bookings to PDF
 */
$router->get('/user/reservas/pdf', function () use ($latte, $reservaService) {
    $controller = new PdfExportController($latte, $reservaService);
    $controller->exportReservas();
});

// =============================================================================
//  SPECIALIST ROUTES
// =============================================================================

/**
 * GET /specialist - Specialist dashboard
 */
$router->get('/specialist', function () use ($latte, $especialistaRepository, $reservaRepository) {
    $controller = new SpecialistController($latte, $especialistaRepository, $reservaRepository);
    echo $controller->index();
});

/**
 * GET /specialist/bookings - Specialist bookings management
 */
$router->get('/specialist/bookings', function () use ($latte, $especialistaRepository, $reservaRepository) {
    $controller = new SpecialistController($latte, $especialistaRepository, $reservaRepository);
    echo $controller->bookings();
});

/**
 * GET /specialist/profile - Specialist profile page
 */
$router->get('/specialist/profile', function () use ($latte, $especialistaRepository, $reservaRepository) {
    $controller = new SpecialistController($latte, $especialistaRepository, $reservaRepository);
    echo $controller->profile();
});

// =============================================================================
//  PDF EXPORT ROUTES (Admin)
// =============================================================================

/**
 * GET /admin/bookings/pdf - Export admin bookings to PDF
 */
$router->get('/admin/bookings/pdf', function () use ($latte, $reservaService) {
    $controller = new PdfExportController($latte, $reservaService);
    $controller->exportAdminReservas();
});

/**
 * GET /admin/users/pdf - Export users list to PDF
 */
$router->get('/admin/users/pdf', function () use ($latte, $reservaService, $userService) {
    $controller = new PdfExportController($latte, $reservaService, $userService);
    $controller->exportAdminUsers();
});

// =============================================================================
//  ADMIN API ROUTES - Users
// =============================================================================

/**
 * GET /admin/api/users - Get all users with pagination
 */
$router->get('/admin/api/users', function () use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->getAllUsers();
});

/**
 * POST /admin/api/users - Create new user
 */
$router->post('/admin/api/users', function () use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->createUser();
});

/**
 * GET /admin/api/users/{id} - Get user by ID
 */
$router->get('/admin/api/users/(\\d+)', function ($id) use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->getUserById((int)$id);
});

/**
 * POST /admin/api/users/{id} - Update user (POST method)
 */
$router->post('/admin/api/users/(\\d+)', function ($id) use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->updateUser((int)$id);
});

/**
 * PUT /admin/api/users/{id} - Update user (PUT method)
 */
$router->put('/admin/api/users/(\\d+)', function ($id) use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->updateUser((int)$id);
});

/**
 * DELETE /admin/api/users/{id} - Delete (deactivate) user
 */
$router->delete('/admin/api/users/(\\d+)', function ($id) use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->deleteUser((int)$id);
});

// =============================================================================
//  ADMIN API ROUTES - Services
// =============================================================================

/**
 * GET /admin/api/services - Get all services
 */
$router->get('/admin/api/services', function () use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->getAll();
});

/**
 * GET /admin/api/services/{id} - Get service by ID
 */
$router->get('/admin/api/services/(\\d+)', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->getServiceById((int)$id);
});

/**
 * POST /admin/api/services - Create new service
 */
$router->post('/admin/api/services', function () use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->createService();
});

/**
 * PUT /admin/api/services/{id} - Update service
 */
$router->put('/admin/api/services/(\\d+)', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->updateService((int)$id);
});

/**
 * POST /admin/api/services/{id}/activate - Activate service
 */
$router->post('/admin/api/services/(\\d+)/activate', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->activateService((int)$id);
});

/**
 * POST /admin/api/services/{id}/deactivate - Deactivate service
 */
$router->post('/admin/api/services/(\\d+)/deactivate', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->deactivateService((int)$id);
});

// =============================================================================
//  ADMIN API ROUTES - Bookings
// =============================================================================

/**
 * GET /admin/api/reservas - Get all bookings with filters
 */
$router->get('/admin/api/reservas', function () use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->getAllBookings();
});

/**
 * GET /admin/api/reservas/{id} - Get booking by ID
 */
$router->get('/admin/api/reservas/(\\d+)', function ($id) use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->getBookingById((int)$id);
});

/**
 * POST /admin/api/reservas - Create new booking
 */
$router->post('/admin/api/reservas', function () use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->createBooking();
});

/**
 * PUT /admin/api/reservas/{id} - Update booking
 */
$router->put('/admin/api/reservas/(\\d+)', function ($id) use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->updateBooking((int)$id);
});

/**
 * DELETE /admin/api/reservas/{id} - Delete booking
 */
$router->delete('/admin/api/reservas/(\\d+)', function ($id) use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->deleteBooking((int)$id);
});

// =============================================================================
//  ADMIN API ROUTES - Statistics
// =============================================================================

/**
 * GET /admin/api/especialistas - Get all specialists with user data
 */
$router->get('/admin/api/especialistas', function () use ($especialistaRepository, $reservaService, $servicioService) {
    $controller = new StatsApiController($especialistaRepository, $reservaService, $servicioService);
    $controller->getEspecialistas();
});

/**
 * GET /admin/api/stats/specialist-occupancy - Get specialist occupancy statistics
 */
$router->get('/admin/api/stats/specialist-occupancy', function () use ($especialistaRepository, $reservaService, $servicioService) {
    $controller = new StatsApiController($especialistaRepository, $reservaService, $servicioService);
    $controller->getSpecialistOccupancy();
});

/**
 * GET /admin/api/stats/popular-services - Get popular services statistics
 */
$router->get('/admin/api/stats/popular-services', function () use ($especialistaRepository, $reservaService, $servicioService) {
    $controller = new StatsApiController($especialistaRepository, $reservaService, $servicioService);
    $controller->getPopularServices();
});

/**
 * GET /admin/api/stats/today-kpis - Get today's KPIs (bookings, revenue)
 */
$router->get('/admin/api/stats/today-kpis', function () use ($especialistaRepository, $reservaService, $servicioService) {
    $controller = new StatsApiController($especialistaRepository, $reservaService, $servicioService);
    $controller->getTodayKpis();
});

// =============================================================================
//  PUBLIC API ROUTES
// =============================================================================

/**
 * GET /api/services - Get all active services
 */
$router->get('/api/services', function () use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->getAll();
});

/**
 * GET /api/especialistas/disponibles - Get available specialists for service/date
 */
$router->get('/api/especialistas/disponibles', function () use ($especialistaRepository) {
    $controller = new EspecialistaApiController($especialistaRepository);
    $controller->getDisponibles();
});

/**
 * GET /api/reservas - Get user bookings
 */
$router->get('/api/reservas', function () use ($reservaService) {
    $controller = new BookingApiController($reservaService);
    $controller->getReservas();
});

/**
 * POST /api/reservas - Create new booking
 */
$router->post('/api/reservas', function () use ($reservaService) {
    $controller = new BookingApiController($reservaService);
    $controller->createReserva();
});

/**
 * GET /api/me - Get current authenticated user data
 */
$router->get('/api/me', function () use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->getCurrentUser();
});

// =============================================================================
//  RUN ROUTER
// =============================================================================

$router->run();
