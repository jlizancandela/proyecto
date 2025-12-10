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
            // Verificar que el usuario esté autenticado
            if (!isset($_SESSION['user_id'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Usuario no autenticado']);
                return;
            }

            // Obtener datos del request
            $data = json_decode(file_get_contents('php://input'), true);

            // Validar datos requeridos
            if (!isset($data['servicio_id'], $data['especialista_id'], $data['fecha'], $data['hora'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Faltan datos requeridos']);
                return;
            }

            $idCliente = (int) $_SESSION['user_id'];
            $idEspecialista = (int) $data['especialista_id'];
            $idServicio = (int) $data['servicio_id'];
            $fecha = $data['fecha'];
            $horaInicio = $data['hora'];
            
            // Calcular hora fin basada en la duración del servicio
            // Necesitamos obtener la duración del servicio
            $duracion = $data['duracion'] ?? 60; // Por defecto 60 minutos
            $horaFin = date('H:i:s', strtotime($horaInicio) + ($duracion * 60));

            // Verificar conflictos de horario
            $hayConflicto = $this->reservaRepository->findConflicts(
                $fecha,
                $horaInicio,
                $horaFin,
                $idEspecialista
            );

            if ($hayConflicto) {
                http_response_code(409);
                echo json_encode(['error' => 'El horario seleccionado ya no está disponible']);
                return;
            }

            // Crear la reserva
            $reserva = new Reserva(
                $idCliente,
                $idEspecialista,
                $idServicio,
                $fecha,
                $horaInicio,
                $horaFin,
                'Pendiente', // Estado inicial
                $data['observaciones'] ?? null
            );

            $idReserva = $this->reservaRepository->addReserva($reserva);

            if ($idReserva) {
                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'id_reserva' => $idReserva,
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
}
