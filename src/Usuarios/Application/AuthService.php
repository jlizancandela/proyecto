<?php

namespace Usuarios\Application;

use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;
use Usuarios\Infrastructure\UserRepository;

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
        if (
            empty($userData['nombre']) || empty($userData['apellidos']) ||
            empty($userData['email']) || empty($userData['password'])
        ) {
            throw new \RuntimeException("Todos los campos obligatorios deben estar completos");
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException("El email no tiene un formato válido");
        }

        $result = $this->userRepository->getUserByEmail($userData['email']);
        if ($result) {
            throw new \RuntimeException("El email ya está registrado");
        }

        $password = $userData['password'];
        if (strlen($password) < 8) {
            throw new \RuntimeException("La contraseña debe tener al menos 8 caracteres");
        }
        if (!preg_match('/[A-Z]/', $password)) {
            throw new \RuntimeException("La contraseña debe contener al menos una letra mayúscula");
        }
        if (!preg_match('/[a-z]/', $password)) {
            throw new \RuntimeException("La contraseña debe contener al menos una letra minúscula");
        }
        if (!preg_match('/[0-9]/', $password)) {
            throw new \RuntimeException("La contraseña debe contener al menos un número");
        }
        if (!preg_match('/[@$!%*?&#.,;:\-_+]/', $password)) {
            throw new \RuntimeException("La contraseña debe contener al menos un carácter especial (@$!%*?&#.,;:-_+)");
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

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
