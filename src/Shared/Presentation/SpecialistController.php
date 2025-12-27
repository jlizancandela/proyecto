<?php

/**
 * SpecialistController
 *
 * Handles specialist panel pages, including dashboard, booking management, and profile views.
 * Interacts with `EspecialistaRepository` and `ReservaRepository` to fetch and display data.
 */

namespace Shared\Presentation;

use Latte\Engine;
use Especialistas\Infrastructure\EspecialistaRepository;
use Reservas\Infrastructure\ReservaRepository;

class SpecialistController
{
    private Engine $latte;
    private EspecialistaRepository $especialistaRepository;
    private ReservaRepository $reservaRepository;

    /**
     * SpecialistController constructor.
     *
     * @param Engine $latte The Latte templating engine instance.
     * @param EspecialistaRepository $especialistaRepository The repository for specialist data.
     * @param ReservaRepository $reservaRepository The repository for booking data.
     */
    public function __construct(
        Engine $latte,
        EspecialistaRepository $especialistaRepository,
        ReservaRepository $reservaRepository
    ) {
        $this->latte = $latte;
        $this->especialistaRepository = $especialistaRepository;
        $this->reservaRepository = $reservaRepository;
    }

    /**
     * Shows specialist dashboard with KPIs and quick actions
     * @return string
     */
    public function index(): string
    {
        $userId = $_SESSION['user_id'];
        $especialistaId = $this->especialistaRepository->getEspecialistaIdByUserId($userId);

        if (!$especialistaId) {
            $_SESSION['error'] = 'No se encontró el perfil de especialista';
            header('Location: /');
            exit;
        }

        $totalBookings = $this->reservaRepository->countByEspecialistaIdWithFilters($especialistaId);

        $today = date('Y-m-d');
        $nextMonth = date('Y-m-d', strtotime('+30 days'));
        $upcomingBookings = $this->reservaRepository->countByEspecialistaIdWithFilters(
            $especialistaId,
            $today,
            $nextMonth
        );

        $bookings = $this->reservaRepository->findByEspecialistaIdWithFilters(
            $especialistaId,
            1000,
            0,
            $today,
            $nextMonth
        );

        $estimatedRevenue = 0;
        foreach ($bookings as $booking) {
            if ($booking->estado !== 'Cancelada') {
                $estimatedRevenue += $booking->servicio_precio ?? 0;
            }
        }

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Specialist.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Especialista'),
                'userPhoto' => $this->getSpecialistPhoto($userId),
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/specialist',
                'totalBookings' => $totalBookings,
                'upcomingBookings' => $upcomingBookings,
                'estimatedRevenue' => number_format($estimatedRevenue, 2, '.', '')
            ]
        );
    }

    /**
     * Shows specialist bookings with filters and pagination
     * @return string
     */
    public function bookings(): string
    {
        $userId = $_SESSION['user_id'];
        $especialistaId = $this->especialistaRepository->getEspecialistaIdByUserId($userId);

        if (!$especialistaId) {
            $_SESSION['error'] = 'No se encontró el perfil de especialista';
            header('Location: /');
            exit;
        }

        $limit = 10;
        $page = (int) ($_GET['page'] ?? 1);
        $offset = ($page - 1) * $limit;

        $fechaDesde = $_GET['fecha_desde'] ?? null;
        $fechaHasta = $_GET['fecha_hasta'] ?? null;
        $estado = $_GET['estado'] ?? null;
        $clienteSearch = $_GET['cliente'] ?? null;

        $reservas = $this->reservaRepository->findByEspecialistaIdWithFilters(
            $especialistaId,
            $limit,
            $offset,
            $fechaDesde,
            $fechaHasta,
            $estado,
            $clienteSearch
        );

        $total = $this->reservaRepository->countByEspecialistaIdWithFilters(
            $especialistaId,
            $fechaDesde,
            $fechaHasta,
            $estado,
            $clienteSearch
        );

        $totalPages = (int) ceil($total / $limit);

        $reservasData = array_map(fn($reserva) => $reserva->toArray(), $reservas);

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/SpecialistBookings.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Especialista'),
                'userPhoto' => $this->getSpecialistPhoto($userId),
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/specialist/bookings',
                'reservas' => $reservasData,
                'page' => $page,
                'totalPages' => $totalPages,
                'total' => $total,
                'filtros' => [
                    'fecha_desde' => $fechaDesde,
                    'fecha_hasta' => $fechaHasta,
                    'estado' => $estado,
                    'cliente' => $clienteSearch
                ]
            ]
        );
    }

    /**
     * Shows specialist profile with services
     * @return string
     */
    public function profile(): string
    {
        $userId = $_SESSION['user_id'];
        $profileData = $this->especialistaRepository->getEspecialistaProfileWithServices($userId);

        if (!$profileData) {
            $_SESSION['error'] = 'No se encontró el perfil de especialista';
            header('Location: /');
            exit;
        }

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/SpecialistProfile.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Especialista'),
                'userPhoto' => $profileData['foto_url'] ?? null,
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/specialist/profile',
                'profile' => $profileData
            ]
        );
    }

    /**
     * Gets specialist photo URL
     * @param int $userId
     * @return string|null
     */
    private function getSpecialistPhoto(int $userId): ?string
    {
        $profile = $this->especialistaRepository->getEspecialistaProfileWithServices($userId);
        return $profile['foto_url'] ?? null;
    }
}
