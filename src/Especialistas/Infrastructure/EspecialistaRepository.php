<?php

namespace Especialistas\Infrastructure;

use Especialistas\Domain\Especialista;
use Especialistas\Domain\HorarioEspecialista;
use Servicios\Domain\EspecialistaServicio;
use Servicios\Domain\Servicio;
use Especialistas\Application\EspecialistaUsuarioDTO;
use PDO;

class EspecialistaRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Gets all especialistas with their usuario data using JOIN
     *
     * @return EspecialistaUsuarioDTO[]
     */
    public function getAllEspecialistasConUsuario(): array
    {
        try {
            $stmt = $this->db->query("
                SELECT 
                    u.id_usuario,
                    u.rol,
                    u.nombre,
                    u.apellidos,
                    u.email,
                    u.telefono,
                    u.fecha_registro,
                    u.activo,
                    e.id as id_especialista,
                    e.descripcion,
                    e.foto_url
                FROM usuarios u
                INNER JOIN especialistas e ON u.id_usuario = e.id_usuario
            ");

            $especialistas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $especialistas[] = EspecialistaUsuarioDTO::fromDatabase($row);
            }
            return $especialistas;
        } catch (\Exception $e) {
            error_log(
                "Error al obtener especialistas con usuario: " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Gets a single especialista with their usuario data using JOIN
     *
     * @param int $id The especialista ID
     * @return EspecialistaUsuarioDTO|null
     */
    public function getEspecialistaConUsuarioById(
        int $id,
    ): ?EspecialistaUsuarioDTO {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.id_usuario,
                    u.rol,
                    u.nombre,
                    u.apellidos,
                    u.email,
                    u.telefono,
                    u.fecha_registro,
                    u.activo,
                    e.id as id_especialista,
                    e.descripcion,
                    e.foto_url
                FROM usuarios u
                INNER JOIN especialistas e ON u.id_usuario = e.id_usuario
                WHERE e.id = :id
            ");
            $stmt->execute(["id" => $id]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? EspecialistaUsuarioDTO::fromDatabase($row) : null;
        } catch (\Exception $e) {
            error_log(
                "Error al obtener especialista con usuario por ID: " .
                    $e->getMessage(),
            );
            return null;
        }
    }

    /**
     * Add a new especialista
     *
     * @param Especialista $especialista
     * @return void
     */
    public function addEspecialista(Especialista $especialista): void
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO especialistas (id_usuario, descripcion, foto_url) VALUES (:id_usuario, :descripcion, :foto_url)",
            );
            $stmt->execute([
                "id_usuario" => $especialista->getIdUsuario(),
                "descripcion" => $especialista->getDescripcion(),
                "foto_url" => $especialista->getFotoUrl(),
            ]);
        } catch (\Exception $e) {
            error_log("Error al agregar el especialista: " . $e->getMessage());
        }
    }

    /**
     * Update an existing especialista
     *
     * @param Especialista $especialista
     * @return void
     */
    public function updateEspecialista(Especialista $especialista): void
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE especialistas SET id_usuario = :id_usuario, descripcion = :descripcion, foto_url = :foto_url WHERE id = :id",
            );
            $stmt->execute([
                "id" => $especialista->getIdEspecialista(),
                "id_usuario" => $especialista->getIdUsuario(),
                "descripcion" => $especialista->getDescripcion(),
                "foto_url" => $especialista->getFotoUrl(),
            ]);
        } catch (\Exception $e) {
            error_log(
                "Error al actualizar el especialista: " . $e->getMessage(),
            );
        }
    }

    /**
     * Delete an especialista by ID
     *
     * @param int $id
     * @return void
     */
    public function deleteEspecialista(int $id): void
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM especialistas WHERE id = :id",
            );
            $stmt->execute(["id" => $id]);
        } catch (\Exception $e) {
            error_log("Error al eliminar el especialista: " . $e->getMessage());
        }
    }

    /**
     * Get all horarios
     *
     * @return HorarioEspecialista[]
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
            error_log(
                "Error al obtener todos los horarios: " . $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Get a horario by ID
     *
     * @param int $id
     * @return HorarioEspecialista|null
     */
    public function getHorarioById(int $id): ?HorarioEspecialista
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM horarios_especialistas WHERE id_horario = :id",
            );
            $stmt->execute(["id" => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? HorarioEspecialista::fromDatabase($row) : null;
        } catch (\Exception $e) {
            error_log("Error al obtener horario por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all horarios for a specific especialista
     *
     * @param int $id_especialista
     * @return HorarioEspecialista[]
     */
    public function getHorariosByEspecialista(int $id_especialista): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM horarios_especialistas WHERE id_especialista = :id_especialista ORDER BY dia_semana, hora_inicio",
            );
            $stmt->execute(["id_especialista" => $id_especialista]);

            $horarios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $horarios[] = HorarioEspecialista::fromDatabase($row);
            }
            return $horarios;
        } catch (\Exception $e) {
            error_log(
                "Error al obtener horarios del especialista: " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Get horarios for a specific day of the week
     *
     * @param int $dia_semana Day of the week (0-6, where 0 is Sunday)
     * @return HorarioEspecialista[]
     */
    public function getHorariosByDia(int $dia_semana): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM horarios_especialistas WHERE dia_semana = :dia_semana ORDER BY hora_inicio",
            );
            $stmt->execute(["dia_semana" => $dia_semana]);

            $horarios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $horarios[] = HorarioEspecialista::fromDatabase($row);
            }
            return $horarios;
        } catch (\Exception $e) {
            error_log("Error al obtener horarios por día: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get horarios for a specific especialista on a specific day
     *
     * @param int $id_especialista
     * @param int $dia_semana
     * @return HorarioEspecialista[]
     */
    public function getHorariosByEspecialistaYDia(
        int $id_especialista,
        int $dia_semana,
    ): array {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM horarios_especialistas 
                WHERE id_especialista = :id_especialista AND dia_semana = :dia_semana 
                ORDER BY hora_inicio",
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
            error_log(
                "Error al obtener horarios por especialista y día: " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Add a new horario
     *
     * @param HorarioEspecialista $horario
     * @return void
     */
    public function addHorario(HorarioEspecialista $horario): void
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO horarios_especialistas (id_especialista, dia_semana, hora_inicio, hora_fin) 
                VALUES (:id_especialista, :dia_semana, :hora_inicio, :hora_fin)",
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
     * Update an existing horario
     *
     * @param HorarioEspecialista $horario
     * @return void
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
                WHERE id_horario = :id_horario",
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
     * Delete a horario by ID
     *
     * @param int $id
     * @return void
     */
    public function deleteHorario(int $id): void
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM horarios_especialistas WHERE id_horario = :id",
            );
            $stmt->execute(["id" => $id]);
        } catch (\Exception $e) {
            error_log("Error al eliminar horario: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete all horarios for a specific especialista
     *
     * @param int $id_especialista
     * @return void
     */
    public function deleteHorariosByEspecialista(int $id_especialista): void
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM horarios_especialistas WHERE id_especialista = :id_especialista",
            );
            $stmt->execute(["id_especialista" => $id_especialista]);
        } catch (\Exception $e) {
            error_log(
                "Error al eliminar horarios del especialista: " .
                    $e->getMessage(),
            );
            throw $e;
        }
    }

    /**
     * Get horarios grouped by day of the week for a specific especialista
     * Useful for displaying weekly schedules
     *
     * @param int $id_especialista
     * @return array<int, HorarioEspecialista[]> Array indexed by day of week (0-6)
     */
    public function getHorariosGroupedByDay(int $id_especialista): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM horarios_especialistas 
                WHERE id_especialista = :id_especialista 
                ORDER BY dia_semana, hora_inicio",
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
            error_log(
                "Error al obtener horarios agrupados por día: " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Check if an especialista is available at a specific time on a specific day
     *
     * @param int $id_especialista
     * @param int $dia_semana Day of the week (0-6)
     * @param string $hora Time to check (HH:MM:SS format)
     * @return bool True if the especialista has a time slot that includes this time
     */
    public function isEspecialistaAvailableAt(
        int $id_especialista,
        int $dia_semana,
        string $hora,
    ): bool {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM horarios_especialistas 
                WHERE id_especialista = :id_especialista 
                AND dia_semana = :dia_semana 
                AND hora_inicio <= :hora 
                AND hora_fin > :hora",
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
     * Get all days of the week where an especialista has configured time slots
     *
     * @param int $id_especialista
     * @return int[] Array of day numbers (0-6)
     */
    public function getAvailableDaysForEspecialista(int $id_especialista): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT DISTINCT dia_semana FROM horarios_especialistas 
                WHERE id_especialista = :id_especialista 
                ORDER BY dia_semana",
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
     * Get all horarios for a specific day with especialista information
     * Useful for displaying all specialists available on a given day
     *
     * @param int $dia_semana Day of the week (0-6)
     * @return array Array of associative arrays with horario and especialista data
     */
    public function getHorariosByDiaWithEspecialistaInfo(int $dia_semana): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT 
                    h.*,
                    e.id_usuario,
                    e.descripcion as especialista_descripcion,
                    e.foto_url,
                    u.nombre,
                    u.apellidos,
                    u.email
                FROM horarios_especialistas h
                INNER JOIN especialistas e ON h.id_especialista = e.id
                INNER JOIN usuarios u ON e.id_usuario = u.id_usuario
                WHERE h.dia_semana = :dia_semana
                ORDER BY h.hora_inicio, u.apellidos, u.nombre",
            );
            $stmt->execute(["dia_semana" => $dia_semana]);

            $results = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = [
                    "horario" => HorarioEspecialista::fromDatabase($row),
                    "especialista" => [
                        "id" => $row["id_especialista"],
                        "id_usuario" => $row["id_usuario"],
                        "nombre" => $row["nombre"],
                        "apellidos" => $row["apellidos"],
                        "email" => $row["email"],
                        "descripcion" => $row["especialista_descripcion"],
                        "foto_url" => $row["foto_url"],
                    ],
                ];
            }
            return $results;
        } catch (\Exception $e) {
            error_log(
                "Error al obtener horarios con info de especialista: " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Check if a horario exists for a specific time slot
     *
     * @param int $id_especialista
     * @param int $dia_semana
     * @param string $hora_inicio
     * @param string $hora_fin
     * @return bool
     */
    public function existsHorarioConflict(
        int $id_especialista,
        int $dia_semana,
        string $hora_inicio,
        string $hora_fin,
    ): bool {
        try {
            $stmt = $this->db->prepare(
                "SELECT COUNT(*) as count FROM horarios_especialistas 
                WHERE id_especialista = :id_especialista 
                AND dia_semana = :dia_semana 
                AND (
                    (hora_inicio < :hora_fin AND hora_fin > :hora_inicio)
                )",
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
            error_log(
                "Error al verificar conflicto de horario: " . $e->getMessage(),
            );
            return false;
        }
    }

    /**
     * Check if a specific relationship between an especialista and a servicio exists
     *
     * @param int $id_especialista The especialista ID
     * @param int $id_servicio The servicio ID
     * @return EspecialistaServicio|null The relationship object or null if not found
     */
    public function getEspecialistaServicio(
        int $id_especialista,
        int $id_servicio,
    ): ?EspecialistaServicio {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM especialista_servicios WHERE id_especialista = :id_especialista AND id_servicio = :id_servicio",
            );
            $stmt->execute([
                "id_especialista" => $id_especialista,
                "id_servicio" => $id_servicio,
            ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? EspecialistaServicio::fromDatabase($row) : null;
        } catch (\Exception $e) {
            error_log(
                "Error al obtener el especialista_servicio: " .
                    $e->getMessage(),
            );
            return null;
        }
    }

    /**
     * Get full Service objects associated with a specialist
     *
     * @param int $id_especialista
     * @return Servicio[]
     */
    public function getServiciosForEspecialista(int $id_especialista): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT s.* FROM servicios s
                INNER JOIN especialista_servicios es ON s.id_servicio = es.id_servicio
                WHERE es.id_especialista = :id_especialista",
            );
            $stmt->execute(["id_especialista" => $id_especialista]);

            $servicios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $servicios[] = Servicio::fromDatabase($row);
            }
            return $servicios;
        } catch (\Exception $e) {
            error_log(
                "Error al obtener servicios del especialista: " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Get full Especialista objects associated with a service
     *
     * @param int $id_servicio
     * @return Especialista[]
     */
    public function getEspecialistasForServicio(int $id_servicio): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT e.* FROM especialistas e
                INNER JOIN especialista_servicios es ON e.id_especialista = es.id_especialista
                WHERE es.id_servicio = :id_servicio",
            );
            $stmt->execute(["id_servicio" => $id_servicio]);

            $especialistas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $especialistas[] = Especialista::fromDatabase($row);
            }
            return $especialistas;
        } catch (\Exception $e) {
            error_log(
                "Error al obtener especialistas del servicio: " .
                    $e->getMessage(),
            );
            return [];
        }
    }

    /**
     * Create a new relationship between an especialista and a servicio
     *
     * @param EspecialistaServicio $especialistaServicio The relationship to create
     * @return void
     */
    public function addEspecialistaServicio(
        EspecialistaServicio $especialistaServicio,
    ): void {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO especialista_servicios (id_especialista, id_servicio) VALUES (:id_especialista, :id_servicio)",
            );
            $stmt->execute([
                "id_especialista" => $especialistaServicio->getIdEspecialista(),
                "id_servicio" => $especialistaServicio->getIdServicio(),
            ]);
        } catch (\Exception $e) {
            error_log(
                "Error al agregar el especialista_servicio: " .
                    $e->getMessage(),
            );
        }
    }

    /**
     * Delete a relationship between an especialista and a servicio
     *
     * @param int $id_especialista The especialista ID
     * @param int $id_servicio The servicio ID
     * @return void
     */
    public function deleteEspecialistaServicio(
        int $id_especialista,
        int $id_servicio,
    ): void {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM especialista_servicios WHERE id_especialista = :id_especialista AND id_servicio = :id_servicio",
            );
            $stmt->execute([
                "id_especialista" => $id_especialista,
                "id_servicio" => $id_servicio,
            ]);
        } catch (\Exception $e) {
            error_log(
                "Error al eliminar el especialista_servicio: " .
                    $e->getMessage(),
            );
        }
    }

    /**
     * Delete all servicio relationships for a specific especialista
     *
     * @param int $id_especialista
     * @return void
     */
    public function deleteAllServiciosForEspecialista(int $id_especialista): void
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM especialista_servicios WHERE id_especialista = :id_especialista",
            );
            $stmt->execute(["id_especialista" => $id_especialista]);
        } catch (\Exception $e) {
            error_log(
                "Error al eliminar servicios del especialista: " .
                    $e->getMessage(),
            );
        }
    }
}
