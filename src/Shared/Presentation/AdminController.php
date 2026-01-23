<?php

/**
 * Main entrance for all admin-only pages in the hair salon app.
 */

namespace Shared\Presentation;

use Latte\Engine;
use Usuarios\Application\UserService;
use Usuarios\Presentation\Transformers\UserTransformer;
use Shared\Infrastructure\Pagination\Paginator;
use Servicios\Application\ServicioService;

/**
 * Controls the display of the main admin dashboard and settings.
 */
class AdminController
{
    private Engine $latte;
    private ?UserService $userService;
    private ?ServicioService $servicioService;
    private ?\Reservas\Application\ReservaService $reservaService;
    private ?\Especialistas\Infrastructure\EspecialistaServicioRepository $especialistaServicioRepository;
    private ?\Especialistas\Infrastructure\EspecialistaRepository $especialistaRepository;

    /**
     * AdminController constructor.
     *
     * @param Engine $latte The Latte templating engine instance.
     * @param UserService|null $userService The user management service.
     * @param ServicioService|null $servicioService The salon services service.
     * @param \Reservas\Application\ReservaService|null $reservaService The booking management service.
     * @param \Especialistas\Infrastructure\EspecialistaServicioRepository|null $especialistaServicioRepository
     * @param \Especialistas\Infrastructure\EspecialistaRepository|null $especialistaRepository
     */
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

    /**
     * Renders the main admin dashboard page.
     *
     * @return string The rendered HTML content.
     */
    public function index(): string
    {
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        $info = $_SESSION['info'] ?? null;
        unset($_SESSION['success'], $_SESSION['error'], $_SESSION['info']);

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Admin.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'admin'),
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/admin',
                'success' => $success,
                'error' => $error,
                'info' => $info
            ]
        );
    }

    /**
     * Renders the user management page with filtering and pagination.
     *
     * @return string The rendered HTML content.
     */
    public function usersManagement(): string
    {
        $limit = 10;
        $page = (int) ($_GET['page'] ?? 1);
        $offset = ($page - 1) * $limit;
        $filters = $this->getUsersFilters();

        $users = $this->userService->getAllUsersWithFilters($filters, $limit, $offset);
        $total = $this->userService->countAllUsersWithFilters($filters);

        $totalPages = (int) ceil($total / $limit);
        $usersArray = $this->enrichUsersWithServices(UserTransformer::toArrayCollection($users));

        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        $info = $_SESSION['info'] ?? null;
        unset($_SESSION['success'], $_SESSION['error'], $_SESSION['info']);

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/UsersManagement.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'users' => $usersArray,
                'page' => $page,
                'totalPages' => $totalPages,
                'search' => $_GET['search'] ?? '',
                'total' => $total,
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/admin/users',
                'availableServices' => $this->servicioService->getAllServices(),
                'success' => $success,
                'error' => $error,
                'info' => $info
            ]
        );
    }

    /**
     * Renders the services management page.
     *
     * @return string The rendered HTML content.
     */
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

        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        $info = $_SESSION['info'] ?? null;
        unset($_SESSION['success'], $_SESSION['error'], $_SESSION['info']);

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/ServicesManagement.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'servicios' => $serviciosData,
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/admin/services',
                'success' => $success,
                'error' => $error,
                'info' => $info
            ]
        );
    }

    /**
     * Renders the bookings management page with filtering and pagination.
     *
     * @return string The rendered HTML content.
     */
    public function bookingsManagement(): string
    {
        $limit = 10;
        $page = (int) ($_GET['page'] ?? 1);
        $offset = ($page - 1) * $limit;
        $filtros = $this->getBookingsFilters();

        $reservas = $this->reservaService->getAllReservasWithFilters($filtros, $limit, $offset);
        $total = $this->reservaService->countAllReservasWithFilters($filtros);
        $totalPages = (int) ceil($total / $limit);

        $reservasData = array_map(fn($reserva) => $reserva->toArray(), $reservas);

        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        $info = $_SESSION['info'] ?? null;
        unset($_SESSION['success'], $_SESSION['error'], $_SESSION['info']);

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
                'services' => $this->servicioService->getAllServices(),
                'success' => $success,
                'error' => $error,
                'info' => $info
            ]
        );
    }

    /**
     * Builds the filters array from the request.
     * @return array
     */
    private function getUsersFilters(): array
    {
        $filters = [];
        $search = trim($_GET['search'] ?? '');
        $rol = trim($_GET['rol'] ?? '');
        $sort = trim($_GET['sort'] ?? '');
        $order = trim($_GET['order'] ?? 'asc');

        if (!empty($search)) {
            $filters['search'] = $search;
        }
        if (!empty($rol)) {
            $filters['rol'] = $rol;
        }
        if (!empty($sort)) {
            $filters['sort'] = $sort;
        }
        if (!empty($order)) {
            $filters['order'] = $order;
        }
        if (isset($_GET['estado']) && $_GET['estado'] !== '') {
            $filters['estado'] = $_GET['estado'];
        }

        return $filters;
    }

    /**
     * Enriches user data with services if they are specialists.
     * @param array $usersArray
     * @return array
     */
    private function enrichUsersWithServices(array $usersArray): array
    {
        if (!$this->especialistaServicioRepository || !$this->especialistaRepository) {
            return $usersArray;
        }

        foreach ($usersArray as &$userData) {
            $userData['servicios'] = [];
            if ($userData['rol'] === 'Especialista') {
                $especialistaId = $this->especialistaRepository->getEspecialistaIdByUserId($userData['id']);
                if ($especialistaId) {
                    $servicios = $this->especialistaServicioRepository->getServiciosForEspecialista($especialistaId);
                    $userData['servicios'] = array_map(fn($s) => $s->getNombreServicio(), $servicios);
                }
            }
        }

        return $usersArray;
    }

    /**
     * Builds the filters array for bookings from the request.
     * @return array
     */
    private function getBookingsFilters(): array
    {
        $filtros = [];

        $keys = ['cliente', 'especialista', 'estado', 'fecha_desde', 'fecha_hasta', 'sort', 'order', 'search'];
        foreach ($keys as $key) {
            if (!empty($_GET[$key])) {
                if ($key === 'search') {
                    $filtros['cliente_search'] = trim($_GET[$key]);
                } else {
                    $filtros[$key] = ($key === 'cliente' || $key === 'especialista') ? (int) $_GET[$key] : trim($_GET[$key]);
                }
            }
        }

        return $filtros;
    }
}
