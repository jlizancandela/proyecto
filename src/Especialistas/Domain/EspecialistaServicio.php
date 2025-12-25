<?php

// Links specialists with the services they can provide

namespace Especialistas\Domain;

class EspecialistaServicio
{
    private int $idEspecialista;
    private int $idServicio;

    public function __construct(int $idEspecialista, int $idServicio)
    {
        $this->idEspecialista = $idEspecialista;
        $this->idServicio = $idServicio;
    }

    /**
     * @return int
     */
    public function getIdEspecialista(): int
    {
        return $this->idEspecialista;
    }

    /**
     * @return int
     */
    public function getIdServicio(): int
    {
        return $this->idServicio;
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromDatabase(array $data): self
    {
        return new self(
            (int) $data["id_especialista"],
            (int) $data["id_servicio"],
        );
    }
}
