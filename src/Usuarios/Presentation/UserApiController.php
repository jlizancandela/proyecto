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
                $servicios = $this->especialistaServicioRepository->getServiciosForEspecialista($id);
                $userData['servicios'] = array_map(fn($s) => $s->getIdServicio(), $servicios);
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
            $data = json_decode(file_get_contents('php://input'), true);

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
                // Crear entrada en tabla especialistas
                $this->especialistaRepository->createBasicEspecialista($userId);

                // Asignar servicios
                foreach ($data['servicios'] as $servicioId) {
                    $especialistaServicio = new \Especialistas\Domain\EspecialistaServicio(
                        $userId,
                        (int) $servicioId
                    );
                    $this->especialistaServicioRepository->addEspecialistaServicio($especialistaServicio);
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
            $data = json_decode(file_get_contents('php://input'), true);

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
                $data['activo'] ?? $existingUser->getActivo(),
                $id
            );

            $this->userService->updateUser($user);

            // Si es especialista, actualizar servicios
            if ($data['rol'] === 'Especialista' && isset($data['servicios'])) {
                // Verificar si ya existe entrada en especialistas, si no, crearla
                if (!$this->especialistaRepository->especialistaExists($id)) {
                    $this->especialistaRepository->createBasicEspecialista($id);
                }

                // Eliminar servicios anteriores y agregar los nuevos
                $this->especialistaServicioRepository->deleteAllServiciosForEspecialista($id);

                foreach ($data['servicios'] as $servicioId) {
                    $especialistaServicio = new \Especialistas\Domain\EspecialistaServicio(
                        $id,
                        (int) $servicioId
                    );
                    $this->especialistaServicioRepository->addEspecialistaServicio($especialistaServicio);
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
}
