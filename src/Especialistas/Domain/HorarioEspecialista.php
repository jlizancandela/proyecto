<?php

// Represents a specialist's work schedule with day and time slots.

namespace Especialistas\Domain;

class HorarioEspecialista
{
    private int $idHorario;
    private int $idEspecialista;
    private int $diaSemana;
    private string $horaInicio;
    private string $horaFin;

    /**
     * @param int $id_especialista
     * @param int $diaSemana
     * @param string $hora_inicio
     * @param string $hora_fin
     * @param int|null $id_horario
     */
    public function __construct(
        int $id_especialista,
        int $diaSemana,
        string $hora_inicio,
        string $hora_fin,
        ?int $id_horario = null,
    ) {
        $this->idEspecialista = $id_especialista;
        $this->diaSemana = $diaSemana;
        $this->horaInicio = $hora_inicio;
        $this->horaFin = $hora_fin;
        if ($id_horario !== null) {
            $this->idHorario = $id_horario;
        }
    }

    /**
     * @return int
     */
    public function getIdHorario(): int
    {
        return $this->idHorario;
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
    public function getDiaSemana(): int
    {
        return $this->diaSemana;
    }

    /**
     * @return string
     */
    public function getHoraInicio(): string
    {
        return $this->horaInicio;
    }

    /**
     * @return string
     */
    public function getHoraFin(): string
    {
        return $this->horaFin;
    }

    /**
     * @param array $data
     * @return self
     */
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
