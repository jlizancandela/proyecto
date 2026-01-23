<?php

/**
 * Controller for specialists to manage their own bookings.
 */

namespace Reservas\Presentation;

use Reservas\Application\ReservaService;
use Especialistas\Infrastructure\EspecialistaRepository;

/**
 * Specialist API controller for booking management
 */
class BookingSpecialistApiController
{
    private ReservaService $reservaService;
    private EspecialistaRepository $especialistaRepository;

    /**
     * BookingSpecialistApiController constructor.
     * @param ReservaService $reservaService The booking service instance.
     * @param EspecialistaRepository $especialistaRepository The specialist repository instance.
     */
    public function __construct(ReservaService $reservaService, EspecialistaRepository $especialistaRepository)
    {
        $this->reservaService = $reservaService;
        $this->especialistaRepository = $especialistaRepository;
    }

    /**
     * Updates the status of a booking assigned to the logged-in specialist.
     * Only allows transitioning to 'Completada' or 'Cancelada'.
     *
     * @param int $id Booking ID
     * @return void
     */
    public function updateStatus(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $userId = $_SESSION['user_id'];
            $especialistaId = $this->especialistaRepository->getEspecialistaIdByUserId($userId);

            if (!$especialistaId) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Perfil de especialista no encontrado']);
                return;
            }

            $booking = $this->reservaService->getReservaById($id);

            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Reserva no encontrada']);
                return;
            }

            // Verify the booking belongs to this specialist
            if ($booking->id_especialista !== $especialistaId) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'No tienes permiso para modificar esta reserva']);
                return;
            }

            $data = json_decode(file_get_contents('php://input'), true);
            $newStatus = $data['estado'] ?? null;

            $allowedStatuses = ['Completada', 'Cancelada'];
            if (!in_array($newStatus, $allowedStatuses)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'error' => 'Estado no permitido. Solo se puede marcar como Completada o Cancelada.'
                ]);
                return;
            }

            $success = $this->reservaService->updateReservaStatus($id, $newStatus);

            if ($success) {
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'message' => "Reserva marcada como $newStatus exitosamente"
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Error al actualizar el estado']);
            }
        } catch (\Exception $e) {
            error_log("Error in BookingSpecialistApiController::updateStatus: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Error interno del servidor']);
        }
    }
}
