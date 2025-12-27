<?php

/**
 * AuthMiddleware
 *
 * Provides static methods for authentication and authorization checks.
 * Used to protect routes and API endpoints based on user session and role.
 */

namespace Shared\Infrastructure\Middleware;

class AuthMiddleware
{
    /**
     * Ensures that the current user is authenticated and has an 'Admin' role.
     * Redirects to the login page if not authenticated, or to the home page if not an admin.
     * @return void
     */
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

    /**
     * Ensures that the current user is authenticated.
     * Redirects to the login page if not authenticated.
     * @return void
     */
    public static function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder a esta página';
            header('Location: /login');
            exit;
        }
    }


    /**
     * Ensures that the current user is authenticated for API access.
     * Returns a 401 Unauthorized JSON response if not authenticated.
     * @return void
     */
    public static function apiRequireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }
    }


    /**
     * Ensures that the current user is authenticated and has an 'Admin' role for API access.
     * Returns a 403 Forbidden JSON response if not authenticated or not an admin.
     * @return void
     */
    public static function apiRequireAdmin(): void
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Prohibido']);
            exit;
        }
    }



    /**
     * Ensures that the current user is authenticated and has a 'Client' role.
     * Redirects to the login page if not authenticated, or to the home page if not a client.
     * @return void
     */
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
