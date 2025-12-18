<?php

namespace Usuarios\Presentation;

use Latte\Engine;
use Usuarios\Application\AuthService;

class AuthController
{
    private Engine $latte;
    private AuthService $authService;

    public function __construct(Engine $latte, AuthService $authService)
    {
        $this->latte = $latte;
        $this->authService = $authService;
    }

    public function showLogin(): string
    {
        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Login.latte',
            [
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
}
