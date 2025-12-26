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
    private ?\Reservas\Application\ReservaService $reservaService;
    private ?\Especialistas\Infrastructure\EspecialistaServicioRepository $especialistaServicioRepository;
    private ?\Especialistas\Infrastructure\EspecialistaRepository $especialistaRepository;

    public function __construct(
        Engine $latte,
        ?UserService $userService = null,
        ?ServicioService $servicioService = null,
        ?\Reservas\Application\ReservaService $reservaService = null,
        ?\Especialistas\Infrastructure\EspecialistaServicioRepository $especialistaServicioRepository = null,
        ?\Especialistas\Infrastructure\EspecialistaRepository $especialistaRepository = null
    ) {
        $this->latte = $latte;
        $this->userService = $userService;
        $this->servicioService = $servicioService;
        $this->reservaService = $reservaService;
        $this->especialistaServicioRepository = $especialistaServicioRepository;
        $this->especialistaRepository = $especialistaRepository;
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
        $rol = trim($_GET['rol'] ?? '');
        $sort = trim($_GET['sort'] ?? '');
        $order = trim($_GET['order'] ?? 'asc');
        $offset = ($page - 1) * $limit;

        // Construir array de filtros
        $filters = [];
        if (!empty($search)) $filters['search'] = $search;
        if (!empty($rol)) $filters['rol'] = $rol;
        if (!empty($sort)) $filters['sort'] = $sort;
        if (!empty($order)) $filters['order'] = $order;
        if (isset($_GET['estado']) && $_GET['estado'] !== '') $filters['estado'] = $_GET['estado'];

        // Obtener usuarios filtrados y total
        $users = $this->userService->getAllUsersWithFilters($filters, $limit, $offset);
        $total = $this->userService->countAllUsersWithFilters($filters);

        $totalPages = (int) ceil($total / $limit);

        $usersArray = UserTransformer::toArrayCollection($users);

        // Agregar servicios para especialistas
        if ($this->especialistaServicioRepository && $this->especialistaRepository) {
            foreach ($usersArray as &$userData) {
                if ($userData['rol'] === 'Especialista') {
                    $especialistaId = $this->especialistaRepository->getEspecialistaIdByUserId($userData['id']);
                    if ($especialistaId) {
                        $servicios = $this->especialistaServicioRepository->getServiciosForEspecialista($especialistaId);
                        $userData['servicios'] = array_map(fn($s) => $s->getNombreServicio(), $servicios);
                    } else {
                        $userData['servicios'] = [];
                    }
                } else {
                    $userData['servicios'] = [];
                }
            }
        }

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/UsersManagement.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'users' => $usersArray,
                'page' => $page,
                'totalPages' => $totalPages,
                'search' => $search,
                'total' => $total,
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/admin/users',
                'availableServices' => $this->servicioService->getAllServices()
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
                'precio' => $servicio->getPrecio(),
                'activo' => $servicio->isActivo()
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

    public function bookingsManagement(): string
    {
        $limit = 10;
        $page = (int) ($_GET['page'] ?? 1);
        $offset = ($page - 1) * $limit;

        $filtros = [];

        if (!empty($_GET['cliente'])) {
            $filtros['cliente'] = (int) $_GET['cliente'];
        }

        if (!empty($_GET['especialista'])) {
            $filtros['especialista'] = (int) $_GET['especialista'];
        }

        if (!empty($_GET['estado'])) {
            $filtros['estado'] = trim($_GET['estado']);
        }

        if (!empty($_GET['fecha_desde'])) {
            $filtros['fecha_desde'] = trim($_GET['fecha_desde']);
        }

        if (!empty($_GET['fecha_hasta'])) {
            $filtros['fecha_hasta'] = trim($_GET['fecha_hasta']);
        }

        if (!empty($_GET['sort'])) {
            $filtros['sort'] = trim($_GET['sort']);
        }

        if (!empty($_GET['order'])) {
            $filtros['order'] = trim($_GET['order']);
        }

        $reservas = $this->reservaService->getAllReservasWithFilters($filtros, $limit, $offset);
        $total = $this->reservaService->countAllReservasWithFilters($filtros);
        $totalPages = (int) ceil($total / $limit);

        $reservasData = array_map(fn($reserva) => $reserva->toArray(), $reservas);

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/BookingsManagement.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'reservas' => $reservasData,
                'page' => $page,
                'totalPages' => $totalPages,
                'total' => $total,
                'filtros' => $filtros,
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/admin/bookings',
                'clients' => $this->userService->getUsersByRole(\Usuarios\Domain\UserRole::Cliente->value, 1000),
                'specialists' => $this->especialistaRepository->getAllEspecialistasWithUserData(),
                'services' => $this->servicioService->getAllServices()
            ]
        );
    }
}
