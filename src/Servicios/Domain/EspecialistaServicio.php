<?php

namespace Servicios\Domain;

class EspecialistaServicio
{
    private int $id_especialista;
    private int $id_servicio;

    public function __construct(int $id_especialista, int $id_servicio)
    {
        $this->id_especialista = $id_especialista;
        $this->id_servicio = $id_servicio;
    }

    public function getIdEspecialista(): int
    {
        return $this->id_especialista;
    }

    public function getIdServicio(): int
    {
        return $this->id_servicio;
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            (int) $data["id_especialista"],
            (int) $data["id_servicio"],
        );
    }
}
