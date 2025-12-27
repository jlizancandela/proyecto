<?php

/**
 * Handles user API endpoints and table rendering for admin panel.
 */

namespace Usuarios\Presentation;

use Latte\Engine;
use Shared\Infrastructure\Pagination\Paginator;
use Usuarios\Application\UserService;
use Usuarios\Presentation\Transformers\UserTransformer;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

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

    /**
     * Retrieves all users with pagination and optional search.
     *
     * @return void
     */
    public function getAllUsers(): void
    {
        header('Content-Type: application/json');

        try {
            $limit = min((int) ($_GET['limit'] ?? 10), 1000);
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

    /**
     * Retrieves a single user by ID with specialist data if applicable.
     *
     * @param int $id User ID
     * @return void
     */
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

            if ($user->getRol() === \Usuarios\Domain\UserRole::Especialista) {
                $especialistaData = $this->especialistaRepository->getEspecialistaDataByUserId($id);
                if ($especialistaData) {
                    $userData['descripcion'] = $especialistaData['descripcion'];
                    $userData['foto_url'] = $especialistaData['foto_url'];
                    $especialistaId = $especialistaData['id_especialista'];

                    $servicios = $this->especialistaServicioRepository->getServiciosForEspecialista($especialistaId);
                    $userData['servicios'] = array_map(fn($s) => $s->getIdServicio(), $servicios);
                } else {
                    $userData['servicios'] = [];
                    $userData['descripcion'] = '';
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

    /**
     * Renders the users table HTML for admin panel.
     *
     * @return string HTML content
     */
    public function getUsersTable(): string
    {
        try {
            $limit = 10;
            $page = Paginator::validatePage($_GET['page'] ?? 1, PHP_INT_MAX);
            $users = $this->userService->getAllUsers($limit, Paginator::getOffset($page, $limit));
            $total = $this->userService->getTotalUsers();
            $totalPages = Paginator::getTotalPages($total, $limit);
            $page = Paginator::validatePage($page, $totalPages);

            $usersArray = UserTransformer::toArrayCollection($users);
            $this->enrichUsersWithServices($usersArray);

            return $this->latte->renderToString(
                __DIR__ . '/../../../views/components/users-table-content.latte',
                [
                    'users' => $usersArray,
                    'pagination' => Paginator::getPagination($page, $totalPages, '/admin/users/table'),
                    'hasUsers' => count($users) > 0
                ]
            );
        } catch (\Exception $e) {
            return '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /**
     * Renders the search results table HTML for admin panel.
     *
     * @return string HTML content
     */
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

            $usersArray = UserTransformer::toArrayCollection($users);
            $this->enrichUsersWithServices($usersArray);

            return $this->latte->renderToString(
                __DIR__ . '/../../../views/components/users-table-content.latte',
                [
                    'users' => $usersArray,
                    'pagination' => Paginator::getPagination($page, $totalPages, "/admin/users/search?search={$search}"),
                    'hasUsers' => count($users) > 0
                ]
            );
        } catch (\Exception $e) {
            return '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /**
     * Deactivates a user (soft delete).
     *
     * @param int $id User ID
     * @return void
     */
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

            if ($user->getRol() === \Usuarios\Domain\UserRole::Admin) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'error' => 'No se puede desactivar un usuario administrador'
                ], JSON_PRETTY_PRINT);
                return;
            }

            $this->userService->deactivateUser($id);

            echo json_encode([
                'success' => true,
                'message' => 'Usuario desactivado correctamente'
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Creates a new user with validation.
     *
     * @return void
     */
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

            $this->validateUserData($data, true);

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

            if ($data['rol'] === 'Especialista' && !empty($data['servicios'])) {
                $this->handleEspecialistaCreation($user->getId(), $data);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'data' => UserTransformer::toJsonApi($user)
            ], JSON_PRETTY_PRINT);
        } catch (ValidationException $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Updates an existing user with validation.
     *
     * @param int $id User ID
     * @return void
     */
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

            $this->validateUserData($data, false);

            $rol = $data['rol'] ?? $existingUser->getRol()->value;
            $nombre = $data['nombre'] ?? $existingUser->getNombre();
            $apellidos = $data['apellidos'] ?? $existingUser->getApellidos();
            $email = $data['email'] ?? $existingUser->getEmail();
            $telefono = $data['telefono'] ?? $existingUser->getTelefono();
            $activo = isset($data['activo']) ? in_array($data['activo'], [true, '1', 1, 'on'], true) : $existingUser->getActivo();

            if ($existingUser->getRol() === \Usuarios\Domain\UserRole::Admin) {
                $rol = 'Admin';
                $activo = true;
            }

            $passwordHash = !empty($data['password'])
                ? password_hash($data['password'], PASSWORD_BCRYPT)
                : $existingUser->getPassword();

            $user = new \Usuarios\Domain\Usuario(
                $rol,
                $nombre,
                $apellidos,
                $email,
                $passwordHash,
                $telefono,
                $existingUser->getFechaRegistro()->format('Y-m-d H:i:s'),
                $activo,
                $id
            );

            $this->userService->updateUser($user);

            if ($rol === 'Especialista' && isset($data['servicios'])) {
                $this->handleEspecialistaUpdate($id, $data);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Usuario actualizado correctamente',
                'data' => UserTransformer::toJsonApi($user)
            ], JSON_PRETTY_PRINT);
        } catch (ValidationException $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Returns the currently authenticated user data.
     *
     * @return void
     */
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

    /**
     * Validates user data using Respect Validation.
     *
     * @param array $data User data to validate
     * @param bool $requirePassword Whether password is required
     * @return void
     * @throws ValidationException If validation fails
     */
    private function validateUserData(array $data, bool $requirePassword): void
    {
        $validator = v::key('nombre', v::stringType()->notEmpty()->length(2, 50), !isset($data['nombre']))
            ->key('apellidos', v::stringType()->notEmpty()->length(2, 100), !isset($data['apellidos']))
            ->key('email', v::email(), !isset($data['email']))
            ->key('telefono', v::optional(v::phone()), false)
            ->key('rol', v::optional(v::in(['Admin', 'Especialista', 'Cliente'])), false);

        if ($requirePassword) {
            $validator = $validator->key('password', v::stringType()->notEmpty()->length(6, null));
        }

        $validator->assert($data);
    }

    /**
     * Enriches user array with specialist services.
     *
     * @param array &$usersArray Users array to enrich (passed by reference)
     * @return void
     */
    private function enrichUsersWithServices(array &$usersArray): void
    {
        foreach ($usersArray as &$userData) {
            if ($userData['rol'] === 'Especialista') {
                $especialistaId = $this->especialistaRepository->getEspecialistaIdByUserId($userData['id']);
                if ($especialistaId) {
                    $servicios = $this->especialistaServicioRepository->getServiciosForEspecialista($especialistaId);
                    $userData['servicios'] = array_map(fn($s) => $s->getNombreServicio(), $servicios);
                } else {
                    $userData['servicios'] = [];
                }
            } else {
                $userData['servicios'] = [];
            }
        }
    }

    /**
     * Handles specialist creation with services and avatar.
     *
     * @param int $userId User ID
     * @param array $data Request data
     * @return void
     */
    private function handleEspecialistaCreation(int $userId, array $data): void
    {
        $avatarUrl = null;
        if (isset($_FILES['avatar'])) {
            $avatarUrl = $this->handleAvatarUpload($_FILES['avatar']);
        }

        $descripcion = $data['descripcion'] ?? null;
        $especialistaId = $this->especialistaRepository->createBasicEspecialista($userId, $avatarUrl, $descripcion);

        if ($especialistaId) {
            foreach ($data['servicios'] as $servicioId) {
                $especialistaServicio = new \Especialistas\Domain\EspecialistaServicio(
                    $especialistaId,
                    (int) $servicioId
                );
                $this->especialistaServicioRepository->addEspecialistaServicio($especialistaServicio);
            }
        }
    }

    /**
     * Handles specialist update with services and avatar.
     *
     * @param int $userId User ID
     * @param array $data Request data
     * @return void
     */
    private function handleEspecialistaUpdate(int $userId, array $data): void
    {
        $especialistaId = $this->especialistaRepository->getEspecialistaIdByUserId($userId);

        if (!$especialistaId) {
            $especialistaId = $this->especialistaRepository->createBasicEspecialista($userId);
        }

        if ($especialistaId) {
            if (isset($_FILES['avatar'])) {
                $avatarUrl = $this->handleAvatarUpload($_FILES['avatar']);
                if ($avatarUrl) {
                    $this->especialistaRepository->updateEspecialistaPhoto($especialistaId, $avatarUrl);
                }
            }

            if (isset($data['descripcion'])) {
                $this->especialistaRepository->updateEspecialistaDescription($especialistaId, $data['descripcion']);
            }

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

    /**
     * Retrieves request data from JSON or POST.
     *
     * @return array Request data
     */
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

    /**
     * Handles avatar file upload with validation.
     *
     * @param array|null $file Uploaded file data
     * @return string|null Avatar URL or null if upload failed
     */
    private function handleAvatarUpload(?array $file): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        $uploadDir = __DIR__ . '/../../../public/images/avatars/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('avatar_') . '.' . $extension;

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return '/public/images/avatars/' . $filename;
        }

        return null;
    }
}
