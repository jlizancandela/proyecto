<?php

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

    public function getIdEspecialista(): int
    {
        return $this->idEspecialista;
    }

    public function getIdServicio(): int
    {
        return $this->idServicio;
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            (int) $data["id_especialista"],
            (int) $data["id_servicio"],
        );
    }
}
