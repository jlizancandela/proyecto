<?php

/**
 * EspecialistaServicio
 *
 * Represents the many-to-many relationship between specialists and services.
 * Links specialists with the services they can provide.
 */

namespace Especialistas\Domain;

class EspecialistaServicio
{
    private int $idEspecialista;
    private int $idServicio;

    /**
     * EspecialistaServicio constructor.
     * @param int $idEspecialista The ID of the specialist.
     * @param int $idServicio The ID of the service.
     */
    public function __construct(int $idEspecialista, int $idServicio)
    {
        $this->idEspecialista = $idEspecialista;
        $this->idServicio = $idServicio;
    }

    /**
     * Get the specialist's ID.
     * @return int The ID of the specialist.
     */
    public function getIdEspecialista(): int
    {
        return $this->idEspecialista;
    }

    /**
     * Get the service's ID.
     * @return int The ID of the service.
     */
    public function getIdServicio(): int
    {
        return $this->idServicio;
    }

    /**
     * Creates an EspecialistaServicio instance from database row data.
     * @param array $data Associative array of data from the database.
     * @return self A new EspecialistaServicio instance.
     */
    public static function fromDatabase(array $data): self
    {
        return new self(
            (int) $data["id_especialista"],
            (int) $data["id_servicio"],
        );
    }
}
