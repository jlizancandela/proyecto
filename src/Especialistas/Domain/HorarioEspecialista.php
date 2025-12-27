<?php

/**
 * HorarioEspecialista
 *
 * Represents a specialist's work schedule for a specific day of the week,
 * defined by a start and end time.
 */

namespace Especialistas\Domain;

class HorarioEspecialista
{
    private int $idHorario;
    private int $idEspecialista;
    private int $diaSemana;
    private string $horaInicio;
    private string $horaFin;

    /**
     * HorarioEspecialista constructor.
     *
     * @param int $id_especialista The ID of the specialist this schedule belongs to.
     * @param int $diaSemana The day of the week (1 for Monday, 7 for Sunday).
     * @param string $hora_inicio The start time of the schedule slot (e.g., "09:00").
     * @param string $hora_fin The end time of the schedule slot (e.g., "18:00").
     * @param int|null $id_horario The unique ID of the schedule slot (optional, for existing records).
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
     * Get the ID of the schedule entry.
     * @return int The unique ID of the schedule slot.
     */
    public function getIdHorario(): int
    {
        return $this->idHorario;
    }

    /**
     * Get the specialist's ID.
     * @return int The ID of the specialist this schedule belongs to.
     */
    public function getIdEspecialista(): int
    {
        return $this->idEspecialista;
    }

    /**
     * Get the day of the week for this schedule.
     * @return int The day of the week (1-7).
     */
    public function getDiaSemana(): int
    {
        return $this->diaSemana;
    }

    /**
     * Get the start time of the schedule slot.
     * @return string The start time in "HH:MM" format.
     */
    public function getHoraInicio(): string
    {
        return $this->horaInicio;
    }

    /**
     * Get the end time of the schedule slot.
     * @return string The end time in "HH:MM" format.
     */
    public function getHoraFin(): string
    {
        return $this->horaFin;
    }

    /**
     * Creates a HorarioEspecialista instance from database row data.
     * @param array $data Associative array of data from the database.
     * @return self A new HorarioEspecialista instance.
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
