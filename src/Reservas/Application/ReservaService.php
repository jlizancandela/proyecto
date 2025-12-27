<?php

namespace Reservas\Application;

use Reservas\Domain\Reserva;
use Reservas\Infrastructure\ReservaRepository;
use Respect\Validation\Validator as v;

/**
 * Servicio de gestión de reservas
 * 
 * Implementa la lógica de negocio para crear, validar y gestionar reservas.
 * Aplica reglas de validación, verifica conflictos de horario y límites semanales.
 */
class ReservaService
{
    private ReservaRepository $reservaRepository;

    /**
     * ReservaService constructor.
     * @param ReservaRepository $reservaRepository The booking repository instance.
     */
    public function __construct(ReservaRepository $reservaRepository)
    {
        $this->reservaRepository = $reservaRepository;
    }

    /**
     * Crea una nueva reserva validando datos y reglas de negocio
     * 
     * Valida formato de datos, verifica que no haya conflictos de horario
     * con el especialista o el cliente, y comprueba el límite semanal de reservas.
     * 
     * @param array $data Datos de la reserva (servicio_id, especialista_id, fecha, hora, etc.)
     * @return int ID de la reserva creada
     * @throws \RuntimeException Si los datos son inválidos, hay conflictos o se excede el límite
     */
    public function createReserva(array $data): int
    {
        $this->validateReservaData($data);

        $clientId = (int) $data['id_cliente'];
        $specialistId = (int) $data['especialista_id'];
        $serviceId = (int) $data['servicio_id'];
        $date = $data['fecha'];
        $startTime = $data['hora'];
        $duration = $data['duracion'] ?? 60;
        $endTime = date('H:i:s', strtotime($startTime) + ($duration * 60));

        $this->validateNoConflicts($date, $startTime, $endTime, $specialistId, $clientId);
        $this->validateWeeklyLimit($clientId, $serviceId, $date);

        $reserva = new Reserva(
            $clientId,
            $specialistId,
            $serviceId,
            $date,
            $startTime,
            $endTime,
            'Pendiente',
            $data['observaciones'] ?? null
        );

        $reservaId = $this->reservaRepository->addReserva($reserva);

        if (!$reservaId) {
            throw new \RuntimeException('Error al crear la reserva');
        }

        return $reservaId;
    }

    /**
     * Valida el formato y contenido de los datos de la reserva
     * 
     * Verifica que todos los campos requeridos estén presentes y tengan
     * el formato correcto. También valida que la fecha sea futura.
     * 
     * @param array $data Datos a validar
     * @return void
     * @throws \RuntimeException Si algún dato es inválido o la fecha es pasada
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
     * Verifica que no existan conflictos de horario
     * 
     * Comprueba que el especialista no tenga otra reserva en el mismo horario
     * y que el cliente tampoco tenga otra cita a la misma hora.
     * 
     * @param string $date Fecha de la reserva (Y-m-d)
     * @param string $startTime Hora de inicio (H:i:s)
     * @param string $endTime Hora de fin (H:i:s)
     * @param int $specialistId ID del especialista
     * @param int $clientId ID del cliente
     * @return void
     * @throws \RuntimeException Si existe conflicto de horario
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
     * Valida que el cliente no exceda el límite de reservas semanales
     * 
     * Comprueba que el cliente no tenga ya una reserva activa del mismo
     * servicio en la misma semana (de lunes a domingo).
     * 
     * @param int $clientId ID del cliente
     * @param int $serviceId ID del servicio
     * @param string $date Fecha de la reserva (Y-m-d)
     * @return void
     * @throws \RuntimeException Si ya existe una reserva del servicio en la semana
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

    /**
     * Obtiene las reservas de un cliente con paginación
     * 
     * @param int $clientId ID del cliente
     * @param int $limit Número máximo de resultados
     * @param int $offset Desplazamiento para paginación
     * @return array Array de reservas
     */
    public function getReservasByClient(int $clientId, int $limit = 50, int $offset = 0): array
    {
        return $this->reservaRepository->findByClient($clientId, $limit, $offset);
    }

    /**
     * Busca una reserva específica con todos sus datos relacionados
     * 
     * @param int $id ID de la reserva
     * @return ReservaCompletaDTO|null DTO con datos completos o null si no existe
     */
    public function getReservaById(int $id): ?ReservaCompletaDTO
    {
        return $this->reservaRepository->getReservaCompletaById($id);
    }

