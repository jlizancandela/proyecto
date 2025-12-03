<?php

namespace Reservas\Infrastructure;

use Reservas\Domain\Reserva;
use Reservas\Application\ReservaCompletaDTO;
use PDO;

class ReservaRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return ReservaCompletaDTO[]
     */
    public function getAllReservasCompletas(
        int $limit = 50,
        int $offset = 0,
    ): array {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    r.*,
                    c.nombre as cliente_nombre, 
                    c.apellidos as cliente_apellidos, 
                    c.email as cliente_email, 
                    c.telefono as cliente_telefono,
                    u.nombre as especialista_nombre, 
                    u.apellidos as especialista_apellidos,
                    u.email as especialista_email, 
                    u.telefono as especialista_telefono,
                    e.descripcion as especialista_descripcion, 
                    e.foto_url as especialista_foto_url,
                    s.nombre_servicio, 
                    s.duracion_minutos, 
                    s.precio, 
                    s.descripcion as servicio_descripcion
                FROM RESERVA r
                INNER JOIN USUARIO c ON r.id_cliente = c.id_usuario
                INNER JOIN ESPECIALISTA e ON r.id_especialista = e.id_especialista
                INNER JOIN USUARIO u ON e.id_usuario = u.id_usuario
                INNER JOIN SERVICIO s ON r.id_servicio = s.id_servicio
                ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC
                LIMIT :limit OFFSET :offset
            ");

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $reservas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reservas[] = ReservaCompletaDTO::fromDatabase($row);
            }
            return $reservas;
        } catch (\Exception $e) {
            error_log("Error al obtener reservas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * @param int $id
     * @return ReservaCompletaDTO|null
     */
    public function getReservaCompletaById(int $id): ?ReservaCompletaDTO
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    r.*,
                    c.nombre as cliente_nombre, 
                    c.apellidos as cliente_apellidos, 
                    c.email as cliente_email, 
                    c.telefono as cliente_telefono,
                    u.nombre as especialista_nombre, 
                    u.apellidos as especialista_apellidos,
                    u.email as especialista_email, 
                    u.telefono as especialista_telefono,
                    e.descripcion as especialista_descripcion, 
                    e.foto_url as especialista_foto_url,
                    s.nombre_servicio, 
                    s.duracion_minutos, 
                    s.precio, 
                    s.descripcion as servicio_descripcion
                FROM RESERVA r
                INNER JOIN USUARIO c ON r.id_cliente = c.id_usuario
                INNER JOIN ESPECIALISTA e ON r.id_especialista = e.id_especialista
                INNER JOIN USUARIO u ON e.id_usuario = u.id_usuario
                INNER JOIN SERVICIO s ON r.id_servicio = s.id_servicio
                WHERE r.id_reserva = :id
            ");

            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? ReservaCompletaDTO::fromDatabase($row) : null;
        } catch (\Exception $e) {
            error_log("Error al obtener reserva: " . $e->getMessage());
            return null;
        }
    }

    /**
     * @param Reserva $reserva
     * @return int|null
     */
    public function addReserva(Reserva $reserva): ?int
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO RESERVA 
                (id_cliente, id_especialista, id_servicio, fecha_reserva, 
                 hora_inicio, hora_fin, estado, observaciones) 
                VALUES 
                (:id_cliente, :id_especialista, :id_servicio, :fecha_reserva, 
                 :hora_inicio, :hora_fin, :estado, :observaciones)",
            );

            $stmt->execute([
                'id_cliente' => $reserva->getIdCliente(),
                'id_especialista' => $reserva->getIdEspecialista(),
                'id_servicio' => $reserva->getIdServicio(),
                'fecha_reserva' => $reserva->getFechaReserva()->format('Y-m-d'),
                'hora_inicio' => $reserva->getHoraInicio(),
                'hora_fin' => $reserva->getHoraFin(),
                'estado' => $reserva->getEstado(),
                'observaciones' => $reserva->getObservaciones(),
            ]);

            return (int) $this->db->lastInsertId();
        } catch (\Exception $e) {
            error_log("Error al agregar reserva: " . $e->getMessage());
            return null;
        }
    }

    /**
     * @param Reserva $reserva
     * @return bool
     */
    public function updateReserva(Reserva $reserva): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE RESERVA 
                SET id_cliente = :id_cliente,
                    id_especialista = :id_especialista,
                    id_servicio = :id_servicio,
                    fecha_reserva = :fecha_reserva,
                    hora_inicio = :hora_inicio,
                    hora_fin = :hora_fin,
                    estado = :estado,
                    observaciones = :observaciones
                WHERE id_reserva = :id_reserva",
            );

            return $stmt->execute([
                'id_reserva' => $reserva->getIdReserva(),
                'id_cliente' => $reserva->getIdCliente(),
                'id_especialista' => $reserva->getIdEspecialista(),
                'id_servicio' => $reserva->getIdServicio(),
                'fecha_reserva' => $reserva->getFechaReserva()->format('Y-m-d'),
                'hora_inicio' => $reserva->getHoraInicio(),
                'hora_fin' => $reserva->getHoraFin(),
                'estado' => $reserva->getEstado(),
                'observaciones' => $reserva->getObservaciones(),
            ]);
        } catch (\Exception $e) {
            error_log("Error al actualizar reserva: " . $e->getMessage());
            return false;
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteReserva(int $id): bool
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM RESERVA WHERE id_reserva = :id",
            );
            return $stmt->execute(['id' => $id]);
        } catch (\Exception $e) {
            error_log("Error al eliminar reserva: " . $e->getMessage());
            return false;
        }
    }

    /**
     * @param int $id_cliente
     * @param int $limit
     * @param int $offset
     * @return ReservaCompletaDTO[]
     */
    public function findByClient(
        int $id_cliente,
        int $limit = 50,
        int $offset = 0,
    ): array {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    r.*,
                    c.nombre as cliente_nombre, 
                    c.apellidos as cliente_apellidos, 
                    c.email as cliente_email, 
                    c.telefono as cliente_telefono,
                    u.nombre as especialista_nombre, 
                    u.apellidos as especialista_apellidos,
                    u.email as especialista_email, 
                    u.telefono as especialista_telefono,
                    e.descripcion as especialista_descripcion, 
                    e.foto_url as especialista_foto_url,
                    s.nombre_servicio, 
                    s.duracion_minutos, 
                    s.precio, 
                    s.descripcion as servicio_descripcion
                FROM RESERVA r
                INNER JOIN USUARIO c ON r.id_cliente = c.id_usuario
                INNER JOIN ESPECIALISTA e ON r.id_especialista = e.id_especialista
                INNER JOIN USUARIO u ON e.id_usuario = u.id_usuario
                INNER JOIN SERVICIO s ON r.id_servicio = s.id_servicio
                WHERE r.id_cliente = :id_cliente
                ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC
                LIMIT :limit OFFSET :offset
            ");

            $stmt->bindValue(':id_cliente', $id_cliente, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $reservas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reservas[] = ReservaCompletaDTO::fromDatabase($row);
            }
            return $reservas;
        } catch (\Exception $e) {
            error_log("Error al obtener reservas del cliente: " . $e->getMessage());
            return [];
        }
    }

    /**
     * @param array{
     *     cliente?: int,
     *     especialista?: int,
     *     servicio?: int,
     *     estado?: string,
     *     fecha_desde?: string,
     *     fecha_hasta?: string
     * } $filtros
     * @param int $limit
     * @param int $offset
     * @return ReservaCompletaDTO[]
     */
    public function findAllFiltered(
        array $filtros = [],
        int $limit = 50,
        int $offset = 0,
    ): array {
        try {
            $sql = "
                SELECT 
                    r.*,
                    c.nombre as cliente_nombre, 
                    c.apellidos as cliente_apellidos, 
                    c.email as cliente_email, 
                    c.telefono as cliente_telefono,
                    u.nombre as especialista_nombre, 
                    u.apellidos as especialista_apellidos,
                    u.email as especialista_email, 
                    u.telefono as especialista_telefono,
                    e.descripcion as especialista_descripcion, 
                    e.foto_url as especialista_foto_url,
                    s.nombre_servicio, 
                    s.duracion_minutos, 
                    s.precio, 
                    s.descripcion as servicio_descripcion
                FROM RESERVA r
                INNER JOIN USUARIO c ON r.id_cliente = c.id_usuario
                INNER JOIN ESPECIALISTA e ON r.id_especialista = e.id_especialista
                INNER JOIN USUARIO u ON e.id_usuario = u.id_usuario
                INNER JOIN SERVICIO s ON r.id_servicio = s.id_servicio
                WHERE 1=1
            ";

            $params = [];

            if (isset($filtros['cliente'])) {
                $sql .= " AND r.id_cliente = :id_cliente";
                $params['id_cliente'] = $filtros['cliente'];
            }

            if (isset($filtros['especialista'])) {
                $sql .= " AND r.id_especialista = :id_especialista";
                $params['id_especialista'] = $filtros['especialista'];
            }

            if (isset($filtros['servicio'])) {
                $sql .= " AND r.id_servicio = :id_servicio";
                $params['id_servicio'] = $filtros['servicio'];
            }

            if (isset($filtros['estado'])) {
                $sql .= " AND r.estado = :estado";
                $params['estado'] = $filtros['estado'];
            }

            if (isset($filtros['fecha_desde'])) {
                $sql .= " AND r.fecha_reserva >= :fecha_desde";
                $params['fecha_desde'] = $filtros['fecha_desde'];
            }

            if (isset($filtros['fecha_hasta'])) {
                $sql .= " AND r.fecha_reserva <= :fecha_hasta";
                $params['fecha_hasta'] = $filtros['fecha_hasta'];
            }

            $sql .= " ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC";
            $sql .= " LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            $reservas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reservas[] = ReservaCompletaDTO::fromDatabase($row);
            }
            return $reservas;
        } catch (\Exception $e) {
            error_log("Error al obtener reservas filtradas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * @param string $fecha
     * @param string $hora_inicio
     * @param string $hora_fin
     * @param int $id_especialista
     * @param int|null $exclude_id_reserva
     * @return bool
     */
    public function findConflicts(
        string $fecha,
        string $hora_inicio,
        string $hora_fin,
        int $id_especialista,
        ?int $exclude_id_reserva = null,
    ): bool {
        try {
            $sql = "
                SELECT COUNT(*) as count 
                FROM RESERVA 
                WHERE id_especialista = :id_especialista 
                AND fecha_reserva = :fecha_reserva 
                AND estado != 'Cancelada'
                AND (hora_inicio < :hora_fin AND hora_fin > :hora_inicio)
            ";

            if ($exclude_id_reserva !== null) {
                $sql .= " AND id_reserva != :exclude_id";
            }

            $stmt = $this->db->prepare($sql);

            $params = [
                'id_especialista' => $id_especialista,
                'fecha_reserva' => $fecha,
                'hora_inicio' => $hora_inicio,
                'hora_fin' => $hora_fin,
            ];

            if ($exclude_id_reserva !== null) {
                $params['exclude_id'] = $exclude_id_reserva;
            }

            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['count'] > 0;
        } catch (\Exception $e) {
            error_log("Error al verificar conflictos: " . $e->getMessage());
            return true;
        }
    }
}
