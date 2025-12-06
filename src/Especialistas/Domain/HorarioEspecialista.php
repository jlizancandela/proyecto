<?php

namespace Especialistas\Domain;

class HorarioEspecialista
{
    private int $id_horario;
    private int $id_especialista;
    private int $dia_semana;
    private string $hora_inicio;
    private string $hora_fin;

    public function __construct(
        int $id_especialista,
        int $dia_semana,
        string $hora_inicio,
        string $hora_fin,
        ?int $id_horario = null,
    ) {
        $this->id_especialista = $id_especialista;
        $this->dia_semana = $dia_semana;
        $this->hora_inicio = $hora_inicio;
        $this->hora_fin = $hora_fin;
        if ($id_horario !== null) {
            $this->id_horario = $id_horario;
        }
    }

    public function getIdHorario(): int
    {
        return $this->id_horario;
    }

    public function getIdEspecialista(): int
    {
        return $this->id_especialista;
    }

    public function getDiaSemana(): int
    {
        return $this->dia_semana;
    }

    public function getHoraInicio(): string
    {
        return $this->hora_inicio;
    }

    public function getHoraFin(): string
    {
        return $this->hora_fin;
    }

    public static function fromDatabase(array $data): self
    {
        return new self(
            (int) $data["id_especialista"],
            (int) $data["dia_semana"],
            $data["hora_inicio"],
            $data["hora_fin"],
            $data["id_horario"] ?? null,
        );
    }
}
