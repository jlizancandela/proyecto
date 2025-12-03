<?php

namespace Especialistas\Application;

use Especialistas\Domain\Especialista;
use Especialistas\Domain\HorarioEspecialista;
use Especialistas\Infrastructure\EspecialistaRepository;
use Shared\Domain\Exceptions\InvalidValidation;
use Respect\Validation\Exceptions\NestedValidationException;
use Especialistas\Application\EspecialistaUsuarioDTO;
use Servicios\Domain\EspecialistaServicio;

class EspecialistaService
{
    private EspecialistaRepository $especialistaRepository;

    public function __construct(
        EspecialistaRepository $especialistaRepository,
    ) {
        $this->especialistaRepository = $especialistaRepository;
    }

    // Metodos para especialista

    public function getAllEspecialistas(): array
    {
        return $this->especialistaRepository->getAllEspecialistasConUsuario();
    }

    public function getEspecialistaById(int $id): ?EspecialistaUsuarioDTO
    {
        return $this->especialistaRepository->getEspecialistaConUsuarioById(
            $id,
        );
    }

    public function addEspecialista(Especialista $especialista): void
    {
        try {
            $validator = $especialista->getValidation();
            $validator->check($especialista);
        } catch (NestedValidationException $e) {
            throw new InvalidValidation($e->getFullMessage());
        }
        $this->especialistaRepository->addEspecialista($especialista);
    }

    public function updateEspecialista(Especialista $especialista): void
    {
        try {
            $validator = $especialista->getValidation();
            $validator->check($especialista);
        } catch (NestedValidationException $e) {
            throw new InvalidValidation($e->getFullMessage());
        }
        $this->especialistaRepository->updateEspecialista($especialista);
    }

    public function deleteEspecialista(int $id): void
    {
        $this->especialistaRepository->deleteEspecialista($id);
    }

    // Métodos para horarios de especialista

    /**
     * Get all schedules for a specialist
     *
     * @param int $id_especialista
     * @return array
     */
    public function getHorariosEspecialista(int $id_especialista): array
    {
        return $this->especialistaRepository->getHorariosByEspecialista(
            $id_especialista,
        );
    }

    /**
     * Add a schedule for a specialist
     *
     * @param HorarioEspecialista $horario
     * @return void
     */
    public function addHorarioEspecialista(
        HorarioEspecialista $horario,
    ): void {
        $this->especialistaRepository->addHorario($horario);
    }

    /**
     * Delete a schedule
     *
     * @param int $id
     * @return void
     */
    public function deleteHorarioEspecialista(int $id): void
    {
        $this->especialistaRepository->deleteHorario($id);
    }

    /**
     * Get weekly schedule grouped by day for a specialist
     * Returns an array indexed by day of week (0-6) with time slots
     *
     * @param int $id_especialista
     * @return array<int, HorarioEspecialista[]>
     */
    public function getHorariosSemanalEspecialista(int $id_especialista): array
    {
        return $this->especialistaRepository->getHorariosGroupedByDay(
            $id_especialista,
        );
    }

    /**
     * Get all time slots for a specific day of the week for a specialist
     *
     * @param int $id_especialista
     * @param int $dia_semana Day of the week (0-6, where 0 is Sunday)
     * @return HorarioEspecialista[]
     */
    public function getHorariosEspecialistaPorDia(
        int $id_especialista,
        int $dia_semana,
    ): array {
        return $this->especialistaRepository->getHorariosByEspecialistaYDia(
            $id_especialista,
            $dia_semana,
        );
    }

    /**
     * Check if a specialist is available at a specific time on a specific day
     *
     * @param int $id_especialista
     * @param int $dia_semana Day of the week (0-6)
     * @param string $hora Time in HH:MM:SS format
     * @return bool
     */
    public function isEspecialistaDisponible(
        int $id_especialista,
        int $dia_semana,
        string $hora,
    ): bool {
        return $this->especialistaRepository->isEspecialistaAvailableAt(
            $id_especialista,
            $dia_semana,
            $hora,
        );
    }

    /**
     * Get all days of the week where a specialist has configured time slots
     *
     * @param int $id_especialista
     * @return int[] Array of day numbers (0-6)
     */
    public function getDiasLaborablesEspecialista(int $id_especialista): array
    {
        return $this->especialistaRepository->getAvailableDaysForEspecialista(
            $id_especialista,
        );
    }

    /**
     * Get all specialists available on a specific day with their schedules
     * Useful for showing who works on Mondays, Tuesdays, etc.
     *
     * @param int $dia_semana Day of the week (0-6)
     * @return array Array with horario and especialista information
     */
    public function getEspecialistasDisponiblesPorDia(int $dia_semana): array
    {
        return $this->especialistaRepository->getHorariosByDiaWithEspecialistaInfo(
            $dia_semana,
        );
    }

    // Métodos para servicios de especialista

    /**
     * Get all services for a specialist
     *
     * @param int $id_especialista
     * @return array Array of Servicio objects
     */
    public function getServiciosEspecialista(int $id_especialista): array
    {
        return $this->especialistaRepository->getServiciosForEspecialista(
            $id_especialista,
        );
    }

    /**
     * Add a service to a specialist
     *
     * @param int $id_especialista
     * @param int $id_servicio
     * @return void
     */
    public function addServicioEspecialista(
        int $id_especialista,
        int $id_servicio,
    ): void {
        $especialistaServicio = new EspecialistaServicio(
            $id_especialista,
            $id_servicio,
        );

        $this->especialistaRepository->addEspecialistaServicio(
            $especialistaServicio,
        );
    }

    /**
     * Remove a service from a specialist
     *
     * @param int $id_especialista
     * @param int $id_servicio
     * @return void
     */
    public function deleteServicioEspecialista(
        int $id_especialista,
        int $id_servicio,
    ): void {
        $this->especialistaRepository->deleteEspecialistaServicio(
            $id_especialista,
            $id_servicio,
        );
    }
}
