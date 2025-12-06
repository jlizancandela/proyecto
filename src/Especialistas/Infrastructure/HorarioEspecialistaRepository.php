<?php

namespace Especialistas\Infrastructure;

use Especialistas\Domain\HorarioEspecialista;
use PDO;

class HorarioEspecialistaRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

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
