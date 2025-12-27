<?php

/**
 * Simple dependency injection container that initializes all services and repositories used across the app.
 */

use Latte\Engine;
use Shared\Infrastructure\Database\Database;
use Shared\Infrastructure\Email\EmailService;
use Usuarios\Infrastructure\UserRepository;
use Usuarios\Application\UserService;
use Usuarios\Application\AuthService;
use Servicios\Infrastructure\ServicioRepository;
use Servicios\Application\ServicioService;
use Especialistas\Infrastructure\EspecialistaRepository;
use Especialistas\Infrastructure\EspecialistaServicioRepository;
use Reservas\Infrastructure\ReservaRepository;
use Reservas\Application\ReservaService;

$latte = new Engine();
$latte->setTempDirectory(__DIR__ . '/../../../temp/cache');

$db = (new Database())->getConnection();

$emailService = new EmailService();

$userRepository = new UserRepository($db);
$userService = new UserService($userRepository);
$authService = new AuthService($userRepository, $userService);

$servicioRepository = new ServicioRepository($db);
$servicioService = new ServicioService($servicioRepository);
$especialistaRepository = new EspecialistaRepository($db);
$especialistaServicioRepository = new EspecialistaServicioRepository($db);
$reservaRepository = new ReservaRepository($db);
$reservaService = new ReservaService($reservaRepository);
