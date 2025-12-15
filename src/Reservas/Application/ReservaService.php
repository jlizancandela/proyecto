<?php

namespace Reservas\Application;

use Reservas\Domain\Reserva;
use Reservas\Infrastructure\ReservaRepository;
use Respect\Validation\Validator as v;

class ReservaService
{
    private ReservaRepository $reservaRepository;

    public function __construct(ReservaRepository $reservaRepository)
    {
        $this->reservaRepository = $reservaRepository;
    }

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

    public function getReservasByClient(int $clientId, int $limit = 50, int $offset = 0): array
    {
        return $this->reservaRepository->findByClient($clientId, $limit, $offset);
    }

    public function getReservaById(int $id): ?ReservaCompletaDTO
    {
        return $this->reservaRepository->getReservaCompletaById($id);
    }

    public function updateReservaStatus(int $reservaId, string $newStatus): bool
    {
        $validStatuses = ['Pendiente', 'Confirmada', 'Completada', 'Cancelada'];

        if (!in_array($newStatus, $validStatuses)) {
            throw new \RuntimeException('Estado de reserva inválido');
        }

        return $this->reservaRepository->updateStatus($reservaId, $newStatus);
    }
}
