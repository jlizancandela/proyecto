<?php

use Latte\Engine;
use Shared\Infrastructure\Database\Database;
use Usuarios\Infrastructure\UserRepository;
use Usuarios\Application\UserService;
use Usuarios\Application\AuthService;

$latte = new Engine();
$latte->setTempDirectory(__DIR__ . '/../../../temp/cache');

$db = (new Database())->getConnection();

$userRepository = new UserRepository($db);
$userService = new UserService($userRepository);
$authService = new AuthService($userRepository, $userService);

$servicioRepository = new \Servicios\Infrastructure\ServicioRepository($db);
$especialistaRepository = new \Especialistas\Infrastructure\EspecialistaRepository($db);
$reservaRepository = new \Reservas\Infrastructure\ReservaRepository($db);
$reservaService = new \Reservas\Application\ReservaService($reservaRepository);
