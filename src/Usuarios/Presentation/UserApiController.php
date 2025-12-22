<?php

namespace Usuarios\Presentation;

use Latte\Engine;
use Shared\Infrastructure\Pagination\Paginator;
use Usuarios\Application\UserService;
use Usuarios\Presentation\Transformers\UserTransformer;

class UserApiController
{
    private UserService $userService;
    private Engine $latte;
    private \Especialistas\Infrastructure\EspecialistaServicioRepository $especialistaServicioRepository;
    private \Especialistas\Infrastructure\EspecialistaRepository $especialistaRepository;

    public function __construct(
        Engine $latte,
        UserService $userService,
        \Especialistas\Infrastructure\EspecialistaServicioRepository $especialistaServicioRepository,
        \Especialistas\Infrastructure\EspecialistaRepository $especialistaRepository
    ) {
        $this->latte = $latte;
        $this->userService = $userService;
        $this->especialistaServicioRepository = $especialistaServicioRepository;
        $this->especialistaRepository = $especialistaRepository;
    }

    public function getAllUsers(): void
    {
        header('Content-Type: application/json');

        try {
            $limit = 10;
            $page = (int) ($_GET['page'] ?? 1);
            $search = trim($_GET['search'] ?? '');
            $offset = ($page - 1) * $limit;

            if (!empty($search)) {
                $users = $this->userService->searchUsers($search, $limit, $offset);
                $total = $this->userService->getTotalSearchResults($search);
            } else {
                $users = $this->userService->getAllUsers($limit, $offset);
                $total = $this->userService->getTotalUsers();
            }

            $totalPages = (int) ceil($total / $limit);

            echo json_encode([
                'success' => true,
                'users' => UserTransformer::toArrayCollection($users),
                'total' => $total,
                'page' => $page,
                'totalPages' => $totalPages
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }

    public function getUserById(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $user = $this->userService->getUserById($id);

            if (!$user) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Usuario no encontrado'
                ], JSON_PRETTY_PRINT);
                return;
            }

            $userData = UserTransformer::toJsonApi($user);

            // Si es especialista, cargar sus servicios
            if ($user->getRol() === 'Especialista') {
                $especialistaId = $this->especialistaRepository->getEspecialistaIdByUserId($id);
                if ($especialistaId) {
                    $servicios = $this->especialistaServicioRepository->getServiciosForEspecialista($especialistaId);
                    $userData['servicios'] = array_map(fn($s) => $s->getIdServicio(), $servicios);
                }
            }

            echo json_encode([
                'success' => true,
                'data' => $userData
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }

    public function getUsersTable(): string
    {
        try {
            $limit = 10;
            $page = Paginator::validatePage($_GET['page'] ?? 1, PHP_INT_MAX);
            $users = $this->userService->getAllUsers($limit, Paginator::getOffset($page, $limit));
            $total = $this->userService->getTotalUsers();
            $totalPages = Paginator::getTotalPages($total, $limit);
            $page = Paginator::validatePage($page, $totalPages);

            return $this->latte->renderToString(
                __DIR__ . '/../../../views/components/users-table-content.latte',
                [
                    'users' => UserTransformer::toArrayCollection($users),
                    'pagination' => Paginator::getPagination($page, $totalPages, '/admin/users/table'),
                    'hasUsers' => count($users) > 0
                ]
            );
        } catch (\Exception $e) {
            return '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    public function searchUsersTable(): string
    {
        try {
            $search = trim($_GET['search'] ?? '');

            if (empty($search)) {
                return $this->getUsersTable();
            }

            $limit = 10;
            $page = Paginator::validatePage($_GET['page'] ?? 1, PHP_INT_MAX);
            $users = $this->userService->searchUsers($search, $limit, Paginator::getOffset($page, $limit));
            $total = $this->userService->getTotalSearchResults($search);
            $totalPages = Paginator::getTotalPages($total, $limit);
            $page = Paginator::validatePage($page, $totalPages);

            return $this->latte->renderToString(
                __DIR__ . '/../../../views/components/users-table-content.latte',
                [
                    'users' => UserTransformer::toArrayCollection($users),
                    'pagination' => Paginator::getPagination($page, $totalPages, "/admin/users/search?search={$search}"),
                    'hasUsers' => count($users) > 0
                ]
            );
        } catch (\Exception $e) {
            return '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    public function deleteUser(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $user = $this->userService->getUserById($id);

            if (!$user) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Usuario no encontrado'
                ], JSON_PRETTY_PRINT);
                return;
            }

            $this->userService->deleteUser($id);

            echo json_encode([
                'success' => true,
                'message' => 'Usuario eliminado correctamente'
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }

    public function createUser(): void
    {
        header('Content-Type: application/json');

        try {
            $data = $this->getRequestData();

            if (!$data) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Datos inválidos'
                ], JSON_PRETTY_PRINT);
                return;
            }

            $user = new \Usuarios\Domain\Usuario(
                $data['rol'] ?? 'Cliente',
                $data['nombre'],
                $data['apellidos'],
                $data['email'],
                password_hash($data['password'], PASSWORD_BCRYPT),
                $data['telefono'] ?? null,
                null,
                true,
                null
            );

            $this->userService->setUser($user);

            // Si es especialista, crear entrada en especialistas y asignar servicios
            if ($data['rol'] === 'Especialista' && !empty($data['servicios'])) {
                $userId = $user->getId();

                // Procesar avatar si existe
                $avatarUrl = null;
                if (isset($_FILES['avatar'])) {
                    $avatarUrl = $this->handleAvatarUpload($_FILES['avatar']);
                }

                // Crear entrada en tabla especialistas y obtener el ID
                $especialistaId = $this->especialistaRepository->createBasicEspecialista($userId, $avatarUrl);

                if ($especialistaId) {
                    // Asignar servicios usando id_especialista
                    foreach ($data['servicios'] as $servicioId) {
                        $especialistaServicio = new \Especialistas\Domain\EspecialistaServicio(
                            $especialistaId,
                            (int) $servicioId
                        );
                        $this->especialistaServicioRepository->addEspecialistaServicio($especialistaServicio);
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'data' => UserTransformer::toJsonApi($user)
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }

    public function updateUser(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $data = $this->getRequestData();

            if (!$data) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Datos inválidos'
                ], JSON_PRETTY_PRINT);
                return;
            }

            $existingUser = $this->userService->getUserById($id);

            if (!$existingUser) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Usuario no encontrado'
                ], JSON_PRETTY_PRINT);
                return;
            }

