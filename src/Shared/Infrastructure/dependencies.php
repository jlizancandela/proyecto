<?php

use Latte\Engine;
use Shared\Infrastructure\Database\Database;
use Shared\Infrastructure\Email\EmailService;
use Usuarios\Infrastructure\UserRepository;
use Usuarios\Application\UserService;
use Usuarios\Application\AuthService;

$latte = new Engine();
$latte->setTempDirectory(__DIR__ . '/../../../temp/cache');

$db = (new Database())->getConnection();

$emailService = new EmailService();

$userRepository = new UserRepository($db);
$userService = new UserService($userRepository);
$authService = new AuthService($userRepository, $userService);

$servicioRepository = new \Servicios\Infrastructure\ServicioRepository($db);
$servicioService = new \Servicios\Application\ServicioService($servicioRepository);
$especialistaRepository = new \Especialistas\Infrastructure\EspecialistaRepository($db);
$especialistaServicioRepository = new \Especialistas\Infrastructure\EspecialistaServicioRepository($db);
$reservaRepository = new \Reservas\Infrastructure\ReservaRepository($db);
$reservaService = new \Reservas\Application\ReservaService($reservaRepository);
