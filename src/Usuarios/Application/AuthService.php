<?php

namespace Usuarios\Application;

use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;
use Usuarios\Infrastructure\UserRepository;
use Respect\Validation\Validator as v;

class AuthService
{
    private UserRepository $userRepository;
    private UserService $userService;

    public function __construct(
        UserRepository $userRepository,
        UserService $userService
    ) {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    public function register(array $userData): Usuario
    {
        $this->validateUserData($userData);

        $result = $this->userRepository->getUserByEmail($userData['email']);
        if ($result) {
            throw new \RuntimeException("El email ya está registrado");
        }

        $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);

        $user = new Usuario(
            $userData['rol'] ?? UserRole::Cliente->value,
            $userData['nombre'],
            $userData['apellidos'],
            $userData['email'],
            $passwordHash,
            $userData['telefono'] ?? null
        );

        $this->userService->setUser($user);

        return $user;
    }

    private function validateUserData(array $userData): void
    {
        $validator = v::key('nombre', v::stringType()->notEmpty()->length(2, 50))
            ->key('apellidos', v::stringType()->notEmpty()->length(2, 100))
            ->key('email', v::email())
            ->key('password', v::stringType()->notEmpty())
            ->key('telefono', v::optional(v::phone()), false)
            ->key('rol', v::optional(v::in(['ADMIN', 'ESPECIALISTA', 'CLIENTE'])), false);

        try {
            $validator->assert($userData);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $this->validatePassword($userData['password']);
    }

    private function validatePassword(string $password): void
    {
        $passwordValidator = v::allOf(
            v::length(8, null),
            v::regex('/[A-Z]/'),
            v::regex('/[a-z]/'),
            v::regex('/[0-9]/'),
            v::regex('/[@$!%*?&#.,;:\-_+]/')
        );

        try {
            $passwordValidator->assert($password);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            throw new \RuntimeException(
                "La contraseña debe tener al menos 8 caracteres, " .
                    "una letra mayúscula, una letra minúscula, un número " .
                    "y un carácter especial (@$!%*?&#.,;:-_+)"
            );
        }
    }

    public function login(string $email, string $password): ?Usuario
    {
        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            return null;
        }

        return password_verify($password, $user->getPassword()) ? $user : null;
    }

    public function startSession(Usuario $user): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user->getId();
        $_SESSION['email'] = $user->getEmail();
        $_SESSION['role'] = $user->getRol()->value;
        $_SESSION['name'] = $user->getNombre();

        session_regenerate_id(true);
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();
    }

    public function getCurrentUser(): ?Usuario
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id'])
            ? $this->userRepository->getUserById($_SESSION['user_id'])
            : null;
    }

    public function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']);
    }

    public function changePassword(
        int $userId,
        string $oldPassword,
        string $newPassword
    ): bool {
        $user = $this->userRepository->getUserById($userId);

        if (!$user || !password_verify($oldPassword, $user->getPassword())) {
            return false;
        }

        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->setPassword($newPasswordHash);
        $this->userService->updateUser($user);

        return true;
    }

    public function hasRole(UserRole $role): bool
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            return false;
        }

        return $user->getRol() === $role;
    }
}
