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
 * Home page
 */
$router->get('/', function () use ($latte, $emailService) {
    $controller = new HomeController($latte, $emailService);
    echo $controller->index();
});

/**
 * Send contact form
 */
$router->post('/contacto', function () use ($latte, $emailService) {
    $controller = new HomeController($latte, $emailService);
    $controller->contact();
});

// =============================================================================
//  AUTHENTICATION ROUTES
// =============================================================================

/**
 * Show login page
 */
$router->get('/login', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showLogin();
});

/**
 * Log in
 */
$router->post('/login', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->login();
});

/**
 * Show registration page
 */
$router->get('/register', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showRegister();
});

/**
 * Create new account
 */
$router->post('/register', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->register();
});

/**
 * Log out
 */
$router->get('/logout', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->logout();
});

/**
 * Forgot password page
 */
$router->get('/forgot-password', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showForgotPasswordForm();
});

/**
 * Send password reset email
 */
$router->post('/forgot-password', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->sendResetLink();
});

/**
 * Reset password page
 */
$router->get('/reset-password', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showResetPasswordForm();
});

/**
 * Change password
 */
$router->post('/reset-password', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->resetPassword();
});

/**
 * Reactivate account page
 */
$router->get('/reactivate', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    echo $controller->showReactivate();
});

/**
 * Reactivate account
 */
$router->post('/reactivate', function () use ($latte, $authService, $emailService, $userService) {
    $controller = new AuthController($latte, $authService, $emailService, $userService);
    $controller->reactivate();
});

// =============================================================================
//  ADMIN ROUTES (Web Pages)
// =============================================================================

/**
 * Admin dashboard
 */
$router->get('/admin', function () use ($latte) {
    $controller = new AdminController($latte);
    echo $controller->index();
});

/**
 * Manage users
 */
$router->get('/admin/users', function () use ($latte, $userService, $servicioService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new AdminController($latte, $userService, $servicioService, null, $especialistaServicioRepository, $especialistaRepository);
    echo $controller->usersManagement();
});

/**
 * Manage services
 */
$router->get('/admin/services', function () use ($latte, $servicioService) {
    $controller = new AdminController($latte, null, $servicioService);
    echo $controller->servicesManagement();
});

/**
 * Manage bookings
 */
$router->get('/admin/bookings', function () use ($latte, $userService, $servicioService, $reservaService, $especialistaRepository) {
    $controller = new AdminController($latte, $userService, $servicioService, $reservaService, null, $especialistaRepository);
    echo $controller->bookingsManagement();
});

// =============================================================================
//  USER ROUTES
// =============================================================================

/**
 * User dashboard
 */
$router->get('/user', function () use ($latte, $reservaService) {
    $controller = new UserController($latte, $reservaService);
    echo $controller->index();
});

/**
 * User profile
 */
$router->get('/user/profile', function () use ($latte, $userService) {
    $controller = new ProfileController($latte, $userService);
    echo $controller->index();
});

/**
 * Update profile
 */
$router->post('/user/profile/update', function () use ($latte, $userService) {
    $controller = new ProfileController($latte, $userService);
    $controller->update();
});

/**
 * Delete account
 */
$router->post('/user/profile/delete', function () use ($latte, $userService) {
    $controller = new ProfileController($latte, $userService);
    $controller->delete();
});

/**
 * My bookings
 */
$router->get('/user/reservas', function () use ($latte, $reservaService) {
    $controller = new MyBookingsController($latte, $reservaService);
    echo $controller->index();
});

/**
 * Make a new booking
 */
$router->get('/user/reservas/nueva', function () use ($latte) {
    $controller = new BookingController($latte);
    echo $controller->index();
});

/**
 * Cancel a booking
 */
$router->post('/user/reservas/cancel/(\\d+)', function ($bookingId) use ($latte, $reservaService) {
    $controller = new MyBookingsController($latte, $reservaService);
    $controller->cancel((int)$bookingId);
});

/**
 * Modify a booking
 */
$router->get('/user/reservas/modify/(\\d+)', function ($bookingId) use ($latte, $reservaService) {
    $controller = new MyBookingsController($latte, $reservaService);
    $controller->modify((int)$bookingId);
});

/**
 * Download my bookings as PDF
 */
$router->get('/user/reservas/pdf', function () use ($latte, $reservaService) {
    $controller = new PdfExportController($latte, $reservaService);
    $controller->exportReservas();
});

// =============================================================================
//  SPECIALIST ROUTES
// =============================================================================

/**
 * Specialist dashboard
 */
$router->get('/specialist', function () use ($latte, $especialistaRepository, $reservaRepository) {
    $controller = new SpecialistController($latte, $especialistaRepository, $reservaRepository);
    echo $controller->index();
});

/**
 * Specialist bookings
 */
$router->get('/specialist/bookings', function () use ($latte, $especialistaRepository, $reservaRepository) {
    $controller = new SpecialistController($latte, $especialistaRepository, $reservaRepository);
    echo $controller->bookings();
});

/**
 * Specialist profile
 */
$router->get('/specialist/profile', function () use ($latte, $especialistaRepository, $reservaRepository) {
    $controller = new SpecialistController($latte, $especialistaRepository, $reservaRepository);
    echo $controller->profile();
});

// =============================================================================
//  PDF EXPORT ROUTES (Admin)
// =============================================================================

