<?php

/**
 * Service for managing bookings with business logic and validations.
 */

namespace Reservas\Application;

use Reservas\Domain\Reserva;
use Reservas\Infrastructure\ReservaRepository;
use Respect\Validation\Validator as v;

class ReservaService
{
    private ReservaRepository $reservaRepository;

    /**
     * @param ReservaRepository $reservaRepository
     */
    public function __construct(ReservaRepository $reservaRepository)
    {
        $this->reservaRepository = $reservaRepository;
    }

    /**
     * Creates a new booking with validation and business rules.
     *
     * @param array $data Booking data
     * @return int Created booking ID
     * @throws \RuntimeException If validation fails or conflicts exist
     */
    public function createReserva(array $data): int
    {
        $this->validateReservaData($data);

        $bookingData = $this->prepareBookingData($data);

        $this->validateNoConflicts(
            $bookingData['date'],
            $bookingData['startTime'],
            $bookingData['endTime'],
            $bookingData['specialistId'],
            $bookingData['clientId']
        );

        $this->validateWeeklyLimit(
            $bookingData['clientId'],
            $bookingData['serviceId'],
            $bookingData['date']
        );

        $reserva = new Reserva(
            $bookingData['clientId'],
            $bookingData['specialistId'],
            $bookingData['serviceId'],
            $bookingData['date'],
            $bookingData['startTime'],
            $bookingData['endTime'],
            'Pendiente',
            $bookingData['observaciones']
        );

        $reservaId = $this->reservaRepository->addReserva($reserva);

        if (!$reservaId) {
            throw new \RuntimeException('Error al crear la reserva');
        }

        return $reservaId;
    }

    /**
     * Updates an existing booking with validation.
     *
     * @param int $reservaId Booking ID
     * @param array $data Updated booking data
     * @return bool True if updated successfully
     * @throws \RuntimeException If validation fails
     */
    public function updateReserva(int $reservaId, array $data): bool
    {
        $existingReserva = $this->reservaRepository->findById($reservaId);

        if (!$existingReserva) {
            throw new \RuntimeException('Reserva no encontrada');
        }

        $this->validateReservaData($data);

        $bookingData = $this->prepareBookingData($data);

        $this->validateNoConflictsForUpdate(
            $bookingData['date'],
            $bookingData['startTime'],
            $bookingData['endTime'],
            $bookingData['specialistId'],
            $bookingData['clientId'],
            $reservaId
        );

        $reserva = new Reserva(
            $bookingData['clientId'],
            $bookingData['specialistId'],
            $bookingData['serviceId'],
            $bookingData['date'],
            $bookingData['startTime'],
            $bookingData['endTime'],
            $data['estado'] ?? 'Pendiente',
            $bookingData['observaciones'],
            $existingReserva->fecha_creacion,
            $reservaId
        );

        return $this->reservaRepository->updateReserva($reserva);
    }

    /**
     * Deletes a booking.
     *
     * @param int $reservaId Booking ID
     * @return bool True if deleted successfully
     */
    public function deleteReserva(int $reservaId): bool
    {
        return $this->reservaRepository->deleteReserva($reservaId);
    }

    /**
     * Updates booking status with validation.
     *
     * @param int $reservaId Booking ID
     * @param string $newStatus New status
     * @return bool True if updated successfully
     * @throws \RuntimeException If status is invalid
     */
    public function updateReservaStatus(int $reservaId, string $newStatus): bool
    {
        $validStatuses = ['Pendiente', 'Confirmada', 'Completada', 'Cancelada'];

        if (!in_array($newStatus, $validStatuses)) {
            throw new \RuntimeException('Estado de reserva inválido');
        }

        return $this->reservaRepository->updateStatus($reservaId, $newStatus);
    }

    /**
     * Gets bookings for a specific client.
     *
     * @param int $clientId Client ID
     * @param int $limit Max results
     * @param int $offset Pagination offset
     * @return array Array of bookings
     */
    public function getReservasByClient(int $clientId, int $limit = 50, int $offset = 0): array
    {
        return $this->reservaRepository->findByClient($clientId, $limit, $offset);
    }

    /**
     * Gets a booking by ID with complete data.
     *
     * @param int $id Booking ID
     * @return ReservaCompletaDTO|null Complete booking DTO or null
     */
    public function getReservaById(int $id)
    {
        return $this->reservaRepository->getReservaCompletaById($id);
    }

    /**
     * Gets user bookings with optional filters.
     *
     * @param int $userId User ID
     * @param int $limit Max results
     * @param int $offset Pagination offset
     * @param string|null $fechaDesde Start date filter
     * @param string|null $fechaHasta End date filter
     * @param string|null $estado Status filter
     * @return array Array of complete booking DTOs
     */
    public function getAllReservasByFilter(
        int $userId,
        int $limit = 50,
        int $offset = 0,
        ?string $fechaDesde = null,
        ?string $fechaHasta = null,
        ?string $estado = null
    ): array {
        return $this->reservaRepository->findByUserIdWithFilters(
            $userId,
            $limit,
            $offset,
            $fechaDesde,
            $fechaHasta,
            $estado
        );
    }

    /**
     * Counts user bookings with optional filters.
     *
     * @param int $userId User ID
     * @param string|null $fechaDesde Start date filter
     * @param string|null $fechaHasta End date filter
     * @param string|null $estado Status filter
     * @return int Total count
     */
    public function countReservasByFilter(
        int $userId,
        ?string $fechaDesde = null,
        ?string $fechaHasta = null,
        ?string $estado = null
    ): int {
        return $this->reservaRepository->countByUserIdWithFilters(
            $userId,
            $fechaDesde,
            $fechaHasta,
            $estado
        );
    }

