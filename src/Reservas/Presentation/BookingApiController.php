<?php

namespace Reservas\Presentation;

use Latte\Engine;
use Reservas\Infrastructure\ReservaRepository;
use Reservas\Domain\Reserva;

class BookingApiController
{
    private ReservaRepository $reservaRepository;

    public function __construct(ReservaRepository $reservaRepository)
    {
        $this->reservaRepository = $reservaRepository;
    }

    public function createReserva(): void
    {
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!isset($data['servicio_id'], $data['especialista_id'], $data['fecha'], $data['hora'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Faltan datos requeridos']);
                return;
            }

            $clientId = (int) $_SESSION['user_id'];
            $specialistId = (int) $data['especialista_id'];
            $serviceId = (int) $data['servicio_id'];
            $date = $data['fecha'];
            $startTime = $data['hora'];
            
            $duration = $data['duracion'] ?? 60;
            $endTime = date('H:i:s', strtotime($startTime) + ($duration * 60));

            $hasConflict = $this->reservaRepository->findConflicts(
                $date,
                $startTime,
                $endTime,
                $specialistId
            );

            if ($hasConflict) {
                http_response_code(409);
                echo json_encode(['error' => 'El horario seleccionado ya no está disponible']);
                return;
            }

            // Evitar que el mismo cliente tenga dos reservas al mismo tiempo
            $clientConflict = $this->reservaRepository->findClientConflicts(
                $date,
                $startTime,
                $endTime,
                $clientId
            );

            if ($clientConflict) {
                http_response_code(409);
                echo json_encode(['error' => 'Ya tienes otra reserva en ese horario']);
                return;
            }

            $booking = new Reserva(
                $clientId,
                $specialistId,
                $serviceId,
                $date,
                $startTime,
                $endTime,
                'Pendiente',
                $data['observaciones'] ?? null
            );

            $bookingId = $this->reservaRepository->addReserva($booking);

            if ($bookingId) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'id_reserva' => $bookingId,
                    'message' => 'Reserva creada exitosamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Error al crear la reserva']);
            }
        } catch (\Exception $e) {
            error_log("Error en createReserva: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }

    public function getReservas(): void
    {
        header('Content-Type: application/json');

        try {
            $clientId = (int) $_SESSION['user_id'];
            $limit = (int) ($_GET['limit'] ?? 50);
            $offset = (int) ($_GET['offset'] ?? 0);
            
            $bookings = $this->reservaRepository->findByClient($clientId, $limit, $offset);

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
