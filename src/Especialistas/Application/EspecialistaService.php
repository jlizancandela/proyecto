<?php

namespace Especialistas\Application;

use Especialistas\Domain\Especialista;
use Especialistas\Domain\HorarioEspecialista;
use Especialistas\Infrastructure\EspecialistaRepository;
use Especialistas\Infrastructure\HorarioEspecialistaRepository;
use Especialistas\Infrastructure\EspecialistaServicioRepository;
use Especialistas\Application\EspecialistaUsuarioDTO;
use Servicios\Domain\EspecialistaServicio;

class EspecialistaService
{
    private EspecialistaRepository $especialistaRepository;
    private HorarioEspecialistaRepository $horarioRepository;
    private EspecialistaServicioRepository $servicioRepository;

    public function __construct(
        EspecialistaRepository $especialistaRepository,
        HorarioEspecialistaRepository $horarioRepository,
        EspecialistaServicioRepository $servicioRepository
    ) {
        $this->especialistaRepository = $especialistaRepository;
        $this->horarioRepository = $horarioRepository;
        $this->servicioRepository = $servicioRepository;
    }

    public function getAllEspecialistas(): array
    {
        return $this->especialistaRepository->getAllEspecialistasConUsuario();
    }

    public function getEspecialistaById(int $id): ?EspecialistaUsuarioDTO
    {
        return $this->especialistaRepository->getEspecialistaConUsuarioById($id);
    }

    public function addEspecialista(Especialista $especialista): void
    {
        $this->especialistaRepository->addEspecialista($especialista);
    }

    public function updateEspecialista(Especialista $especialista): void
    {
        $this->especialistaRepository->updateEspecialista($especialista);
    }

    public function deleteEspecialista(int $id): void
    {
        $this->especialistaRepository->deleteEspecialista($id);
    }

    public function getHorariosEspecialista(int $id_especialista): array
    {
        return $this->horarioRepository->getHorariosByEspecialista($id_especialista);
    }

    public function addHorarioEspecialista(HorarioEspecialista $horario): void
    {
        $this->horarioRepository->addHorario($horario);
    }

    public function deleteHorarioEspecialista(int $id): void
    {
        $this->horarioRepository->deleteHorario($id);
    }

    public function getHorariosSemanalEspecialista(int $id_especialista): array
    {
        return $this->horarioRepository->getHorariosGroupedByDay($id_especialista);
    }

    public function getHorariosEspecialistaPorDia(int $id_especialista, int $dia_semana): array
    {
        return $this->horarioRepository->getHorariosByEspecialistaYDia($id_especialista, $dia_semana);
    }

    public function isEspecialistaDisponible(int $id_especialista, int $dia_semana, string $hora): bool
    {
        return $this->horarioRepository->isEspecialistaAvailableAt($id_especialista, $dia_semana, $hora);
    }

    public function getDiasLaborablesEspecialista(int $id_especialista): array
    {
        return $this->horarioRepository->getAvailableDaysForEspecialista($id_especialista);
    }

    public function getServiciosEspecialista(int $id_especialista): array
    {
        return $this->servicioRepository->getServiciosForEspecialista($id_especialista);
    }

    public function addServicioEspecialista(int $id_especialista, int $id_servicio): void
    {
        $especialistaServicio = new EspecialistaServicio($id_especialista, $id_servicio);
        $this->servicioRepository->addEspecialistaServicio($especialistaServicio);
    }

    public function deleteServicioEspecialista(int $id_especialista, int $id_servicio): void
    {
        $this->servicioRepository->deleteEspecialistaServicio($id_especialista, $id_servicio);
    }
}
