<?php

/**
 * BookingApiController
 *
 * Handles API requests related to user bookings, allowing clients to create and retrieve their reservations.
 */

namespace Reservas\Presentation;

use Reservas\Application\ReservaService;

class BookingApiController
{
    private ReservaService $reservaService;

    /**
     * BookingApiController constructor.
     * @param ReservaService $reservaService The booking service instance.
     */
    public function __construct(ReservaService $reservaService)
    {
        $this->reservaService = $reservaService;
    }

    /**
     * Creates a new booking for the authenticated user.
     * Receives booking data via JSON POST request.
     *
     * @return void
     */
    public function createReserva(): void
    {
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos inválidos']);
                return;
            }

            $data['id_cliente'] = (int) $_SESSION['user_id'];

            $bookingId = $this->reservaService->createReserva($data);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'id_reserva' => $bookingId,
                'message' => 'Reserva creada exitosamente'
            ]);
        } catch (\RuntimeException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log("Error en createReserva: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    /**
     * Retrieves all bookings for the authenticated user.
     * Supports pagination via limit and offset query parameters.
     *
     * @return void
     */
    public function getReservas(): void
    {
        header('Content-Type: application/json');

        try {
            $clientId = (int) $_SESSION['user_id'];
            $limit = (int) ($_GET['limit'] ?? 50);
            $offset = (int) ($_GET['offset'] ?? 0);

            $bookings = $this->reservaService->getReservasByClient($clientId, $limit, $offset);

            $bookingsData = [];
            foreach ($bookings as $booking) {
                if (method_exists($booking, 'toArray')) {
                    $bookingsData[] = $booking->toArray();
                } else {
                    $bookingsData[] = $booking;
                }
            }

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'reservas' => $bookingsData,
                'total' => count($bookingsData)
            ]);
        } catch (\Exception $e) {
            error_log("Error en getReservas: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
}
