<?php

namespace Reservas\Presentation;

use Reservas\Application\ReservaService;

/**
 * Admin API controller for booking management
 */
class BookingAdminApiController
{
    private ReservaService $reservaService;

    public function __construct(ReservaService $reservaService)
    {
        $this->reservaService = $reservaService;
    }

    /**
     * Gets all bookings with optional filters
     *
     * @return void
     */
    public function getAllBookings(): void
    {
        header('Content-Type: application/json');

        try {
            $limit = (int) ($_GET['limit'] ?? 10);
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

            $bookings = $this->reservaService->getAllReservasWithFilters($filtros, $limit, $offset);
            $total = $this->reservaService->countAllReservasWithFilters($filtros);
            $totalPages = (int) ceil($total / $limit);

            $bookingsData = [];
            foreach ($bookings as $booking) {
                $bookingsData[] = [
                    'id_reserva' => $booking->id_reserva,
                    'cliente' => [
                        'id' => $booking->id_cliente,
                        'nombre' => $booking->cliente_nombre,
                        'apellidos' => $booking->cliente_apellidos,
                        'email' => $booking->cliente_email,
                        'telefono' => $booking->cliente_telefono
                    ],
                    'especialista' => [
                        'id' => $booking->id_especialista,
                        'nombre' => $booking->especialista_nombre,
                        'apellidos' => $booking->especialista_apellidos,
                        'email' => $booking->especialista_email,
                        'telefono' => $booking->especialista_telefono
                    ],
                    'servicio' => [
                        'id' => $booking->id_servicio,
                        'nombre' => $booking->nombre_servicio,
                        'duracion' => $booking->duracion_minutos,
                        'precio' => $booking->precio
                    ],
                    'fecha_reserva' => $booking->fecha_reserva,
                    'hora_inicio' => $booking->hora_inicio,
                    'hora_fin' => $booking->hora_fin,
                    'estado' => $booking->estado,
                    'observaciones' => $booking->observaciones,
                    'fecha_creacion' => $booking->fecha_creacion
                ];
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'reservas' => $bookingsData,
                'total' => $total,
                'page' => $page,
                'totalPages' => $totalPages
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            error_log("Error getting all bookings: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Gets a single booking by ID
     *
     * @param int $id Booking ID
     * @return void
     */
    public function getBookingById(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $booking = $this->reservaService->getReservaById($id);

            if (!$booking) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Reserva no encontrada'
                ], JSON_PRETTY_PRINT);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => [
                    'id_reserva' => $booking->id_reserva,
                    'cliente' => [
                        'id' => $booking->id_cliente,
                        'nombre' => $booking->cliente_nombre,
                        'apellidos' => $booking->cliente_apellidos,
                        'email' => $booking->cliente_email,
                        'telefono' => $booking->cliente_telefono
                    ],
                    'especialista' => [
                        'id' => $booking->id_especialista,
                        'nombre' => $booking->especialista_nombre,
                        'apellidos' => $booking->especialista_apellidos
                    ],
                    'servicio' => [
                        'id' => $booking->id_servicio,
                        'nombre' => $booking->nombre_servicio,
                        'duracion' => $booking->duracion_minutos,
                        'precio' => $booking->precio
                    ],
                    'fecha_reserva' => $booking->fecha_reserva,
                    'hora_inicio' => $booking->hora_inicio,
                    'hora_fin' => $booking->hora_fin,
                    'estado' => $booking->estado,
                    'observaciones' => $booking->observaciones,
                    'fecha_creacion' => $booking->fecha_creacion
                ]
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            error_log("Error getting booking: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Creates a new booking
     *
     * @return void
     */
    public function createBooking(): void
    {
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos'], JSON_PRETTY_PRINT);
                return;
            }

            $bookingId = $this->reservaService->createReserva($data);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'id_reserva' => $bookingId,
                'message' => 'Reserva creada exitosamente'
            ], JSON_PRETTY_PRINT);
        } catch (\RuntimeException $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            error_log("Error creating booking: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Updates an existing booking
     *
     * @param int $id Booking ID
     * @return void
     */
    public function updateBooking(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos'], JSON_PRETTY_PRINT);
                return;
            }

            $success = $this->reservaService->updateReserva($id, $data);

            if ($success) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Reserva actualizada exitosamente'
                ], JSON_PRETTY_PRINT);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Error al actualizar la reserva'
                ], JSON_PRETTY_PRINT);
            }
        } catch (\RuntimeException $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            error_log("Error updating booking: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Deletes a booking
     *
     * @param int $id Booking ID
     * @return void
     */
    public function deleteBooking(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $success = $this->reservaService->deleteReserva($id);

            if ($success) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => 'Reserva eliminada exitosamente'
                ], JSON_PRETTY_PRINT);
            } else {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Reserva no encontrada'
                ], JSON_PRETTY_PRINT);
            }
        } catch (\Exception $e) {
            error_log("Error deleting booking: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], JSON_PRETTY_PRINT);
        }
    }
}
