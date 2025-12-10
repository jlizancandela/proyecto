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
}
