<?php

/**
 * Repository for handling booking data and database operations.
 * Allows managing reservations, including creating, updating, and searching with filters.
 */

namespace Reservas\Infrastructure;

use Reservas\Domain\Reserva;
use Reservas\Application\ReservaCompletaDTO;
use PDO;

class ReservaRepository
{
    private PDO $db;

    /**
     * Initializes the repository with the database connection.
     *
     * @param PDO $db The database connection.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    private const BASE_QUERY = "
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
    ";

    /**
     * Applies dynamic filters to the SQL query.
     *
     * @param array $filtros List of filters to apply (client, specialist, service, etc.).
     * @param string $sql The SQL query string to be modified.
     * @param array $params The parameters array to be populated.
     */
    private function applyFilters(array $filtros, string &$sql, array &$params): void
    {
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

        if (isset($filtros['cliente_search']) && $filtros['cliente_search'] !== '') {
            $sql .= " AND (c.nombre LIKE :cliente_search OR c.apellidos LIKE :cliente_search)";
            $params['cliente_search'] = "%{$filtros['cliente_search']}%";
        }
    }



    /**
     * Retrieves a complete booking with details by its ID.
     *
     * @param int $id The booking ID.
     * @return ReservaCompletaDTO|null The booking data or null if not found.
     */
    public function getReservaCompletaById(int $id): ?ReservaCompletaDTO
    {
        try {
            $stmt = $this->db->prepare(self::BASE_QUERY . "
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
     * Adds a new booking to the database.
     *
     * @param Reserva $reserva The booking entity.
     * @return int|null The ID of the created booking or null on failure.
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
     * Updates an existing booking.
     *
     * @param Reserva $reserva The booking entity with updated data.
     * @return bool True if successful, false otherwise.
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
     * Deletes a booking by its ID.
     *
     * @param int $id The booking ID.
     * @return bool True if successful, false otherwise.
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
     * Finds bookings associated with a specific client.
     *
     * @param int $id_cliente The client's user ID.
     * @param int $limit Max number of results.
     * @param int $offset Offset for pagination.
     * @return array List of bookings.
     */
    public function findByClient(
        int $id_cliente,
        int $limit = 50,
        int $offset = 0,
    ): array {
        try {
            $stmt = $this->db->prepare(self::BASE_QUERY . "
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
     * Retrieves all bookings matching the specified filters.
     *
     * @param array $filtros Filters to apply (client, specialist, service, state, date range).
     * @param int $limit Max number of results.
     * @param int $offset Offset for pagination.
     * @return array List of matching bookings.
     */
    public function findAllFiltered(
        array $filtros = [],
        int $limit = 50,
        int $offset = 0,
    ): array {
        try {
            $sql = self::BASE_QUERY . "
                WHERE 1=1
            ";

            $params = [];
            $this->applyFilters($filtros, $sql, $params);


            // Dynamic ORDER BY based on sort parameter
            $orderBy = "r.fecha_reserva DESC, r.hora_inicio DESC"; // Default

            if (isset($filtros['sort'])) {
                $order = isset($filtros['order']) && strtoupper($filtros['order']) === 'DESC' ? 'DESC' : 'ASC';

                switch ($filtros['sort']) {
                    case 'cliente':
                        $orderBy = "c.nombre $order, c.apellidos $order";
                        break;
                    case 'especialista':
                        $orderBy = "u.nombre $order, u.apellidos $order";
                        break;
                    case 'fecha':
                        $orderBy = "r.fecha_reserva $order, r.hora_inicio $order";
                        break;
                    default:
                        $orderBy = "r.fecha_reserva $order, r.hora_inicio $order";
                        break;
                }
            }

            $sql .= " ORDER BY $orderBy";
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
     * Checks for scheduling conflicts for a specialist.
     *
     * @param string $fecha Date of the booking.
     * @param string $hora_inicio Start time.
     * @param string $hora_fin End time.
     * @param int $id_especialista Specialist ID.
     * @param int|null $exclude_id_reserva Booking ID to exclude (for updates).
     * @return bool True if a conflict exists, false otherwise.
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

    /**
     * Checks for scheduling conflicts for a client.
     *
     * @param string $fecha Date of the booking.
     * @param string $hora_inicio Start time.
     * @param string $hora_fin End time.
     * @param int $id_cliente Client ID.
     * @param int|null $exclude_id_reserva Booking ID to exclude.
     * @return bool True if a conflict exists, false otherwise.
     */
    public function findClientConflicts(
        string $fecha,
        string $hora_inicio,
        string $hora_fin,
        int $id_cliente,
        ?int $exclude_id_reserva = null
    ): bool {
        try {
            $sql = "
                SELECT COUNT(*) as count
                FROM RESERVA
                WHERE id_cliente = :id_cliente
                AND fecha_reserva = :fecha_reserva
                AND estado != 'Cancelada'
                AND (hora_inicio < :hora_fin AND hora_fin > :hora_inicio)
            ";

            if ($exclude_id_reserva !== null) {
                $sql .= " AND id_reserva != :exclude_id";
            }

            $stmt = $this->db->prepare($sql);

            $params = [
                'id_cliente' => $id_cliente,
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
            error_log("Error al verificar conflictos del cliente: " . $e->getMessage());
            return true;
        }
    }

    /**
     * Finds bookings for a specific user ID (client).
     *
     * @param int $userId The user ID.
     * @param int $limit Max results.
     * @param int $offset Pagination offset.
     * @return array List of bookings.
     */
    public function findByUserId(int $userId, int $limit = 50, int $offset = 0): array
    {
        try {
            $stmt = $this->db->prepare(self::BASE_QUERY . "
                WHERE r.id_cliente = :userId
                ORDER BY r.fecha_reserva DESC, r.hora_inicio DESC
                LIMIT :limit OFFSET :offset
            ");

            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $reservas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $reservas[] = ReservaCompletaDTO::fromDatabase($row);
            }

            return $reservas;
        } catch (\Exception $e) {
            error_log("Error al obtener reservas del usuario: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Counts the total number of bookings for a specific user.
     *
     * @param int $userId The user ID.
     * @return int Total count.
     */
    public function countByUserId(int $userId): int
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total
                FROM RESERVA r
                WHERE r.id_cliente = :userId
            ");

            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$result['total'];
        } catch (\Exception $e) {
            error_log("Error al contar reservas del usuario: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Finds a booking by its ID and returns a DTO.
     *
     * @param int $reservaId The booking ID.
     * @return ReservaCompletaDTO|null The booking DTO or null if not found.
     */
    public function findById(int $reservaId): ?ReservaCompletaDTO
    {
        try {
            $stmt = $this->db->prepare(self::BASE_QUERY . "
                WHERE r.id_reserva = :reservaId
            ");

            $stmt->bindValue(':reservaId', $reservaId, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            return ReservaCompletaDTO::fromDatabase($row);
        } catch (\Exception $e) {
            error_log("Error al buscar reserva por ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Updates the status of a booking.
     *
     * @param int $reservaId The booking ID.
     * @param string $newStatus The new status.
     * @return bool True if successful, false otherwise.
     */
    public function updateStatus(int $reservaId, string $newStatus): bool
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE RESERVA
                SET estado = :status
                WHERE id_reserva = :reservaId
            ");

            $stmt->bindValue(':status', $newStatus, PDO::PARAM_STR);
            $stmt->bindValue(':reservaId', $reservaId, PDO::PARAM_INT);

            return $stmt->execute() && $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            error_log("Error al actualizar estado de reserva: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Finds bookings for a user with optional filters
     *
     * @param int $userId User/Client ID
     * @param int $limit Maximum number of results
     * @param int $offset Pagination offset
     * @param string|null $fechaDesde Start date (Y-m-d format)
     * @param string|null $fechaHasta End date (Y-m-d format)
     * @param string|null $estado Booking status
     * @return array Array of ReservaCompletaDTO
     */
    public function findByUserIdWithFilters(
        int $userId,
        int $limit = 50,
        int $offset = 0,
        ?string $fechaDesde = null,
        ?string $fechaHasta = null,
        ?string $estado = null
    ): array {
        try {
            $sql = self::BASE_QUERY . "
                WHERE r.id_cliente = :userId
            ";

            $params = ['userId' => $userId];
            $filtros = [
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
                'estado' => $estado
            ];
            $this->applyFilters($filtros, $sql, $params);

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
            error_log("Error getting user bookings with filters: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Counts bookings for a user with optional filters
     *
     * @param int $userId User/Client ID
     * @param string|null $fechaDesde Start date (Y-m-d format)
     * @param string|null $fechaHasta End date (Y-m-d format)
     * @param string|null $estado Booking status
     * @return int Total number of bookings matching criteria
     */
    public function countByUserIdWithFilters(
        int $userId,
        ?string $fechaDesde = null,
        ?string $fechaHasta = null,
        ?string $estado = null
    ): int {
        try {
            $sql = "
                SELECT COUNT(*) as total
                FROM RESERVA r
                WHERE r.id_cliente = :userId
            ";

            $params = ['userId' => $userId];
            $filtros = [
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
                'estado' => $estado
            ];
            $this->applyFilters($filtros, $sql, $params);

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$result['total'];
        } catch (\Exception $e) {
            error_log("Error al contar reservas del usuario con filtros: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Gets the latest booking for a user
     *
     * @param int $userId User/Client ID
     * @return ReservaCompletaDTO|null The latest booking or null if none found
     */
    public function findLatestByUserId(int $userId): ?ReservaCompletaDTO
    {
        try {
            $sql = "
                SELECT
                    r.*,
                    uc.nombre as cliente_nombre,
                    uc.apellidos as cliente_apellidos,
                    uc.email as cliente_email,
                    uc.telefono as cliente_telefono,
                    ue.nombre as especialista_nombre,
                    ue.apellidos as especialista_apellidos,
                    e.descripcion as especialista_descripcion,
                    e.foto_url as especialista_foto_url,
                    s.nombre_servicio,
                    s.duracion_minutos,
                    s.precio,
                    s.descripcion as servicio_descripcion
                FROM RESERVA r
                INNER JOIN USUARIO uc ON r.id_cliente = uc.id_usuario
                INNER JOIN ESPECIALISTA e ON r.id_especialista = e.id_especialista
                INNER JOIN USUARIO ue ON e.id_usuario = ue.id_usuario
                INNER JOIN SERVICIO s ON r.id_servicio = s.id_servicio
                WHERE r.id_cliente = :userId
                AND r.estado IN ('pendiente', 'confirmada')
                AND CONCAT(r.fecha_reserva, ' ', r.hora_inicio) >= NOW()
                ORDER BY r.fecha_reserva ASC, r.hora_inicio ASC
                LIMIT 1
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return null;
            }

            return ReservaCompletaDTO::fromDatabase($result);
        } catch (\Exception $e) {
            error_log("Error al obtener última reserva del usuario: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Counts all bookings with optional filters
     *
     * @param array $filtros Filters (cliente, especialista, estado, fecha_desde, fecha_hasta)
     * @return int Total count of bookings matching filters
     */
    public function countAllFiltered(array $filtros = []): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM RESERVA r WHERE 1=1";
            $params = [];
            $this->applyFilters($filtros, $sql, $params);

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$result['total'];
        } catch (\Exception $e) {
            error_log("Error counting filtered bookings: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Finds bookings for a specific specialist with optional filters
     * @param int $especialistaId Specialist ID
     * @param int $limit Maximum results
     * @param int $offset Pagination offset
     * @param string|null $fechaDesde Start date (Y-m-d format)
     * @param string|null $fechaHasta End date (Y-m-d format)
     * @param string|null $estado Booking status
     * @param string|null $clienteSearch Client name search
     * @return array Array of ReservaCompletaDTO
     */
    public function findByEspecialistaIdWithFilters(
        int $especialistaId,
        int $limit = 50,
        int $offset = 0,
        ?string $fechaDesde = null,
        ?string $fechaHasta = null,
        ?string $estado = null,
        ?string $clienteSearch = null
    ): array {
        try {
            $sql = self::BASE_QUERY . "
                WHERE r.id_especialista = :especialista_id
            ";

            $params = ['especialista_id' => $especialistaId];
            $filtros = [
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
                'estado' => $estado,
                'cliente_search' => $clienteSearch
            ];
            $this->applyFilters($filtros, $sql, $params);

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
            error_log("Error getting specialist bookings with filters: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Counts bookings for a specific specialist with optional filters
     * @param int $especialistaId Specialist ID
     * @param string|null $fechaDesde Start date (Y-m-d format)
     * @param string|null $fechaHasta End date (Y-m-d format)
     * @param string|null $estado Booking status
     * @param string|null $clienteSearch Client name search
     * @return int Total count
     */
    public function countByEspecialistaIdWithFilters(
        int $especialistaId,
        ?string $fechaDesde = null,
        ?string $fechaHasta = null,
        ?string $estado = null,
        ?string $clienteSearch = null
    ): int {
        try {
            $sql = "
                SELECT COUNT(*) as total
                FROM RESERVA r
                INNER JOIN USUARIO c ON r.id_cliente = c.id_usuario
                WHERE r.id_especialista = :especialista_id
            ";

            $params = ['especialista_id' => $especialistaId];
            $filtros = [
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta,
                'estado' => $estado,
                'cliente_search' => $clienteSearch
            ];
            $this->applyFilters($filtros, $sql, $params);

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int)$result['total'];
        } catch (\Exception $e) {
            error_log("Error counting specialist bookings with filters: " . $e->getMessage());
            return 0;
        }
    }
}
