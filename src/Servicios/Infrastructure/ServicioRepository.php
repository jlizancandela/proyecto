<?php

namespace Servicios\Infrastructure;

use Servicios\Domain\Servicio;
use PDO;

class ServicioRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllServicios(): array
    {
        try {
            $stmt = $this->db->query("SELECT * FROM SERVICIO ORDER BY nombre_servicio ASC");

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

    public function save(Servicio $servicio): ?int
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO SERVICIO (nombre_servicio, duracion_minutos, precio, descripcion) 
                VALUES (:nombre_servicio, :duracion_minutos, :precio, :descripcion)"
            );

            $stmt->execute([
                'nombre_servicio' => $servicio->getNombreServicio(),
                'duracion_minutos' => $servicio->getDuracionMinutos(),
                'precio' => $servicio->getPrecio(),
                'descripcion' => $servicio->getDescripcion(),
            ]);

            return (int) $this->db->lastInsertId();
        } catch (\Exception $e) {
            error_log("Error al guardar servicio: " . $e->getMessage());
            return null;
        }
    }

    public function update(Servicio $servicio): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE SERVICIO 
                SET nombre_servicio = :nombre_servicio,
                    duracion_minutos = :duracion_minutos,
                    precio = :precio,
                    descripcion = :descripcion
                WHERE id_servicio = :id_servicio"
            );

            return $stmt->execute([
                'id_servicio' => $servicio->getIdServicio(),
                'nombre_servicio' => $servicio->getNombreServicio(),
                'duracion_minutos' => $servicio->getDuracionMinutos(),
                'precio' => $servicio->getPrecio(),
                'descripcion' => $servicio->getDescripcion(),
            ]);
        } catch (\Exception $e) {
            error_log("Error al actualizar servicio: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM SERVICIO WHERE id_servicio = :id");
            return $stmt->execute(['id' => $id]);
        } catch (\Exception $e) {
            error_log("Error al eliminar servicio: " . $e->getMessage());
            return false;
        }
    }

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