    /**
     * Gets the latest booking for a user.
     *
     * @param int $userId User ID
     * @return ReservaCompletaDTO|null Latest booking or null
     */
    public function getLatestReserva(int $userId)
    {
        return $this->reservaRepository->findLatestByUserId($userId);
    }

    /**
     * Gets all bookings with optional filters (admin).
     *
     * @param array $filtros Filters array
     * @param int $limit Max results
     * @param int $offset Pagination offset
     * @return array Array of complete booking DTOs
     */
    public function getAllReservasWithFilters(
        array $filtros = [],
        int $limit = 50,
        int $offset = 0
    ): array {
        return $this->reservaRepository->findAllFiltered($filtros, $limit, $offset);
    }

    /**
     * Counts all bookings with optional filters (admin).
     *
     * @param array $filtros Filters array
     * @return int Total count
     */
    public function countAllReservasWithFilters(array $filtros = []): int
    {
        return $this->reservaRepository->countAllFiltered($filtros);
    }

    /**
     * Validates booking data format and content.
     *
     * @param array $data Data to validate
     * @return void
     * @throws \RuntimeException If validation fails
     */
    private function validateReservaData(array $data): void
    {
        $validator = v::key('servicio_id', v::intVal()->positive())
            ->key('especialista_id', v::intVal()->positive())
            ->key('fecha', v::date('Y-m-d'))
            ->key('hora', v::regex('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/'))
            ->key('id_cliente', v::intVal()->positive())
            ->key('duracion', v::optional(v::intVal()->positive()->min(15)->max(480)), false)
            ->key('observaciones', v::optional(v::stringType()->length(null, 500)), false);

        try {
            $validator->assert($data);
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            throw new \RuntimeException($e->getMessage());
        }

        $reservaDate = new \DateTime($data['fecha']);
        $today = new \DateTime('today');

        if ($reservaDate < $today) {
            throw new \RuntimeException('La fecha de reserva debe ser futura');
        }
    }

    /**
     * Prepares and normalizes booking data.
     *
     * @param array $data Raw booking data
     * @return array Normalized booking data
     */
    private function prepareBookingData(array $data): array
    {
        $startTime = $data['hora'];
        $duration = $data['duracion'] ?? 60;
        $endTime = date('H:i:s', strtotime($startTime) + ($duration * 60));

        return [
            'clientId' => (int) $data['id_cliente'],
            'specialistId' => (int) $data['especialista_id'],
            'serviceId' => (int) $data['servicio_id'],
            'date' => $data['fecha'],
            'startTime' => $startTime,
            'endTime' => $endTime,
            'observaciones' => $data['observaciones'] ?? null
        ];
    }

    /**
     * Validates no time conflicts exist for new booking.
     *
     * @param string $date Booking date
     * @param string $startTime Start time
     * @param string $endTime End time
     * @param int $specialistId Specialist ID
     * @param int $clientId Client ID
     * @return void
     * @throws \RuntimeException If conflicts exist
     */
    private function validateNoConflicts(
        string $date,
        string $startTime,
        string $endTime,
        int $specialistId,
        int $clientId
    ): void {
        $hasConflict = $this->reservaRepository->findConflicts(
            $date,
            $startTime,
            $endTime,
            $specialistId
        );

        if ($hasConflict) {
            throw new \RuntimeException('El horario seleccionado ya no está disponible');
        }

        $clientConflict = $this->reservaRepository->findClientConflicts(
            $date,
            $startTime,
            $endTime,
            $clientId
        );

        if ($clientConflict) {
            throw new \RuntimeException('Ya tienes otra reserva en ese horario');
        }
    }

    /**
     * Validates no time conflicts exist for booking update.
     *
     * @param string $date Booking date
     * @param string $startTime Start time
     * @param string $endTime End time
     * @param int $specialistId Specialist ID
     * @param int $clientId Client ID
     * @param int $excludeReservaId Booking ID to exclude from check
     * @return void
     * @throws \RuntimeException If conflicts exist
     */
    private function validateNoConflictsForUpdate(
        string $date,
        string $startTime,
        string $endTime,
        int $specialistId,
        int $clientId,
        int $excludeReservaId
    ): void {
        $hasConflict = $this->reservaRepository->findConflicts(
            $date,
            $startTime,
            $endTime,
            $specialistId,
            $excludeReservaId
        );

        if ($hasConflict) {
            throw new \RuntimeException('El horario seleccionado ya no está disponible');
        }

        $clientConflict = $this->reservaRepository->findClientConflicts(
            $date,
            $startTime,
            $endTime,
            $clientId,
            $excludeReservaId
        );

        if ($clientConflict) {
            throw new \RuntimeException('El cliente ya tiene otra reserva en ese horario');
        }
    }

    /**
     * Validates client weekly booking limit.
     *
     * @param int $clientId Client ID
     * @param int $serviceId Service ID
     * @param string $date Booking date
     * @return void
     * @throws \RuntimeException If weekly limit exceeded
     */
    private function validateWeeklyLimit(int $clientId, int $serviceId, string $date): void
    {
        $reservaDate = new \DateTime($date);
        $weekStart = (clone $reservaDate)->modify('monday this week')->format('Y-m-d');
        $weekEnd = (clone $reservaDate)->modify('sunday this week')->format('Y-m-d');

        $weeklyReservas = $this->reservaRepository->findAllFiltered([
            'cliente' => $clientId,
            'servicio' => $serviceId,
            'fecha_desde' => $weekStart,
            'fecha_hasta' => $weekEnd
        ]);

        $activeReservas = array_filter($weeklyReservas, function ($reserva) {
            return $reserva->estado !== 'Cancelada';
        });

        if (count($activeReservas) > 0) {
            throw new \RuntimeException('Ya tienes una reserva de este servicio en esta semana');
        }
    }
}
