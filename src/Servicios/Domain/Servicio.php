<?php

namespace Servicios\Domain;

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
        ?int $id_servicio = null
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

    public static function fromDatabase(array $data): self
    {
        return new self(
            $data["nombre_servicio"],
            (int) $data["duracion_minutos"],
            $data["descripcion"],
            (float) $data["precio"],
            $data["id_servicio"] ?? null
        );
    }
}
