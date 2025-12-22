<?php

namespace Especialistas\Infrastructure;

use Especialistas\Domain\Especialista;
use Especialistas\Application\EspecialistaUsuarioDTO;
use PDO;

class EspecialistaRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getDb(): PDO
    {
        return $this->db;
    }

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
            error_log("Error al obtener especialistas: " . $e->getMessage());
            return [];
        }
    }

    public function getEspecialistaConUsuarioById(int $id): ?EspecialistaUsuarioDTO
    {
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
            error_log("Error al obtener especialista: " . $e->getMessage());
            return null;
        }
    }

    public function addEspecialista(Especialista $especialista): void
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO especialistas (id_usuario, descripcion, foto_url) 
                 VALUES (:id_usuario, :descripcion, :foto_url)"
            );
            $stmt->execute([
                "id_usuario" => $especialista->getIdUsuario(),
                "descripcion" => $especialista->getDescripcion(),
                "foto_url" => $especialista->getFotoUrl(),
            ]);
        } catch (\Exception $e) {
            error_log("Error al agregar especialista: " . $e->getMessage());
        }
    }

    public function updateEspecialista(Especialista $especialista): void
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE especialistas 
                 SET id_usuario = :id_usuario, descripcion = :descripcion, foto_url = :foto_url 
                 WHERE id = :id"
            );
            $stmt->execute([
                "id" => $especialista->getIdEspecialista(),
                "id_usuario" => $especialista->getIdUsuario(),
                "descripcion" => $especialista->getDescripcion(),
                "foto_url" => $especialista->getFotoUrl(),
            ]);
        } catch (\Exception $e) {
            error_log("Error al actualizar especialista: " . $e->getMessage());
        }
    }

    public function getEspecialistasDisponibles(int $idServicio, string $fecha, ?int $limit = null, ?int $offset = null): array
    {
        try {
            // Obtener especialistas que ofrecen este servicio
            $query = "
                SELECT DISTINCT
                    e.id_especialista,
                    e.id_usuario,
                    e.descripcion,
                    e.foto_url,
                    u.nombre,
                    u.apellidos,
                    u.email,
                    u.telefono
                FROM ESPECIALISTA e
                INNER JOIN USUARIO u ON e.id_usuario = u.id_usuario
                INNER JOIN ESPECIALISTA_SERVICIO es ON e.id_especialista = es.id_especialista
                WHERE es.id_servicio = :id_servicio
                AND u.activo = 1
            ";

            // Add pagination if limit is provided
            if ($limit !== null) {
                $query .= " LIMIT :limit";
                if ($offset !== null) {
                    $query .= " OFFSET :offset";
                }
            }

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_servicio', $idServicio, PDO::PARAM_INT);

            if ($limit !== null) {
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                if ($offset !== null) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                }
            }

            $stmt->execute();

            $especialistas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Obtener duración del servicio
                $stmtServicio = $this->db->prepare("
                    SELECT duracion_minutos FROM SERVICIO WHERE id_servicio = :id_servicio
                ");
                $stmtServicio->execute(['id_servicio' => $idServicio]);
                $servicio = $stmtServicio->fetch(PDO::FETCH_ASSOC);
                $duracionMinutos = $servicio['duracion_minutos'];

                // Obtener reservas del especialista para ese día
                $stmtReservas = $this->db->prepare("
                    SELECT hora_inicio, hora_fin 
                    FROM RESERVA 
                    WHERE id_especialista = :id_especialista 
                    AND fecha_reserva = :fecha
                    AND estado != 'Cancelada'
                    ORDER BY hora_inicio
                ");
                $stmtReservas->execute([
                    'id_especialista' => $row['id_especialista'],
                    'fecha' => $fecha
                ]);

                $reservas = $stmtReservas->fetchAll(PDO::FETCH_ASSOC);

                // Calcular horas disponibles (horario de 9:00 a 20:00)
                $horasDisponibles = $this->calcularHorasDisponibles($reservas, $duracionMinutos);

                $especialistas[] = [
                    'id_especialista' => $row['id_especialista'],
                    'nombre' => $row['nombre'],
                    'apellidos' => $row['apellidos'],
                    'descripcion' => $row['descripcion'],
                    'foto_url' => $row['foto_url'],
                    'horas_disponibles' => $horasDisponibles
                ];
            }

            return $especialistas;
        } catch (\Exception $e) {
            error_log("Error al obtener especialistas disponibles: " . $e->getMessage());
            return [];
        }
    }

    private function calcularHorasDisponibles(array $reservas, int $duracionMinutos): array
    {
        $horaInicio = new \DateTime('09:00');
        $horaFin = new \DateTime('20:00');
        $intervalo = 30; // Intervalos de 30 minutos

        $horasDisponibles = [];
        $horaActual = clone $horaInicio;

        while ($horaActual < $horaFin) {
            $horaFinSlot = clone $horaActual;
            $horaFinSlot->modify("+{$duracionMinutos} minutes");

            // Si el slot se pasa del horario de cierre, no lo incluimos
            if ($horaFinSlot > $horaFin) {
                break;
            }

            // Verificar si hay conflicto con alguna reserva
            $hayConflicto = false;
            foreach ($reservas as $reserva) {
                $reservaInicio = new \DateTime($reserva['hora_inicio']);
                $reservaFin = new \DateTime($reserva['hora_fin']);

                // Hay conflicto si el slot se solapa con la reserva
                if ($horaActual < $reservaFin && $horaFinSlot > $reservaInicio) {
                    $hayConflicto = true;
                    break;
                }
            }

            if (!$hayConflicto) {
                $horasDisponibles[] = $horaActual->format('H:i');
            }

            $horaActual->modify("+{$intervalo} minutes");
        }

        return $horasDisponibles;
    }

    public function countEspecialistasDisponibles(int $idServicio, string $fecha): int
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT e.id_especialista) as total
                FROM ESPECIALISTA e
                INNER JOIN USUARIO u ON e.id_usuario = u.id_usuario
                INNER JOIN ESPECIALISTA_SERVICIO es ON e.id_especialista = es.id_especialista
                WHERE es.id_servicio = :id_servicio
                AND u.activo = 1
            ");
            $stmt->execute(['id_servicio' => $idServicio]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (\Exception $e) {
            error_log("Error al contar especialistas disponibles: " . $e->getMessage());
            return 0;
        }
    }

    public function deleteEspecialista(int $id): void
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM especialistas WHERE id = :id");
            $stmt->execute(["id" => $id]);
        } catch (\Exception $e) {
            error_log("Error al eliminar especialista: " . $e->getMessage());
        }
    }
}
