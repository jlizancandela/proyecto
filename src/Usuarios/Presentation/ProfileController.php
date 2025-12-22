<?php

namespace Usuarios\Presentation;

use Latte\Engine;
use Usuarios\Application\UserService;

class ProfileController
{
    private Engine $latte;
    private UserService $userService;

    public function __construct(Engine $latte, UserService $userService)
    {
        $this->latte = $latte;
        $this->userService = $userService;
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $user = $this->userService->getUserById($_SESSION['user_id']);

        if (!$user) {
            header('Location: /login');
            exit;
        }

        $data = [
            'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
            'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/user/profile',
            'user' => $user
        ];

        if (isset($_SESSION['success_message'])) {
            $data['successMessage'] = $_SESSION['success_message'];
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            $data['errorMessage'] = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        }

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Profile.latte',
            $data
        );
    }

    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user/profile');
            exit;
        }

        try {
            $user = $this->userService->getUserById($_SESSION['user_id']);

            if (!$user) {
                $_SESSION['error_message'] = 'Usuario no encontrado';
                header('Location: /user/profile');
                exit;
            }

            $nombre = trim($_POST['nombre'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;

            $user->setNombre($nombre);
            $user->setApellidos($apellidos);
            $user->setEmail($email);
            $user->setTelefono($telefono);

            $this->userService->updateUser($user);

            $_SESSION['name'] = $nombre;
            $_SESSION['success_message'] = 'Perfil actualizado correctamente';

            header('Location: /user/profile');
            exit;
        } catch (\RuntimeException $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header('Location: /user/profile');
            exit;
        } catch (\Exception $e) {
            error_log('Error updating profile: ' . $e->getMessage());
            $_SESSION['error_message'] = 'Error al actualizar el perfil. Por favor, inténtalo de nuevo.';
            header('Location: /user/profile');
            exit;
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user/profile');
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];

            //Baja lógica: desactivar usuario en lugar de eliminar
            $this->userService->deactivateUser($userId);

            session_destroy();

            header('Location: /login?message=account_deactivated');
            exit;
        } catch (\Exception $e) {
            error_log('Error deactivating account: ' . $e->getMessage());
            $_SESSION['error_message'] = 'Error al desactivar la cuenta. Por favor, inténtalo de nuevo.';
            header('Location: /user/profile');
            exit;
        }
    }
}
