<?php

namespace Usuarios\Presentation;

use Latte\Engine;
use Usuarios\Application\AuthService;
use Shared\Infrastructure\Email\EmailService;

class AuthController
{
    private Engine $latte;
    private AuthService $authService;
    private EmailService $emailService;

    public function __construct(
        Engine $latte,
        AuthService $authService,
        EmailService $emailService
    ) {
        $this->latte = $latte;
        $this->authService = $authService;
        $this->emailService = $emailService;
    }

    public function showLogin(): string
    {
        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Login.latte',
            [
                'success' => $_SESSION['login_success'] ?? null,
                'error' => $_SESSION['login_error'] ?? null,
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/login'
            ]
        );
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Por favor, completa todos los campos';
            header('Location: /login');
            exit;
        }

        $user = $this->authService->login($email, $password);

        if ($user === null) {
            $_SESSION['login_error'] = 'Email o contraseña incorrectos';
            header('Location: /login');
            exit;
        }

        $this->authService->startSession($user);
        unset($_SESSION['login_error']);

        header('Location: /');
        exit;
    }

    public function logout(): void
    {
        $this->authService->logout();
        header('Location: /login');
        exit;
    }

    public function showRegister(): string
    {
        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Register.latte',
            [
                'error' => $_SESSION['register_error'] ?? null,
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/register'
            ]
        );
    }

    public function register(): void
    {
        error_log("=== REGISTRO: Método llamado ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("No es POST, redirigiendo...");
            header('Location: /register');
            exit;
        }

        error_log("POST recibido: " . print_r($_POST, true));

        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password-confirm'] ?? '';

        if ($password !== $passwordConfirm) {
            error_log("Contraseñas no coinciden");
            $_SESSION['register_error'] = 'Las contraseñas no coinciden';
            header('Location: /register');
            exit;
        }

        try {
            error_log("Intentando registrar usuario...");
            $user = $this->authService->register([
                'nombre' => $_POST['nombre'] ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'email' => $_POST['email'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'password' => $password,
                'rol' => 'Cliente'
            ]);

            error_log("Usuario registrado exitosamente: " . $user->getEmail());
            $this->authService->startSession($user);
            unset($_SESSION['register_error']);

            error_log("Redirigiendo a /");
            header('Location: /');
            exit;
        } catch (\Exception $e) {
            error_log("Error en registro: " . $e->getMessage());
            $_SESSION['register_error'] = $e->getMessage();
            header('Location: /register');
            exit;
        }
    }

    /**
     * Muestra el formulario de recuperación de contraseña
     */
    public function showForgotPasswordForm(): string
    {
        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/ForgotPassword.latte',
            [
                'error' => $_SESSION['forgot_error'] ?? null,
                'success' => $_SESSION['forgot_success'] ?? null,
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/forgot-password'
            ]
        );
    }

    /**
     * Procesa el envío del enlace de recuperación
     */
    public function sendResetLink(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /forgot-password');
            exit;
        }

        $email = $_POST['email'] ?? '';

        if (empty($email)) {
            $_SESSION['forgot_error'] = 'Por favor, ingresa tu email';
            header('Location: /forgot-password');
            exit;
        }

        try {
            // Generar token
            $token = $this->authService->generatePasswordResetToken($email);

            // Construir URL de reset usando APP_URL del .env
            $baseUrl = $_ENV['APP_URL'] ?? 'https://proyecto.ddev.site';
            $resetUrl = "{$baseUrl}/reset-password?token={$token}";

            // Enviar email
            $this->emailService->sendPasswordRecoveryEmail($email, $resetUrl);

            // Mensaje genérico para prevenir enumeración de usuarios
            $_SESSION['forgot_success'] = 'Si el email existe, recibirás instrucciones para recuperar tu contraseña';
            unset($_SESSION['forgot_error']);
        } catch (\Exception $e) {
            // No revelar si el usuario existe o no
            $_SESSION['forgot_success'] = 'Si el email existe, recibirás instrucciones para recuperar tu contraseña';
            unset($_SESSION['forgot_error']);
        }

        header('Location: /forgot-password');
        exit;
    }

    /**
     * Muestra el formulario de cambio de contraseña
     */
    public function showResetPasswordForm(): string
    {
        $token = $_GET['token'] ?? '';

        // Validar token
        $user = $this->authService->validateResetToken($token);

        if (!$user) {
            $_SESSION['forgot_error'] = 'El enlace de recuperación es inválido o ha expirado';
            header('Location: /forgot-password');
            exit;
        }

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/ResetPassword.latte',
            [
                'token' => $token,
                'error' => $_SESSION['reset_error'] ?? null,
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/reset-password'
            ]
        );
    }

    /**
     * Procesa el cambio de contraseña
     */
    public function resetPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /forgot-password');
            exit;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password-confirm'] ?? '';

        if (empty($password) || empty($passwordConfirm)) {
            $_SESSION['reset_error'] = 'Por favor, completa todos los campos';
            header("Location: /reset-password?token={$token}");
            exit;
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['reset_error'] = 'Las contraseñas no coinciden';
            header("Location: /reset-password?token={$token}");
            exit;
        }

        try {
            $success = $this->authService->resetPassword($token, $password);

            if (!$success) {
                $_SESSION['forgot_error'] = 'El enlace de recuperación es inválido o ha expirado';
                header('Location: /forgot-password');
                exit;
            }

            $_SESSION['login_success'] = 'Contraseña actualizada correctamente. Inicia sesión con tu nueva contraseña';
            unset($_SESSION['reset_error']);
            header('Location: /login');
            exit;
        } catch (\Exception $e) {
            $_SESSION['reset_error'] = $e->getMessage();
            header("Location: /reset-password?token={$token}");
            exit;
        }
    }
}
