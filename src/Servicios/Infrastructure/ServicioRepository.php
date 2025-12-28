<?php

/**
 * Data access for salon services stored in the database.
 */

namespace Servicios\Infrastructure;

use Servicios\Domain\Servicio;
use PDO;

/**
 * Handles all database operations for the services table.
 */
class ServicioRepository
{
    private PDO $db;

    /**
     * ServicioRepository constructor.
     * @param PDO $db The PDO database connection.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Gets all services, optionally filtered by active status
     * @param bool|null $activo Filter by active status (null = all)
     * @return Servicio[] Array of Servicio objects
     */
    public function getAllServicios(?bool $activo = null): array
    {
        try {
            $query = "SELECT * FROM SERVICIO";
            if ($activo !== null) {
                $query .= " WHERE activo = " . ($activo ? '1' : '0');
            }
            $query .= " ORDER BY nombre_servicio ASC";

            $stmt = $this->db->query($query);

            $servicios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $servicios[] = Servicio::fromDatabase($row);
            }
            return $servicios;
        } catch (\Exception $e) {
            error_log("Error al obtener servicios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves a service by its ID.
     * @param int $id The ID of the service.
     * @return Servicio|null The Servicio object or null if not found.
     */
    public function getServicioById(int $id): ?Servicio
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM SERVICIO WHERE id_servicio = :id");
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? Servicio::fromDatabase($row) : null;
        } catch (\Exception $e) {
            error_log("Error al obtener servicio: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Saves a new service in the database.
     * @param Servicio $servicio The service object to save.
     * @return int|null The ID of the newly created service or null on failure.
     */
    public function save(Servicio $servicio): ?int
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO SERVICIO (nombre_servicio, duracion_minutos, precio, descripcion, activo)
                VALUES (:nombre_servicio, :duracion_minutos, :precio, :descripcion, :activo)"
            );

            $stmt->execute([
                'nombre_servicio' => $servicio->getNombreServicio(),
                'duracion_minutos' => $servicio->getDuracionMinutos(),
                'precio' => $servicio->getPrecio(),
                'descripcion' => $servicio->getDescripcion(),
                'activo' => $servicio->isActivo() ? 1 : 0,
            ]);

            return (int) $this->db->lastInsertId();
        } catch (\Exception $e) {
            error_log("Error al guardar servicio: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Updates an existing service record.
     * @param Servicio $servicio The service object with updated data.
     * @return bool True on success, false on failure.
     */
    public function update(Servicio $servicio): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE SERVICIO
                SET nombre_servicio = :nombre_servicio,
                    duracion_minutos = :duracion_minutos,
                    precio = :precio,
                    descripcion = :descripcion,
                    activo = :activo
                WHERE id_servicio = :id_servicio"
            );

            return $stmt->execute([
                'id_servicio' => $servicio->getIdServicio(),
                'nombre_servicio' => $servicio->getNombreServicio(),
                'duracion_minutos' => $servicio->getDuracionMinutos(),
                'precio' => $servicio->getPrecio(),
                'descripcion' => $servicio->getDescripcion(),
                'activo' => $servicio->isActivo() ? 1 : 0,
            ]);
        } catch (\Exception $e) {
            error_log("Error al actualizar servicio: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deactivates a service (soft delete)
     * @param int $id Service ID
     * @return bool Success status
     */
    public function deactivate(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("UPDATE SERVICIO SET activo = 0 WHERE id_servicio = :id");
            return $stmt->execute(['id' => $id]);
        } catch (\Exception $e) {
            error_log("Error al desactivar servicio: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Activates a service
     * @param int $id Service ID
     * @return bool Success status
     */
    public function activate(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("UPDATE SERVICIO SET activo = 1 WHERE id_servicio = :id");
            return $stmt->execute(['id' => $id]);
        } catch (\Exception $e) {
            error_log("Error al activar servicio: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gets the total count of services in the system.
     * @return int The total number of services.
     */
    public function getTotalCount(): int
    {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM SERVICIO");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (\Exception $e) {
            error_log("Error al obtener total de servicios: " . $e->getMessage());
            return 0;
        }
    }
}
