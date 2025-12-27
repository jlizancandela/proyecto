<?php

/**
 * HorarioEspecialistaRepository
 *
 * Repository for managing specialist work schedules in the database.
 * Provides methods for retrieving, adding, updating, and deleting schedule entries.
 */

namespace Especialistas\Infrastructure;

use Especialistas\Domain\HorarioEspecialista;
use PDO;

class HorarioEspecialistaRepository
{
    private PDO $db;

    /**
     * HorarioEspecialistaRepository constructor.
     * @param PDO $db The PDO database connection.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Retrieves all specialist schedules from the database.
     * @return HorarioEspecialista[] An array of HorarioEspecialista objects.
     */
    public function getAllHorarios(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM horarios_especialistas");

            $horarios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $horarios[] = HorarioEspecialista::fromDatabase($row);
            }
            return $horarios;
        } catch (\Exception $e) {
            error_log("Error al obtener horarios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves a specialist schedule by its ID.
     * @param int $id The ID of the schedule entry.
     * @return HorarioEspecialista|null The HorarioEspecialista object if found, null otherwise.
     */
    public function getHorarioById(int $id): ?HorarioEspecialista
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM horarios_especialistas WHERE id_horario = :id"
            );
            $stmt->execute(["id" => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? HorarioEspecialista::fromDatabase($row) : null;
        } catch (\Exception $e) {
            error_log("Error al obtener horario: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieves all schedule entries for a specific specialist.
     * @param int $id_especialista The ID of the specialist.
     * @return HorarioEspecialista[] An array of HorarioEspecialista objects.
     */
    public function getHorariosByEspecialista(int $id_especialista): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM horarios_especialistas
                 WHERE id_especialista = :id_especialista
                 ORDER BY dia_semana, hora_inicio"
            );
            $stmt->execute(["id_especialista" => $id_especialista]);

            $horarios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $horarios[] = HorarioEspecialista::fromDatabase($row);
            }
            return $horarios;
        } catch (\Exception $e) {
            error_log("Error al obtener horarios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves all schedule entries for a specific day of the week.
     * @param int $dia_semana The day of the week (1-7).
     * @return HorarioEspecialista[] An array of HorarioEspecialista objects.
     */
    public function getHorariosByDia(int $dia_semana): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM horarios_especialistas
                 WHERE dia_semana = :dia_semana
                 ORDER BY hora_inicio"
            );
            $stmt->execute(["dia_semana" => $dia_semana]);

            $horarios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $horarios[] = HorarioEspecialista::fromDatabase($row);
            }
            return $horarios;
        } catch (\Exception $e) {
            error_log("Error al obtener horarios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves all schedule entries for a specific specialist and day of the week.
     * @param int $id_especialista The ID of the specialist.
     * @param int $dia_semana The day of the week (1-7).
     * @return HorarioEspecialista[] An array of HorarioEspecialista objects.
     */
    public function getHorariosByEspecialistaYDia(int $id_especialista, int $dia_semana): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM horarios_especialistas
                 WHERE id_especialista = :id_especialista AND dia_semana = :dia_semana
                 ORDER BY hora_inicio"
            );
            $stmt->execute([
                "id_especialista" => $id_especialista,
                "dia_semana" => $dia_semana,
            ]);

            $horarios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $horarios[] = HorarioEspecialista::fromDatabase($row);
            }
            return $horarios;
        } catch (\Exception $e) {
            error_log("Error al obtener horarios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Adds a new schedule entry to the database.
     * @param HorarioEspecialista $horario The HorarioEspecialista object to add.
     * @return void
     * @throws \Exception If there is a database error.
     */
    public function addHorario(HorarioEspecialista $horario): void
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO horarios_especialistas (id_especialista, dia_semana, hora_inicio, hora_fin)
                 VALUES (:id_especialista, :dia_semana, :hora_inicio, :hora_fin)"
            );
            $stmt->execute([
                "id_especialista" => $horario->getIdEspecialista(),
                "dia_semana" => $horario->getDiaSemana(),
                "hora_inicio" => $horario->getHoraInicio(),
                "hora_fin" => $horario->getHoraFin(),
            ]);
        } catch (\Exception $e) {
            error_log("Error al agregar horario: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Updates an existing schedule entry in the database.
     * @param HorarioEspecialista $horario The HorarioEspecialista object with updated data.
     * @return void
     * @throws \Exception If there is a database error.
     */
    public function updateHorario(HorarioEspecialista $horario): void
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE horarios_especialistas
                 SET id_especialista = :id_especialista,
                     dia_semana = :dia_semana,
                     hora_inicio = :hora_inicio,
                     hora_fin = :hora_fin
                 WHERE id_horario = :id_horario"
            );
            $stmt->execute([
                "id_horario" => $horario->getIdHorario(),
                "id_especialista" => $horario->getIdEspecialista(),
                "dia_semana" => $horario->getDiaSemana(),
                "hora_inicio" => $horario->getHoraInicio(),
                "hora_fin" => $horario->getHoraFin(),
            ]);
        } catch (\Exception $e) {
            error_log("Error al actualizar horario: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Deletes a schedule entry by its ID.
     * @param int $id The ID of the schedule entry to delete.
     * @return void
     * @throws \Exception If there is a database error.
     */
    public function deleteHorario(int $id): void
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM horarios_especialistas WHERE id_horario = :id"
            );
            $stmt->execute(["id" => $id]);
        } catch (\Exception $e) {
            error_log("Error al eliminar horario: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Deletes all schedule entries for a specific specialist.
     * @param int $id_especialista The ID of the specialist.
     * @return void
     * @throws \Exception If there is a database error.
     */
    public function deleteHorariosByEspecialista(int $id_especialista): void
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM horarios_especialistas WHERE id_especialista = :id_especialista"
            );
            $stmt->execute(["id_especialista" => $id_especialista]);
        } catch (\Exception $e) {
            error_log("Error al eliminar horarios: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieves all schedule entries for a specific specialist, grouped by day of the week.
     * @param int $id_especialista The ID of the specialist.
     * @return array An associative array where keys are days of the week and values are arrays of HorarioEspecialista objects.
     */
    public function getHorariosGroupedByDay(int $id_especialista): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM horarios_especialistas
                 WHERE id_especialista = :id_especialista
                 ORDER BY dia_semana, hora_inicio"
            );
            $stmt->execute(["id_especialista" => $id_especialista]);

            $grouped = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $horario = HorarioEspecialista::fromDatabase($row);
                $dia = $horario->getDiaSemana();
                if (!isset($grouped[$dia])) {
                    $grouped[$dia] = [];
                }
                $grouped[$dia][] = $horario;
            }
            return $grouped;
        } catch (\Exception $e) {
            error_log("Error al obtener horarios agrupados: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Checks if a specialist is available at a specific time on a specific day.
     * @param int $id_especialista The ID of the specialist.
     * @param int $dia_semana The day of the week.
     * @param string $hora The time to check (e.g., "HH:MM").
     * @return bool True if the specialist is available, false otherwise.
     */
    public function isEspecialistaAvailableAt(int $id_especialista, int $dia_semana, string $hora): bool
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM horarios_especialistas
                 WHERE id_especialista = :id_especialista
                 AND dia_semana = :dia_semana
                 AND hora_inicio <= :hora
                 AND hora_fin > :hora"
            );
            $stmt->execute([
                "id_especialista" => $id_especialista,
                "dia_semana" => $dia_semana,
                "hora" => $hora,
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["count"] > 0;
        } catch (\Exception $e) {
            error_log("Error al verificar disponibilidad: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves all days of the week a specialist has availability.
     * @param int $id_especialista The ID of the specialist.
     * @return int[] An array of integers representing the available days of the week (1-7).
     */
    public function getAvailableDaysForEspecialista(int $id_especialista): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT DISTINCT dia_semana FROM horarios_especialistas
                 WHERE id_especialista = :id_especialista
                 ORDER BY dia_semana"
            );
            $stmt->execute(["id_especialista" => $id_especialista]);

            $days = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $days[] = (int) $row["dia_semana"];
            }
            return $days;
        } catch (\Exception $e) {
            error_log("Error al obtener días disponibles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Checks for scheduling conflicts for a new or updated schedule entry.
     * @param int $id_especialista The ID of the specialist.
     * @param int $dia_semana The day of the week.
     * @param string $hora_inicio The start time of the potential new slot.
     * @param string $hora_fin The end time of the potential new slot.
     * @return bool True if a conflict exists, false otherwise.
     */
    public function existsHorarioConflict(
        int $id_especialista,
        int $dia_semana,
        string $hora_inicio,
        string $hora_fin
    ): bool {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM horarios_especialistas
                 WHERE id_especialista = :id_especialista
                 AND dia_semana = :dia_semana
                 AND (hora_inicio < :hora_fin AND hora_fin > :hora_inicio)"
            );
            $stmt->execute([
                "id_especialista" => $id_especialista,
                "dia_semana" => $dia_semana,
                "hora_inicio" => $hora_inicio,
                "hora_fin" => $hora_fin,
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result["count"] > 0;
        } catch (\Exception $e) {
            error_log("Error al verificar conflicto: " . $e->getMessage());
            return false;
        }
    }
}

