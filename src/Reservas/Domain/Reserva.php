<?php

namespace Reservas\Domain;

/**
 * Represents a reserva in the system.
 */
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

    /**
     * Gets the reserva's ID.
     *
     * @return int The reserva's ID.
     */
    public function getIdReserva(): int
    {
        return $this->id_reserva;
    }



    /**
     * Gets the reserva's client ID.
     *
     * @return int The reserva's client ID.
     */
    public function getIdCliente(): int
    {
        return $this->id_cliente;
    }

    /**
     * Gets the reserva's specialist ID.
     *
     * @return int The reserva's specialist ID.
     */
    public function getIdEspecialista(): int
    {
        return $this->id_especialista;
    }

    /**
     * Gets the reserva's service ID.
     *
     * @return int The reserva's service ID.
     */
    public function getIdServicio(): int
    {
        return $this->id_servicio;
    }

    /**
     * Gets the reserva's reservation date.
     *
     * @return \DateTime The reserva's reservation date.
     */
    public function getFechaReserva(): \DateTime
    {
        return new \DateTime($this->fecha_reserva);
    }

    /**
     * Gets the reserva's start hour.
     *
     * @return string The reserva's start hour.
     */
    public function getHoraInicio(): string
    {
        return $this->hora_inicio;
    }

    /**
     * Gets the reserva's end hour.
     *
     * @return string The reserva's end hour.
     */
    public function getHoraFin(): string
    {
        return $this->hora_fin;
    }

    /**
     * Gets the reserva's state.
     *
     * @return string The reserva's state.
     */
    public function getEstado(): string
    {
        return $this->estado;
    }

    /**
     * Gets the reserva's observations.
     *
     * @return ?string The reserva's observations.
     */
    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    /**
     * Gets the reserva's creation date.
     *
     * @return \DateTime The reserva's creation date.
     */
    public function getFechaCreacion(): \DateTime
    {
        return new \DateTime($this->fecha_creacion);
    }

    /**
     * Creates a Reserva instance from database row data
     *
     * @param array $data Associative array from database
     * @return self
     */
    public static function fromDatabase(array $data): self
    {
        return new self(
            id_cliente: (int) $data['id_cliente'],
            id_especialista: (int) $data['id_especialista'],
            id_servicio: (int) $data['id_servicio'],
            fecha_reserva: $data['fecha_reserva'],
            hora_inicio: $data['hora_inicio'],
            hora_fin: $data['hora_fin'],
            estado: $data['estado'],
            observaciones: $data['observaciones'] ?? null,
            fecha_creacion: $data['fecha_creacion'],
            id_reserva: $data['id_reserva'] ?? null
        );
    }
}
