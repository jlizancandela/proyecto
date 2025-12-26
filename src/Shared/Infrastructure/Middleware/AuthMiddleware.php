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


    public static function apiRequireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    }


    public static function apiRequireAdmin(): void
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Prohibido']);
            exit;
        }
    }



    public static function requireClient(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder a esta página';
            header('Location: /login');
            exit;
        }

        if ($_SESSION['role'] !== 'Cliente') {
            $_SESSION['error'] = 'No tienes permisos para acceder a esta página';
            header('Location: /');
            exit;
        }
    }

    /**
     * Requires user to be authenticated and have Especialista role
     * @return void
     */
    public static function requireSpecialist(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder al panel de especialista';
            header('Location: /login');
            exit;
        }

        if ($_SESSION['role'] !== 'Especialista') {
            $_SESSION['error'] = 'No tienes permisos para acceder al panel de especialista';
            header('Location: /');
            exit;
        }
    }
}
