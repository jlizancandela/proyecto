<?php

namespace Usuarios\Presentation;

use Shared\Infrastructure\Database\Database;
use Usuarios\Infrastructure\UserRepository;
use Usuarios\Application\UserService;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $userRepository = new UserRepository($db);
        $this->userService = new UserService($userRepository);
    }

    public function index(): string
    {
        // Asegurar que la salida esté en UTF-8
        if (!headers_sent()) {
            header("Content-Type: text/html; charset=utf-8");
        }

        // Extraer parámetros de paginación de la petición HTTP
        $limit = isset($_GET["limit"]) ? (int) $_GET["limit"] : 10;
        $offset = isset($_GET["offset"]) ? (int) $_GET["offset"] : 0;

        // Validar y sanitizar parámetros
        $limit = max(1, min($limit, 100)); // Entre 1 y 100 registros
        $offset = max(0, $offset); // No permitir valores negativos

        // Obtener usuarios y total a través del servicio
        $users = $this->userService->getAllUsers($limit, $offset);
        $totalUsers = $this->userService->getTotalUsers();

        // Calcular información de paginación
        $currentPage = floor($offset / $limit) + 1;
        $totalPages = ceil($totalUsers / $limit);
        $hasNextPage = $offset + $limit < $totalUsers;
        $hasPrevPage = $offset > 0;

        ob_start();
?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Lista de Usuarios</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    padding: 20px;
                }

                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 15px;
                    padding: 40px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                }

                h1 {
                    color: #667eea;
                    margin-bottom: 20px;
                }

                .back-link {
                    display: inline-block;
                    margin-bottom: 20px;
                    color: #667eea;
                    text-decoration: none;
                    font-weight: 500;
                }

                .back-link:hover {
                    text-decoration: underline;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                th,
                td {
                    padding: 12px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }

                th {
                    background: #667eea;
                    color: white;
                }

                tr:hover {
                    background: #f5f5f5;
                }

                .badge {
                    padding: 4px 12px;
                    border-radius: 12px;
                    font-size: 0.85em;
                    font-weight: 500;
                }

                .badge-admin {
                    background: #ef4444;
                    color: white;
                }

                .badge-especialista {
                    background: #10b981;
                    color: white;
                }

                .badge-cliente {
                    background: #3b82f6;
                    color: white;
                }

                .pagination {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 2px solid #e5e7eb;
                }

                .pagination-info {
                    color: #6b7280;
                    font-size: 0.95em;
                }

                .pagination-controls {
                    display: flex;
                    gap: 10px;
                }

                .pagination-btn {
                    padding: 8px 16px;
                    background: #667eea;
                    color: white;
                    text-decoration: none;
                    border-radius: 6px;
                    font-weight: 500;
                    transition: background 0.3s;
                }

                .pagination-btn:hover {
                    background: #5568d3;
                }

                .pagination-btn.disabled {
                    background: #d1d5db;
                    color: #9ca3af;
                    pointer-events: none;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <a href="/" class="back-link">← Volver al inicio</a>
                <h1>👥 Lista de Usuarios</h1>
                <p>Mostrando <?= count(
                                    $users,
                                ) ?> de <?= $totalUsers ?> usuarios (Página <?= $currentPage ?> de <?= $totalPages ?>)</p>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars(
                                        $user->getId(),
                                        ENT_QUOTES | ENT_HTML5,
                                        "UTF-8",
                                    ) ?></td>
                                <td><?= htmlspecialchars(
                                        $user->getNombre() .
                                            " " .
                                            $user->getApellidos(),
                                        ENT_QUOTES | ENT_HTML5,
                                        "UTF-8",
                                    ) ?></td>
                                <td><?= htmlspecialchars(
                                        $user->getEmail(),
                                        ENT_QUOTES | ENT_HTML5,
                                        "UTF-8",
                                    ) ?></td>
                                <td>
                                    <span class="badge badge-<?= strtolower(
                                                                    $user->getRol()->value,
                                                                ) ?>">
                                        <?= htmlspecialchars(
                                            $user->getRol()->value,
                                            ENT_QUOTES | ENT_HTML5,
                                            "UTF-8",
                                        ) ?>
                                    </span>
                                </td>
                                <td><?= $user->getActivo()
                                        ? "✅ Activo"
                                        : "❌ Inactivo" ?></td>
                                <td>
                                    <a href="/usuarios/<?= $user->getId() ?>">Ver detalles</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Controles de paginación -->
                <div class="pagination">
                    <div class="pagination-info">
                        Mostrando registros <?= $offset + 1 ?> - <?= min(
                                                                        $offset + $limit,
                                                                        $totalUsers,
                                                                    ) ?> de <?= $totalUsers ?>
                    </div>
                    <div class="pagination-controls">
                        <?php
                        $prevOffset = max(0, $offset - $limit);
                        $nextOffset = $offset + $limit;
                        ?>
                        <a href="/usuarios?limit=<?= $limit ?>&offset=<?= $prevOffset ?>"
                            class="pagination-btn <?= !$hasPrevPage
                                                        ? "disabled"
                                                        : "" ?>">
                            ← Anterior
                        </a>
                        <a href="/usuarios?limit=<?= $limit ?>&offset=<?= $nextOffset ?>"
                            class="pagination-btn <?= !$hasNextPage
                                                        ? "disabled"
                                                        : "" ?>">
                            Siguiente →
                        </a>
                    </div>
                </div>
            </div>
        </body>

        </html>
    <?php return ob_get_clean();
    }

    public function show(int $id): string
    {
        $user = $this->userService->getUserById($id);

        if (!$user):
            return $this->notFound("Usuario no encontrado");
        endif;

        ob_start();
    ?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Usuario #<?= $id ?></title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    padding: 20px;
                }

                .container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 15px;
                    padding: 40px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                }

                h1 {
                    color: #667eea;
                    margin-bottom: 20px;
                }

                .back-link {
                    display: inline-block;
                    margin-bottom: 20px;
                    color: #667eea;
                    text-decoration: none;
                    font-weight: 500;
                }

                .info-grid {
                    display: grid;
                    gap: 15px;
                    margin-top: 20px;
                }

                .info-item {
                    padding: 15px;
                    background: #f8f9fa;
                    border-radius: 8px;
                    border-left: 4px solid #667eea;
                }

                .info-label {
                    font-weight: bold;
                    color: #667eea;
                    margin-bottom: 5px;
                }

                .info-value {
                    color: #333;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <a href="/usuarios" class="back-link">← Volver a la lista</a>
                <h1>Usuario #<?= $user->getId() ?></h1>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nombre completo</div>
                        <div class="info-value"><?= htmlspecialchars(
                                                    $user->getNombre() . " " . $user->getApellidos(),
                                                    ENT_QUOTES | ENT_HTML5,
                                                    "UTF-8",
                                                ) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars(
                                                    $user->getEmail(),
                                                    ENT_QUOTES | ENT_HTML5,
                                                    "UTF-8",
                                                ) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Teléfono</div>
                        <div class="info-value"><?= htmlspecialchars(
                                                    $user->getTelefono() ?? "No especificado",
                                                    ENT_QUOTES | ENT_HTML5,
                                                    "UTF-8",
                                                ) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Rol</div>
                        <div class="info-value"><?= htmlspecialchars(
                                                    $user->getRol()->value,
                                                    ENT_QUOTES | ENT_HTML5,
                                                    "UTF-8",
                                                ) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Estado</div>
                        <div class="info-value"><?= $user->getActivo()
                                                    ? "✅ Activo"
                                                    : "❌ Inactivo" ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Fecha de registro</div>
                        <div class="info-value"><?= $user
                                                    ->getFechaRegistro()
                                                    ->format("d/m/Y H:i") ?></div>
                    </div>
                </div>
            </div>
        </body>

        </html>
<?php return ob_get_clean();
    }

    public function create(): string
    {
        return "<h1>Crear nuevo usuario</h1><p>Formulario de creación (por implementar)</p>";
    }

    private function notFound(string $message): string
    {
        http_response_code(404);
        return "<h1>404 - No encontrado</h1><p>$message</p>";
    }
}
