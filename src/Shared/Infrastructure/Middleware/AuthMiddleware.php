<?php

namespace Shared\Infrastructure\Middleware;

class AuthMiddleware
{
    public static function requireAdmin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder al panel de administración';
            header('Location: /login');
            exit;
        }

        if ($_SESSION['role'] !== 'Admin') {
            $_SESSION['error'] = 'No tienes permisos para acceder al panel de administración';
            header('Location: /');
            exit;
        }
    }

    public static function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder a esta página';
            header('Location: /login');
            exit;
        }
    }
}
