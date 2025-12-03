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

    /**
     * Registra un nuevo usuario en el sistema
     * 
     * @param array{
     *     nombre: string,
     *     apellidos: string,
     *     email: string,
     *     password: string,
     *     telefono?: string,
     *     rol?: string
     * } $userData
     * @return Usuario
     * @throws \RuntimeException
     */
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
            rol: $userData['rol'] ?? UserRole::Cliente->value,
            nombre: $userData['nombre'],
            apellidos: $userData['apellidos'],
            email: $userData['email'],
            password_hash: $passwordHash,
            telefono: $userData['telefono'] ?? null
        );

        $this->userService->setUser($user);

        return $user;
    }

    /**
     * Autentica un usuario con email y contraseña
     * 
     * @param string $email
     * @param string $password
     * @return Usuario|null
     */
    public function login(string $email, string $password): ?Usuario
    {
        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            return null;
        }

        return password_verify($password, $user->getPassword()) ? $user : null;
    }



    /**
     * Inicia una sesión para el usuario autenticado
     * 
     * @param Usuario $user
     * @return void
     */
    public function startSession(Usuario $user): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user->getId();
        $_SESSION['email'] = $user->getEmail();
        $_SESSION['role'] = $user->getRol();
        $_SESSION['name'] = $user->getNombre();

        session_regenerate_id(true);
    }

    /**
     * Cierra la sesión del usuario actual
     * 
     * @return void
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();
    }

    /**
     * Obtiene el usuario actualmente autenticado
     * 
     * @return Usuario|null
     */
    public function getCurrentUser(): ?Usuario
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id'])
            ? $this->userRepository->getUserById($_SESSION['user_id'])
            : null;
    }

    /**
     * Verifica si hay un usuario autenticado
     * 
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']);
    }

    /**
     * Cambia la contraseña de un usuario
     * 
     * @param int $userId
     * @param string $oldPassword
     * @param string $newPassword
     * @return bool
     * @throws \RuntimeException
     */
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

    /**
     * Verifica si el usuario actual tiene un rol específico
     * 
     * @param UserRole $role
     * @return bool
     */
    public function hasRole(UserRole $role): bool
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            return false;
        }

        return $user->getRol() === $role;
    }
}
