<?php

/**
 * Reserva
 *
 * Domain entity representing a booking (reserva) in the system.
 * Contains all details related to a booking, including client, specialist,
 * service, dates, times, status, and creation information.
 */

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

    /**
     * Reserva constructor.
     *
     * @param int $id_cliente The ID of the client who made the booking.
     * @param int $id_especialista The ID of the specialist for the booking.
     * @param int $id_servicio The ID of the service booked.
     * @param string $fecha_reserva The date of the booking in 'YYYY-MM-DD' format.
     * @param string $hora_inicio The start time of the booking in 'HH:MM:SS' format.
     * @param string $hora_fin The end time of the booking in 'HH:MM:SS' format.
     * @param string $estado The current status of the booking (e.g., 'Pendiente', 'Confirmada').
     * @param string|null $observaciones Optional observations for the booking.
     * @param string|null $fecha_creacion The creation date and time of the booking in 'YYYY-MM-DD HH:MM:SS' format. Defaults to current time if null.
     * @param int|null $id_reserva The unique ID of the booking. Optional, used for existing bookings.
     */
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
     * Get the booking ID.
     * @return int The unique ID of the booking.
     */
    public function getIdReserva(): int
    {
        return $this->id_reserva;
    }

    /**
     * Get the client ID.
     * @return int The ID of the client who made the booking.
     */
    public function getIdCliente(): int
    {
        return $this->id_cliente;
    }

    /**
     * Get the specialist ID.
     * @return int The ID of the specialist for the booking.
     */
    public function getIdEspecialista(): int
    {
        return $this->id_especialista;
    }

    /**
     * Get the service ID.
     * @return int The ID of the service booked.
     */
    public function getIdServicio(): int
    {
        return $this->id_servicio;
    }

    /**
     * Get the booking date.
     * @return \DateTime A DateTime object representing the date of the booking.
     */
    public function getFechaReserva(): \DateTime
    {
        return new \DateTime($this->fecha_reserva);
    }

    /**
     * Get the start time of the booking.
     * @return string The start time in 'HH:MM:SS' format.
     */
    public function getHoraInicio(): string
    {
        return $this->hora_inicio;
    }

    /**
     * Get the end time of the booking.
     * @return string The end time in 'HH:MM:SS' format.
     */
    public function getHoraFin(): string
    {
        return $this->hora_fin;
    }

    /**
     * Get the status of the booking.
     * @return string The current status of the booking.
     */
    public function getEstado(): string
    {
        return $this->estado;
    }

    /**
     * Get the observations for the booking.
     * @return string|null Optional observations for the booking.
     */
    public function getObservaciones(): ?string
    {
        return $this->observaciones;
    }

    /**
     * Get the creation date and time of the booking.
     * @return \DateTime A DateTime object representing the creation date and time.
     */
    public function getFechaCreacion(): \DateTime
    {
        return new \DateTime($this->fecha_creacion);
    }

    /**
     * Creates a Reserva instance from database row data.
     * @param array $data Associative array of data from the database.
     * @return self A new Reserva instance.
     */
    public static function fromDatabase(array $data): self
    {
        return new self(
            $data['id_cliente'],
            $data['id_especialista'],
            $data['id_servicio'],
            $data['fecha_reserva'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['estado'],
            $data['observaciones'] ?? null,
            $data['fecha_creacion'] ?? null,
            $data['id_reserva'] ?? null
        );
    }
}
