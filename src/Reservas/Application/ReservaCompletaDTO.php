<?php

/**
 * ReservaCompletaDTO
 *
 * Data Transfer Object for a complete booking, including details from the client,
 * specialist, and service involved in the reservation.
 */

namespace Reservas\Application;

class ReservaCompletaDTO
{
    public int $id_reserva;
    public int $id_cliente;
    public int $id_especialista;
    public int $id_servicio;
    public string $fecha_reserva;
    public string $hora_inicio;
    public string $hora_fin;
    public string $estado;
    public ?string $observaciones;
    public string $fecha_creacion;

    public ?string $cliente_nombre;
    public ?string $cliente_apellidos;
    public ?string $cliente_email;
    public ?string $cliente_telefono;

    public ?string $especialista_nombre;
    public ?string $especialista_apellidos;
    public ?string $especialista_email;
    public ?string $especialista_telefono;
    public ?string $especialista_descripcion;
    public ?string $especialista_foto_url;

    public ?string $servicio_nombre;
    public int $servicio_duracion_minutos;
    public float $servicio_precio;
    public ?string $servicio_descripcion;

    /**
     * Creates a ReservaCompletaDTO instance from an associative array, typically a database row.
     *
     * @param array $data Associative array containing all booking-related data.
     * @return self A new ReservaCompletaDTO instance.
     */
    public static function fromDatabase(array $data): self
    {
        $dto = new self();

        $dto->id_reserva = (int) $data['id_reserva'];
        $dto->id_cliente = (int) $data['id_cliente'];
        $dto->id_especialista = (int) $data['id_especialista'];
        $dto->id_servicio = (int) $data['id_servicio'];
        $dto->fecha_reserva = $data['fecha_reserva'];
        $dto->hora_inicio = $data['hora_inicio'];
        $dto->hora_fin = $data['hora_fin'];
        $dto->estado = $data['estado'];
        $dto->observaciones = $data['observaciones'] ?? null;
        $dto->fecha_creacion = $data['fecha_creacion'];

        $dto->cliente_nombre = $data['cliente_nombre'] ?? null;
        $dto->cliente_apellidos = $data['cliente_apellidos'] ?? null;
        $dto->cliente_email = $data['cliente_email'] ?? null;
        $dto->cliente_telefono = $data['cliente_telefono'] ?? null;

        $dto->especialista_nombre = $data['especialista_nombre'] ?? null;
        $dto->especialista_apellidos = $data['especialista_apellidos'] ?? null;
        $dto->especialista_email = $data['especialista_email'] ?? null;
        $dto->especialista_telefono = $data['especialista_telefono'] ?? null;
        $dto->especialista_descripcion = $data['especialista_descripcion'] ?? null;
        $dto->especialista_foto_url = $data['especialista_foto_url'] ?? null;

        $dto->servicio_nombre = $data['nombre_servicio'] ?? null;
        $dto->servicio_duracion_minutos = (int) $data['duracion_minutos'];
        $dto->servicio_precio = (float) $data['precio'];
        $dto->servicio_descripcion = $data['servicio_descripcion'] ?? null;

        return $dto;
    }

    /**
     * Converts the DTO to an associative array.
     *
     * @return array The DTO data as an associative array.
     */
    public function toArray(): array
    {
        return [
            'id_reserva' => $this->id_reserva,
            'id_cliente' => $this->id_cliente,
            'id_especialista' => $this->id_especialista,
            'id_servicio' => $this->id_servicio,
            'fecha_reserva' => $this->fecha_reserva,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'fecha_creacion' => $this->fecha_creacion,
            'cliente' => [
                'nombre' => $this->cliente_nombre,
                'apellidos' => $this->cliente_apellidos,
                'email' => $this->cliente_email,
                'telefono' => $this->cliente_telefono,
            ],
            'especialista' => [
                'nombre' => $this->especialista_nombre,
                'apellidos' => $this->especialista_apellidos,
                'email' => $this->especialista_email,
                'telefono' => $this->especialista_telefono,
                'descripcion' => $this->especialista_descripcion,
                'foto_url' => $this->especialista_foto_url,
            ],
            'servicio' => [
                'nombre' => $this->servicio_nombre,
                'duracion_minutos' => $this->servicio_duracion_minutos,
                'precio' => $this->servicio_precio,
                'descripcion' => $this->servicio_descripcion,
            ],
        ];
    }
}
