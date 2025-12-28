<?php

/**
 * Page for customers to see and manage their own hair salon appointments.
 */

namespace Reservas\Presentation;

use Latte\Engine;
use Reservas\Application\ReservaService;

/**
 * Shows the history of bookings for the logged-in client.
 */
class MyBookingsController
{
    private Engine $latte;
    private ReservaService $reservaService;

    private const ITEMS_PER_PAGE = 6;

    /**
     * MyBookingsController constructor.
     * @param Engine $latte The Latte templating engine instance.
     * @param ReservaService $reservaService The booking service instance.
     */
    public function __construct(Engine $latte, ReservaService $reservaService)
    {
        $this->latte = $latte;
        $this->reservaService = $reservaService;
    }

    /**
     * Muestra el listado paginado de reservas del usuario con filtros opcionales
     *
     * Permite filtrar por rango de fechas (desde/hasta) y estado de la reserva.
     * Los filtros se mantienen en la URL para persistir al cambiar de página.
     *
     * @return string HTML renderizado de la página de reservas
     */
    public function index()
    {
        $page = (int)($_GET['page'] ?? 1);
        $limit = self::ITEMS_PER_PAGE;
        $offset = ($page - 1) * $limit;

        $fromDate = $_GET['fecha_desde'] ?? null;
        $toDate = $_GET['fecha_hasta'] ?? null;
        $status = $_GET['estado'] ?? null;

        if ($fromDate === '') {
            $fromDate = null;
        }
        if ($toDate === '') {
            $toDate = null;
        }
        if ($status === '') {
            $status = null;
        }

        if ($fromDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fromDate)) {
            $fromDate = null;
        }

        if ($toDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $toDate)) {
            $toDate = null;
        }

        $validStatuses = ['Pendiente', 'Confirmada', 'Completada', 'Cancelada'];
        if ($status && !in_array($status, $validStatuses)) {
            $status = null;
        }

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $bookings = $this->reservaService->getAllReservasByFilter(
            $userId,
            $limit,
            $offset,
            $fromDate,
            $toDate,
            $status
        );

        $totalBookings = $this->reservaService->countReservasByFilter(
            $userId,
            $fromDate,
            $toDate,
            $status
        );

        $totalPages = ceil($totalBookings / $limit);

        $pdfUrl = '/user/reservas/pdf';
        $filterParams = array_filter([
            'fecha_desde' => $fromDate,
            'fecha_hasta' => $toDate,
            'estado' => $status
        ]);
        if (!empty($filterParams)) {
            $pdfUrl .= '?' . http_build_query($filterParams);
        }

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
                    'fecha_desde' => $fromDate,
                    'fecha_hasta' => $toDate,
                    'estado' => $status
                ],
                'pdfUrl' => $pdfUrl
            ]
        );
    }

    /**
     * Cancela una reserva verificando permisos del usuario
     *
     * Valida que la reserva pertenezca al usuario logueado antes de cancelar.
     * Redirige a la lista de reservas con mensaje de éxito o error.
     *
     * @param int $bookingId ID de la reserva a cancelar
     * @return void
     */
    public function cancel(int $bookingId): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        try {
            $booking = $this->reservaService->getReservaById($bookingId);
            if (!$booking || $booking->id_cliente !== $userId) {
                $_SESSION['error'] = 'No tienes permisos para cancelar esta reserva';
                header('Location: /user/reservas');
                exit;
            }

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

    /**
     * Modifica una reserva cancelándola y redirigiendo al formulario de nueva reserva
     *
     * Cancela la reserva actual y redirige al usuario al formulario para crear
     * una nueva reserva con los mismos datos base.
     *
     * @param int $bookingId ID de la reserva a modificar
     * @return void
     */
    public function modify(int $bookingId): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        try {
            $booking = $this->reservaService->getReservaById($bookingId);
            if (!$booking || $booking->id_cliente !== $userId) {
                $_SESSION['error'] = 'No tienes permisos para modificar esta reserva';
                header('Location: /user/reservas');
                exit;
            }

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
