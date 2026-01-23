<?php

require_once __DIR__ . '/vendor/autoload.php';

use Reservas\Infrastructure\ReservaRepository;
use Dotenv\Dotenv;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Conexión a BD
$dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8mb4";
$pdo = new PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$repo = new ReservaRepository($pdo);

echo "Probando countAllFiltered con búsqueda 'Maria'...\n";
$filtros = ['cliente_search' => 'Maria'];
$total = $repo->countAllFiltered($filtros);
echo "Total encontrados: $total\n";
