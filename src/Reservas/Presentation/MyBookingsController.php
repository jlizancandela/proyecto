<?php

namespace Reservas\Presentation;

use Latte\Engine;
use Reservas\Infrastructure\ReservaRepository;

class MyBookingsController
{
    private Engine $latte;
    private ReservaRepository $reservaRepository;

    public function __construct(Engine $latte, ReservaRepository $reservaRepository)
    {
        $this->latte = $latte;
        $this->reservaRepository = $reservaRepository;
    }

    public function index()
    {
        // Get pagination parameters
        $page = (int)($_GET['page'] ?? 1);
        $limit = 6; // Bookings per page
        $offset = ($page - 1) * $limit;

        // Get user ID from session
        $userId = $_SESSION['user_id'] ?? null;
        
        if (!$userId) {
            // Redirect to login if no user in session
            header('Location: /login');
            exit;
        }

        // Get user bookings with pagination
        $bookings = $this->reservaRepository->findByUserId($userId, $limit, $offset);
        $totalBookings = $this->reservaRepository->countByUserId($userId);
        $totalPages = ceil($totalBookings / $limit);

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Bookings.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/user/reservas',
                'bookings' => $bookings,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalBookings' => $totalBookings
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
            $booking = $this->reservaRepository->findById($bookingId);
            if (!$booking || $booking->id_cliente !== $userId) {
                $_SESSION['error'] = 'No tienes permisos para cancelar esta reserva';
                header('Location: /user/reservas');
                exit;
            }

            // Cancelar la reserva
            $success = $this->reservaRepository->updateStatus($bookingId, 'Cancelada');
            
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
            $booking = $this->reservaRepository->findById($bookingId);
            if (!$booking || $booking->id_cliente !== $userId) {
                $_SESSION['error'] = 'No tienes permisos para modificar esta reserva';
                header('Location: /user/reservas');
                exit;
            }

            // Cancelar la reserva actual
            $success = $this->reservaRepository->updateStatus($bookingId, 'Cancelada');
            
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