            $passwordHash = !empty($data['password'])
                ? password_hash($data['password'], PASSWORD_BCRYPT)
                : $existingUser->getPassword();

            $user = new \Usuarios\Domain\Usuario(
                $data['rol'],
                $data['nombre'],
                $data['apellidos'],
                $data['email'],
                $passwordHash,
                $data['telefono'] ?? null,
                $existingUser->getFechaRegistro()->format('Y-m-d H:i:s'),
                isset($data['activo']) ? in_array($data['activo'], [true, '1', 1, 'on'], true) : $existingUser->getActivo(),
                $id
            );

            $this->userService->updateUser($user);

            // Si es especialista, actualizar servicios
            if ($data['rol'] === 'Especialista' && isset($data['servicios'])) {
                // Verificar si ya existe entrada en especialistas, si no, crearla
                $especialistaId = $this->especialistaRepository->getEspecialistaIdByUserId($id);

                if (!$especialistaId) {
                    $especialistaId = $this->especialistaRepository->createBasicEspecialista($id);
                }

                if ($especialistaId) {
                    // Actualizar avatar si se subió uno nuevo
                    if (isset($_FILES['avatar'])) {
                        $avatarUrl = $this->handleAvatarUpload($_FILES['avatar']);
                        if ($avatarUrl) {
                            $this->especialistaRepository->updateEspecialistaPhoto($especialistaId, $avatarUrl);
                        }
                    }

                    // Eliminar servicios anteriores y agregar los nuevos
                    $this->especialistaServicioRepository->deleteAllServiciosForEspecialista($especialistaId);

                    foreach ($data['servicios'] as $servicioId) {
                        $especialistaServicio = new \Especialistas\Domain\EspecialistaServicio(
                            $especialistaId,
                            (int) $servicioId
                        );
                        $this->especialistaServicioRepository->addEspecialistaServicio($especialistaServicio);
                    }
                }
            }

            echo json_encode([
                'success' => true,
                'message' => 'Usuario actualizado correctamente',
                'data' => UserTransformer::toJsonApi($user)
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }
    public function getCurrentUser(): void
    {
        header('Content-Type: application/json');

        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'No autorizado']);
                return;
            }

            $user = $this->userService->getUserById($_SESSION['user_id']);

            if (!$user) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
                return;
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $user->getId(),
                    'nombre' => $user->getNombre(),
                    'apellidos' => $user->getApellidos(),
                    'email' => $user->getEmail()
                ]
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function getRequestData(): array
    {
        $input = file_get_contents('php://input');
        if (!empty($input)) {
            $jsonData = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                return $jsonData;
            }
        }
        return $_POST;
    }

    private function handleAvatarUpload(?array $file): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        // Definir ruta absoluta para uploads
        $uploadDir = __DIR__ . '/../../../../public/images/avatars/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('avatar_') . '.' . $extension;

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return '/images/avatars/' . $filename;
        }

        return null;
    }
}
