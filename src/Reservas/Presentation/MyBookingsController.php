<?php

namespace Reservas\Presentation;

use Latte\Engine;
use Reservas\Application\ReservaService;

class MyBookingsController
{
    private Engine $latte;
    private ReservaService $reservaService;

    public function __construct(Engine $latte, ReservaService $reservaService)
    {
        $this->latte = $latte;
        $this->reservaService = $reservaService;
    }

    public function index()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = 6;
        $offset = ($page - 1) * $limit;

        // Get optional filter parameters
        $fechaDesde = $_GET['fecha_desde'] ?? null;
        $fechaHasta = $_GET['fecha_hasta'] ?? null;
        $estado = $_GET['estado'] ?? null;

        // Convert empty strings to null
        if ($fechaDesde === '') {
            $fechaDesde = null;
        }
        if ($fechaHasta === '') {
            $fechaHasta = null;
        }
        if ($estado === '') {
            $estado = null;
        }

        // Validate fecha_desde format if provided
        if ($fechaDesde && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDesde)) {
            $fechaDesde = null; // Invalid format, ignore
        }

        // Validate fecha_hasta format if provided
        if ($fechaHasta && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaHasta)) {
            $fechaHasta = null; // Invalid format, ignore
        }

        // Validate estado if provided
        $validEstados = ['Pendiente', 'Confirmada', 'Completada', 'Cancelada'];
        if ($estado && !in_array($estado, $validEstados)) {
            $estado = null; // Invalid status, ignore
        }

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        // Get user bookings with pagination and filters
        $bookings = $this->reservaService->getAllReservasByFilter(
            $userId,
            $limit,
            $offset,
            $fechaDesde,
            $fechaHasta,
            $estado
        );

        $totalBookings = $this->reservaService->countReservasByFilter(
            $userId,
            $fechaDesde,
            $fechaHasta,
            $estado
        );

        $totalPages = ceil($totalBookings / $limit);

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Bookings.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/user/reservas',
                'bookings' => $bookings,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalBookings' => $totalBookings,
                'filters' => [
                    'fecha_desde' => $fechaDesde,
                    'fecha_hasta' => $fechaHasta,
                    'estado' => $estado
                ]
            ]
        );
    }

    public function cancel(int $bookingId): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        try {
            // Verificar que la reserva pertenece al usuario
            $booking = $this->reservaService->getReservaById($bookingId);
            if (!$booking || $booking->id_cliente !== $userId) {
                $_SESSION['error'] = 'No tienes permisos para cancelar esta reserva';
                header('Location: /user/reservas');
                exit;
            }

            // Cancelar la reserva
            $success = $this->reservaService->updateReservaStatus($bookingId, 'Cancelada');

            if ($success) {
                $_SESSION['success'] = 'Reserva cancelada exitosamente';
            } else {
                $_SESSION['error'] = 'Error al cancelar la reserva';
            }
        } catch (\Exception $e) {
            error_log("Error al cancelar reserva: " . $e->getMessage());
            $_SESSION['error'] = 'Error interno al cancelar la reserva';
        }

        header('Location: /user/reservas');
        exit;
    }

    public function modify(int $bookingId): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        try {
            // Verificar que la reserva pertenece al usuario
            $booking = $this->reservaService->getReservaById($bookingId);
            if (!$booking || $booking->id_cliente !== $userId) {
                $_SESSION['error'] = 'No tienes permisos para modificar esta reserva';
                header('Location: /user/reservas');
                exit;
            }

            // Cancelar la reserva actual
            $success = $this->reservaService->updateReservaStatus($bookingId, 'Cancelada');

            if ($success) {
                $_SESSION['info'] = 'Reserva anterior cancelada. Puedes crear una nueva reserva.';
                header('Location: /user/reservas/nueva');
            } else {
                $_SESSION['error'] = 'Error al procesar la modificación';
                header('Location: /user/reservas');
            }
        } catch (\Exception $e) {
            error_log("Error al modificar reserva: " . $e->getMessage());
            $_SESSION['error'] = 'Error interno al modificar la reserva';
            header('Location: /user/reservas');
        }
        exit;
    }
}
