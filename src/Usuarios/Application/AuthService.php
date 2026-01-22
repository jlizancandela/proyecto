<?php

/**
 * All security and login features for users are handled in this service.
 */

namespace Usuarios\Application;

use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;
use Usuarios\Infrastructure\UserRepository;
use Usuarios\Infrastructure\PasswordResetRepository;
use Respect\Validation\Validator as v;
use Shared\Domain\Exceptions\InvalidPasswordException;
use Shared\Domain\Exceptions\InvalidEmailException;
use Shared\Domain\Exceptions\InvalidUserDataException;
use Shared\Domain\Exceptions\InvalidUserException;

/**
 * Authentication and session management service.
 *
 * Handles user registration, login, logout, and credential validation.
 * Implements password validation rules and PHP session management.
 */
class AuthService
{
    private UserRepository $userRepository;
    private UserService $userService;
    private PasswordResetRepository $passwordResetRepository;

    private const TOKEN_EXPIRATION_SECONDS = 3600;

    /**
     * AuthService constructor.
     *
     * @param UserRepository $userRepository Repository for user data operations.
     * @param UserService $userService Service for high-level user management.
     * @param PasswordResetRepository $passwordResetRepository Repository for password reset tokens.
     */
    public function __construct(
        UserRepository $userRepository,
        UserService $userService,
        PasswordResetRepository $passwordResetRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * Registers a new user by validating data and checking for unique email.
     *
     * Validates data format, checks that the email is not registered,
     * hashes the password, and creates the user in the database.
     *
     * @param array $userData User data (name, surname, email, password, phone, role)
     * @return Usuario Created user
     * @throws InvalidEmailException If the email is already registered
     * @throws InvalidUserDataException If user data is invalid
     * @throws InvalidPasswordException If password does not meet requirements
     */
    public function register(array $userData): Usuario
    {
        $this->validateUserData($userData);

        $result = $this->userRepository->getUserByEmail($userData['email']);
        if ($result) {
            throw new InvalidEmailException('El email ya está registrado');
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

    /**
     * Validates the format and content of user data.
     *
     * Verifies that name, surname, email, and password meet requirements.
     * Delegates password validation to validatePassword().
     *
     * @param array $userData Data to validate
     * @return void
     * @throws InvalidUserDataException If any data does not meet validation rules
     * @throws InvalidPasswordException If password does not meet requirements
     */
    private function validateUserData(array $userData): void
    {
        $validator = v::key('nombre', v::stringType()->notEmpty()->length(2, 50))
            ->key('apellidos', v::stringType()->notEmpty()->length(2, 100))
            ->key('email', v::email())
            ->key('password', v::stringType()->notEmpty())
            ->key('telefono', v::optional(v::phone()), false)
            ->key('rol', v::optional(v::in(['Admin', 'Especialista', 'Cliente'])), false);

        try {
            $validator->assert($userData);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            throw new InvalidUserDataException($e->getMessage());
        }

        $this->validatePassword($userData['password']);
    }

    /**
     * Validates that the password meets security requirements.
     *
     * Requirements: minimum 6 characters.
     *
     * @param string $password Password to validate
     * @return void
     * @throws InvalidPasswordException If password does not meet requirements
     */
    private function validatePassword(string $password): void
    {
        $passwordValidator = v::length(6, null);

        try {
            $passwordValidator->assert($password);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            throw new InvalidPasswordException('La contraseña debe tener al menos 6 caracteres');
        }
    }

    /**
     * Authenticates a user by verifying email and password.
     *
     * Finds the user by email and verifies the password matches
     * using password_verify().
     *
     * @param string $email User email
     * @param string $password Plain text password
     * @return Usuario|null User if credentials are correct, null otherwise
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
     * Starts a PHP session for the authenticated user.
     *
     * Stores user data in $_SESSION and regenerates the session ID
     * for security.
     *
     * @param Usuario $user Authenticated user
     * @return void
     */
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

    /**
     * Logs out the current user.
     *
     * Clears all session variables and destroys the PHP session.
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
     * Retrieves the currently authenticated user.
     *
     * Finds the user in the database using the ID stored in the session.
     *
     * @return Usuario|null Current user or null if no active session
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
     * Checks if there is an authenticated user.
     *
     * @return bool True if there is an active session with user_id
     */
    public function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']);
    }

    /**
     * Changes a user's password verifying the current password.
     *
     * Validates that the current password is correct before updating.
     * The new password is hashed before saving.
     *
     * @param int $userId User ID
     * @param string $oldPassword Current password in plain text
     * @param string $newPassword New password in plain text
     * @return bool True if changed successfully, false if current password is incorrect
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
     * Checks if the current user has a specific role.
     *
     * @param UserRole $role Role to check
     * @return bool True if the user has the specified role
     */
    public function hasRole(UserRole $role): bool
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            return false;
        }

        return $user->getRol() === $role;
    }

    /**
     * Generates a password reset token for a user.
     *
     * Creates a unique token using random_bytes and sets an expiration
     * of 1 hour. The token is saved in the database.
     *
     * @param string $email User email
     * @return string Generated token
     * @throws InvalidUserException If the user does not exist
     */
    public function generatePasswordResetToken(string $email): string
    {
        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            throw new InvalidUserException("Usuario no encontrado");
        }

        $token = bin2hex(random_bytes(32));

        // Set expiration to 1 hour
        $expiration = date('Y-m-d H:i:s', time() + self::TOKEN_EXPIRATION_SECONDS);

        $this->passwordResetRepository->savePasswordResetToken($user->getId(), $token, $expiration);

        return $token;
    }

    /**
     * Validates a password reset token.
     *
     * Verifies that the token exists and has not expired.
     *
     * @param string $token Reset token
     * @return Usuario|null User if token is valid, null otherwise
     */
    public function validateResetToken(string $token): ?Usuario
    {
        if (empty($token)) {
            return null;
        }

        $user = $this->passwordResetRepository->getUserByResetToken($token);
        if (!$user) {
            return null;
        }

        $expiration = $this->userRepository->getResetTokenExpiration($user->getId());
        $isExpired = !$expiration || strtotime($expiration) < time();

        return $isExpired ? null : $user;
    }

    /**
     * Resets a user's password using a valid token.
     *
     * Validates the token, validates the new password, hashes it,
     * updates it in the database, and clears the token.
     *
     * @param string $token Reset token
     * @param string $newPassword New password in plain text
     * @return bool True if reset successfully, false if token is invalid
     * @throws InvalidPasswordException If password does not meet requirements
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        $user = $this->validateResetToken($token);

        if (!$user) {
            return false;
        }

        $this->validatePassword($newPassword);

        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $user->setPassword($newPasswordHash);
        $this->userService->updateUser($user);

        $this->passwordResetRepository->clearResetToken($user->getId());

        return true;
    }
}
