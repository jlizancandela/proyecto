<?php

/**
 * Domain model representing a salon service.
 */

namespace Servicios\Domain;

/**
 * Represents a single service offered by the hair salon.
 */
class Servicio
{
    private int $id_servicio;
    private string $nombre_servicio;
    private int $duracion_minutos;
    private string $descripcion;
    private float $precio;
    private bool $activo;

    /**
     * Servicio constructor.
     *
     * @param string $nombre_servicio The name of the service.
     * @param int $duracion_minutos Estimated duration in minutes.
     * @param string $descripcion Detailed description of the service.
     * @param float $precio Price in decimal format.
     * @param int|null $id_servicio The unique database ID (optional).
     * @param bool $activo Whether the service is currently active.
     */
    public function __construct(
        string $nombre_servicio,
        int $duracion_minutos,
        string $descripcion,
        float $precio,
        ?int $id_servicio = null,
        bool $activo = true
    ) {
        $this->nombre_servicio = $nombre_servicio;
        $this->duracion_minutos = $duracion_minutos;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->activo = $activo;
        if ($id_servicio !== null) {
            $this->id_servicio = $id_servicio;
        }
    }

    /**
     * Gets the service ID.
     * @return int The unique service ID.
     */
    public function getIdServicio(): int
    {
        return $this->id_servicio;
    }

    /**
     * Gets the service name.
     * @return string The name of the service.
     */
    public function getNombreServicio(): string
    {
        return $this->nombre_servicio;
    }

    /**
     * Gets the service duration.
     * @return int Duration in minutes.
     */
    public function getDuracionMinutos(): int
    {
        return $this->duracion_minutos;
    }

    /**
     * Gets the service description.
     * @return string The detailed description.
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * Gets the service price.
     * @return float The price.
     */
    public function getPrecio(): float
    {
        return $this->precio;
    }

    /**
     * Checks if the service is active.
     * @return bool True if active, false otherwise.
     */
    public function isActivo(): bool
    {
        return $this->activo;
    }

    /**
     * Hydrates a Servicio object from a database associative array.
     *
     * @param array $data The raw record from the database.
     * @return self A new instance of Servicio.
     */
    public static function fromDatabase(array $data): self
    {
        return new self(
            $data["nombre_servicio"],
            (int) $data["duracion_minutos"],
            $data["descripcion"],
            (float) $data["precio"],
            $data["id_servicio"] ?? null,
            (bool) ($data["activo"] ?? true)
        );
    }
}