/**
 * Export bookings to PDF
 */
$router->get('/admin/bookings/pdf', function () use ($latte, $reservaService) {
    $controller = new PdfExportController($latte, $reservaService);
    $controller->exportAdminReservas();
});

/**
 * Export users to PDF
 */
$router->get('/admin/users/pdf', function () use ($latte, $reservaService, $userService) {
    $controller = new PdfExportController($latte, $reservaService, $userService);
    $controller->exportAdminUsers();
});

// =============================================================================
//  ADMIN API ROUTES - Users
// =============================================================================

/**
 * Get all users
 */
$router->get('/admin/api/users', function () use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->getAllUsers();
});

/**
 * Create user
 */
$router->post('/admin/api/users', function () use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->createUser();
});

/**
 * Get one user
 */
$router->get('/admin/api/users/(\\d+)', function ($id) use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->getUserById((int)$id);
});

/**
 * Update user (POST)
 */
$router->post('/admin/api/users/(\\d+)', function ($id) use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->updateUser((int)$id);
});

/**
 * Update user (PUT)
 */
$router->put('/admin/api/users/(\\d+)', function ($id) use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->updateUser((int)$id);
});

/**
 * Delete user
 */
$router->delete('/admin/api/users/(\\d+)', function ($id) use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->deleteUser((int)$id);
});

// =============================================================================
//  ADMIN API ROUTES - Services
// =============================================================================

/**
 * Get all services
 */
$router->get('/admin/api/services', function () use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->getAll();
});

/**
 * Get one service
 */
$router->get('/admin/api/services/(\\d+)', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->getServiceById((int)$id);
});

/**
 * Create service
 */
$router->post('/admin/api/services', function () use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->createService();
});

/**
 * Update service
 */
$router->put('/admin/api/services/(\\d+)', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->updateService((int)$id);
});

/**
 * Activate service
 */
$router->post('/admin/api/services/(\\d+)/activate', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->activateService((int)$id);
});

/**
 * Deactivate service
 */
$router->post('/admin/api/services/(\\d+)/deactivate', function ($id) use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->deactivateService((int)$id);
});

// =============================================================================
//  ADMIN API ROUTES - Bookings
// =============================================================================

/**
 * Get all bookings
 */
$router->get('/admin/api/reservas', function () use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->getAllBookings();
});

/**
 * Get one booking
 */
$router->get('/admin/api/reservas/(\\d+)', function ($id) use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->getBookingById((int)$id);
});

/**
 * Create booking
 */
$router->post('/admin/api/reservas', function () use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->createBooking();
});

/**
 * Update booking
 */
$router->put('/admin/api/reservas/(\\d+)', function ($id) use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->updateBooking((int)$id);
});

/**
 * Delete booking
 */
$router->delete('/admin/api/reservas/(\\d+)', function ($id) use ($reservaService) {
    $controller = new BookingAdminApiController($reservaService);
    $controller->deleteBooking((int)$id);
});

// =============================================================================
//  ADMIN API ROUTES - Statistics
// =============================================================================

/**
 * Get all specialists
 */
$router->get('/admin/api/especialistas', function () use ($especialistaRepository, $reservaService, $servicioService) {
    $controller = new StatsApiController($especialistaRepository, $reservaService, $servicioService);
    $controller->getEspecialistas();
});

/**
 * Specialist occupancy stats
 */
$router->get('/admin/api/stats/specialist-occupancy', function () use ($especialistaRepository, $reservaService, $servicioService) {
    $controller = new StatsApiController($especialistaRepository, $reservaService, $servicioService);
    $controller->getSpecialistOccupancy();
});

/**
 * Popular services stats
 */
$router->get('/admin/api/stats/popular-services', function () use ($especialistaRepository, $reservaService, $servicioService) {
    $controller = new StatsApiController($especialistaRepository, $reservaService, $servicioService);
    $controller->getPopularServices();
});

/**
 * Today's numbers
 */
$router->get('/admin/api/stats/today-kpis', function () use ($especialistaRepository, $reservaService, $servicioService) {
    $controller = new StatsApiController($especialistaRepository, $reservaService, $servicioService);
    $controller->getTodayKpis();
});

// =============================================================================
//  PUBLIC API ROUTES
// =============================================================================

/**
 * Get available services
 */
$router->get('/api/services', function () use ($servicioService) {
    $controller = new ServiceApiController($servicioService);
    $controller->getAll();
});

/**
 * Get available specialists
 */
$router->get('/api/especialistas/disponibles', function () use ($especialistaRepository) {
    $controller = new EspecialistaApiController($especialistaRepository);
    $controller->getDisponibles();
});

/**
 * Get my bookings
 */
$router->get('/api/reservas', function () use ($reservaService) {
    $controller = new BookingApiController($reservaService);
    $controller->getReservas();
});

/**
 * Create a booking
 */
$router->post('/api/reservas', function () use ($reservaService) {
    $controller = new BookingApiController($reservaService);
    $controller->createReserva();
});

/**
 * Get my user data
 */
$router->get('/api/me', function () use ($latte, $userService, $especialistaServicioRepository, $especialistaRepository) {
    $controller = new UserApiController($latte, $userService, $especialistaServicioRepository, $especialistaRepository);
    $controller->getCurrentUser();
});

// =============================================================================
//  RUN ROUTER
// =============================================================================

$router->run();
