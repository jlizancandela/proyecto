<?php

namespace Reservas\Domain;

class Reserva
{
    private int $id_reserva;
    private int $id_cliente;
    private int $id_especialista;
    private int $id_servicio;
    private string $fecha_reserva;
    private string $hora_inicio;
    private string $hora_fin;
    private string $estado;
    private ?string $observaciones;
    private string $fecha_creacion;

    public function __construct(
        int $id_cliente,
        int $id_especialista,
        int $id_servicio,
        string $fecha_reserva,
        string $hora_inicio,
        string $hora_fin,
        string $estado,
        ?string $observaciones = null,
        ?string $fecha_creacion = null,
        ?int $id_reserva = null
    ) {
        $this->id_cliente = $id_cliente;
        $this->id_especialista = $id_especialista;
        $this->id_servicio = $id_servicio;
        $this->fecha_reserva = $fecha_reserva;
        $this->hora_inicio = $hora_inicio;
        $this->hora_fin = $hora_fin;
        $this->estado = $estado;
        $this->observaciones = $observaciones;
        $this->fecha_creacion = $fecha_creacion ?? date('Y-m-d H:i:s');
        if ($id_reserva !== null) {
            $this->id_reserva = $id_reserva;
        }
    }

    public function getIdReserva(): int
    {
        return $this->id_reserva;
    }



    public function getIdCliente(): int
    {
        return $this->id_cliente;
    }

    public function getIdEspecialista(): int
    {
        return $this->id_especialista;
    }

    public function getIdServicio(): int
    {
        return $this->id_servicio;
    }

    public function getFechaReserva(): \DateTime
    {
        return new \DateTime($this->fecha_reserva);
    }

    public function getHoraInicio(): string
    {
        return $this->hora_inicio;
    }

    public function getHoraFin(): string
    {
        return $this->hora_fin;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    public function getFechaCreacion(): \DateTime
    {
        return new \DateTime($this->fecha_creacion);
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            (int) $data['id_cliente'],
            (int) $data['id_especialista'],
            (int) $data['id_servicio'],
            $data['fecha_reserva'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['estado'],
            $data['observaciones'] ?? null,
            $data['fecha_creacion'],
            $data['id_reserva'] ?? null
        );
    }
}
