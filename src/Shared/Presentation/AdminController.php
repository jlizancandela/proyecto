<?php

namespace Shared\Presentation;

use Latte\Engine;
use Usuarios\Application\UserService;
use Usuarios\Presentation\Transformers\UserTransformer;
use Shared\Infrastructure\Pagination\Paginator;
use Servicios\Application\ServicioService;

class AdminController
{
    private Engine $latte;
    private ?UserService $userService;
    private ?ServicioService $servicioService;

    public function __construct(
        Engine $latte,
        ?UserService $userService = null,
        ?ServicioService $servicioService = null
    ) {
        $this->latte = $latte;
        $this->userService = $userService;
        $this->servicioService = $servicioService;
    }

    public function index(): string
    {
        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Admin.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'admin'),
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/admin'
            ]
        );
    }

    public function usersManagement(): string
    {
        $limit = 10;
        $page = (int) ($_GET['page'] ?? 1);
        $search = trim($_GET['search'] ?? '');
        $offset = ($page - 1) * $limit;

        if (!empty($search)) {
            $users = $this->userService->searchUsers($search, $limit, $offset);
            $total = $this->userService->getTotalSearchResults($search);
        } else {
            $users = $this->userService->getAllUsers($limit, $offset);
            $total = $this->userService->getTotalUsers();
        }

        $totalPages = (int) ceil($total / $limit);

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/UsersManagement.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'users' => UserTransformer::toArrayCollection($users),
                'page' => $page,
                'totalPages' => $totalPages,
                'search' => $search,
                'total' => $total,
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/admin/users'
            ]
        );
    }

    public function servicesManagement(): string
    {
        $servicios = $this->servicioService->getAllServices();

        $serviciosData = array_map(function ($servicio) {
            return [
                'id' => $servicio->getIdServicio(),
                'nombre_servicio' => $servicio->getNombreServicio(),
                'descripcion' => $servicio->getDescripcion(),
                'duracion_minutos' => $servicio->getDuracionMinutos(),
                'precio' => $servicio->getPrecio()
            ];
        }, $servicios);

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/ServicesManagement.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'servicios' => $serviciosData,
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/admin/services'
            ]
        );
    }
}
