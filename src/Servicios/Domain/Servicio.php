<?php

namespace Servicios\Domain;

use Respect\Validation\Validator as v;

/**
 * Represents a servicio in the system.
 */
class Servicio
{
    private int $id_servicio;
    private string $nombre_servicio;
    private int $duracion_minutos;
    private string $descripcion;
    private float $precio;

    public function __construct(
        string $nombre_servicio,
        int $duracion_minutos,
        string $descripcion,
        float $precio,
        ?int $id_servicio = null,
    ) {
        $this->nombre_servicio = $nombre_servicio;
        $this->duracion_minutos = $duracion_minutos;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        if ($id_servicio !== null) {
            $this->id_servicio = $id_servicio;
        }
    }

    public function getIdServicio(): int
    {
        return $this->id_servicio;
    }

    public function getNombreServicio(): string
    {
        return $this->nombre_servicio;
    }

    public function getDuracionMinutos(): int
    {
        return $this->duracion_minutos;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    /**
     * Creates a Servicio instance from database row data
     *
     * @param array $data Associative array from database
     * @return self
     */
    public static function fromDatabase(array $data): self
    {
        return new self(
            nombre_servicio: $data["nombre_servicio"],
            duracion_minutos: (int) $data["duracion_minutos"],
            descripcion: $data["descripcion"],
            precio: (float) $data["precio"],
            id_servicio: $data["id_servicio"] ?? null,
        );
    }

    public function getValidation(): \Respect\Validation\Validator
    {
        return v::attribute(
            "nombre_servicio",
            v::stringType()->notEmpty()->length(3, 100),
            "El nombre del servicio es obligatorio",
        )
            ->attribute(
                "duracion_minutos",
                v::intType()->notEmpty()->positive(),
                "La duracion del servicio es obligatoria",
            )
            ->attribute(
                "descripcion",
                v::stringType()->notEmpty()->length(3, 100),
                "La descripcion del servicio es obligatoria",
            )
            ->attribute(
                "precio",
                v::floatType()->notEmpty()->positive(),
                "El precio del servicio es obligatorio",
            );
    }
}
