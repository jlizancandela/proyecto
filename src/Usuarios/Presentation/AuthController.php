<?php

namespace Usuarios\Presentation;

use Latte\Engine;
use Usuarios\Application\AuthService;
use Usuarios\Application\UserService;
use Shared\Infrastructure\Email\EmailService;

class AuthController
{
    private Engine $latte;
    private AuthService $authService;
    private EmailService $emailService;
    private ?UserService $userService;

    public function __construct(
        Engine $latte,
        AuthService $authService,
        EmailService $emailService,
        UserService $userService = null
    ) {
        $this->latte = $latte;
        $this->authService = $authService;
        $this->emailService = $emailService;
        $this->userService = $userService;
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

        if (!$user->getActivo()) {
            $_SESSION['inactive_user_id'] = $user->getId();
            $_SESSION['inactive_user_email'] = $user->getEmail();
            header('Location: /reactivate');
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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }

        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password-confirm'] ?? '';

        if ($password !== $passwordConfirm) {
            $_SESSION['register_error'] = 'Las contraseñas no coinciden';
            header('Location: /register');
            exit;
        }

        try {
            $user = $this->authService->register([
                'nombre' => $_POST['nombre'] ?? '',
                'apellidos' => $_POST['apellidos'] ?? '',
                'email' => $_POST['email'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'password' => $password,
                'rol' => 'Cliente'
            ]);

            $this->authService->startSession($user);
            unset($_SESSION['register_error']);

            header('Location: /');
            exit;
        } catch (\Exception $e) {
            $_SESSION['register_error'] = $e->getMessage();
            header('Location: /register');
            exit;
        }
    }

    /**
     * Shows the password recovery form.
     * @return string Rendered HTML for the forgot password page.
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
     * Processes the password reset link request.
     * @return void
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
            $token = $this->authService->generatePasswordResetToken($email);
            $baseUrl = $_ENV['APP_URL'] ?? 'https://proyecto.ddev.site';
            $resetUrl = "{$baseUrl}/reset-password?token={$token}";
            $this->emailService->sendPasswordRecoveryEmail($email, $resetUrl);

            $_SESSION['forgot_success'] = 'Si el email existe, recibirás instrucciones para recuperar tu contraseña';
            unset($_SESSION['forgot_error']);
        } catch (\Exception $e) {
            $_SESSION['forgot_success'] = 'Si el email existe, recibirás instrucciones para recuperar tu contraseña';
            unset($_SESSION['forgot_error']);
        }

        header('Location: /forgot-password');
        exit;
    }

    /**
     * Shows the password reset form.
     * @return string Rendered HTML for the reset password page.
     */
    public function showResetPasswordForm(): string
    {
        $token = $_GET['token'] ?? '';
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
     * Processes the password reset request.
     * @return void
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

    /**
     * Shows the account reactivation page.
     * @return string Rendered HTML for the reactivation page.
     */
    public function showReactivate(): string
    {
        if (!isset($_SESSION['inactive_user_id'])) {
            header('Location: /login');
            exit;
        }

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Reactivate.latte',
            [
                'email' => $_SESSION['inactive_user_email'] ?? 'tu cuenta',
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/reactivate'
            ]
        );
    }

    /**
     * Processes the account reactivation request.
     * @return void
     */
    public function reactivate(): void
    {
        if (!isset($_SESSION['inactive_user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /reactivate');
            exit;
        }

        try {
            $userId = $_SESSION['inactive_user_id'];
            $this->userService->activateUser($userId);
            $user = $this->userService->getUserById($userId);

            if ($user) {
                $this->authService->startSession($user);
                unset($_SESSION['inactive_user_id']);
                unset($_SESSION['inactive_user_email']);

                $_SESSION['success_message'] = '¡Bienvenido de nuevo! Tu cuenta ha sido reactivada.';
                header('Location: /user');
                exit;
            }

            $_SESSION['login_error'] = 'Error al reactivar la cuenta';
            header('Location: /login');
            exit;
        } catch (\Exception $e) {
            error_log('Error reactivating account: ' . $e->getMessage());
            $_SESSION['login_error'] = 'Error al reactivar la cuenta. Por favor, inténtalo de nuevo.';
            header('Location: /login');
            exit;
        }
    }
}