    /**
     * Actualiza el estado de una reserva
     * 
     * Valida que el nuevo estado sea válido antes de actualizar.
     * 
     * @param int $reservaId ID de la reserva
     * @param string $newStatus Nuevo estado (Pendiente, Confirmada, Completada, Cancelada)
     * @return bool True si se actualizó correctamente
     * @throws \RuntimeException Si el estado es inválido
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
     * Obtiene reservas de un usuario con filtros opcionales
     * 
     * @param int $userId ID del usuario
     * @param int $limit Límite de resultados
     * @param int $offset Desplazamiento para paginación
     * @param string|null $fechaDesde Filtro opcional por fecha desde
     * @param string|null $fechaHasta Filtro opcional por fecha hasta
     * @param string|null $estado Filtro opcional por estado
     * @return array Array de ReservaCompletaDTO
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
     * Cuenta las reservas de un usuario con filtros opcionales
     * 
     * @param int $userId ID del usuario
     * @param string|null $fechaDesde Filtro opcional por fecha desde
     * @param string|null $fechaHasta Filtro opcional por fecha hasta
     * @param string|null $estado Filtro opcional por estado
     * @return int Número total de reservas
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
     * Obtiene la última reserva de un usuario
     * 
     * @param int $userId ID del usuario
     * @return ReservaCompletaDTO|null La última reserva o null si no tiene
     */
    public function getLatestReserva(int $userId): ?ReservaCompletaDTO
    {
        return $this->reservaRepository->findLatestByUserId($userId);
    }

    /**
     * Gets all bookings with optional filters (admin)
     * 
     * @param array $filtros Filters (cliente, especialista, estado, fecha_desde, fecha_hasta)
     * @param int $limit Limit of results
     * @param int $offset Offset for pagination
     * @return array Array of ReservaCompletaDTO
     */
    public function getAllReservasWithFilters(
        array $filtros = [],
        int $limit = 50,
        int $offset = 0
    ): array {
        return $this->reservaRepository->findAllFiltered($filtros, $limit, $offset);
    }

    /**
     * Counts all bookings with optional filters (admin)
     * 
     * @param array $filtros Filters (cliente, especialista, estado, fecha_desde, fecha_hasta)
     * @return int Total count of bookings
     */
    public function countAllReservasWithFilters(array $filtros = []): int
    {
        return $this->reservaRepository->countAllFiltered($filtros);
    }

    /**
     * Updates a booking with validation
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

        $clientId = (int) $data['id_cliente'];
        $specialistId = (int) $data['especialista_id'];
        $serviceId = (int) $data['servicio_id'];
        $date = $data['fecha'];
        $startTime = $data['hora'];
        $duration = $data['duracion'] ?? 60;
        $endTime = date('H:i:s', strtotime($startTime) + ($duration * 60));
        $estado = $data['estado'] ?? 'Pendiente';
        $observaciones = $data['observaciones'] ?? null;

        // Validate no conflicts, excluding current booking
        $hasConflict = $this->reservaRepository->findConflicts(
            $date,
            $startTime,
            $endTime,
            $specialistId,
            $reservaId
        );

        if ($hasConflict) {
            throw new \RuntimeException('El horario seleccionado ya no está disponible');
        }

        $clientConflict = $this->reservaRepository->findClientConflicts(
            $date,
            $startTime,
            $endTime,
            $clientId,
            $reservaId
        );

        if ($clientConflict) {
            throw new \RuntimeException('El cliente ya tiene otra reserva en ese horario');
        }

        $reserva = new Reserva(
            $clientId,
            $specialistId,
            $serviceId,
            $date,
            $startTime,
            $endTime,
            $estado,
            $observaciones,
            $existingReserva->fecha_creacion,
            $reservaId
        );

        return $this->reservaRepository->updateReserva($reserva);
    }

    /**
     * Deletes a booking
     * 
     * @param int $reservaId Booking ID
     * @return bool True if deleted successfully
     */
    public function deleteReserva(int $reservaId): bool
    {
        return $this->reservaRepository->deleteReserva($reservaId);
    }
}
