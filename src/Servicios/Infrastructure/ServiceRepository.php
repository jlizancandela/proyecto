<?php

namespace Reservas\Infrastructure;

use PDO;
use PDOException;
use Servicios\Domain\Servicio;

class ServiceRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAllServices(): array
    {
        try {
            $query = "SELECT * FROM servicios";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $servicios = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $servicios[] = Servicio::fromDatabase($row);
            }
            return $servicios;
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al obtener todos los servicios: " . $e->getMessage(),
            );
        }
    }

    public function getServiceById(int $id): ?Servicio
    {
        try {
            $query = "SELECT * FROM servicios WHERE id_servicio = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? Servicio::fromDatabase($row) : null;
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al obtener servicio por ID: " . $e->getMessage(),
            );
        }
    }

    public function createService(Servicio $servicio): int
    {
        try {
            $query = "INSERT INTO servicios (nombre_servicio, duracion_minutos, descripcion, precio) 
                      VALUES (:nombre_servicio, :duracion_minutos, :descripcion, :precio)";
            $stmt = $this->db->prepare($query);

            $nombreServicio = $servicio->getNombreServicio();
            $duracionMinutos = $servicio->getDuracionMinutos();
            $descripcion = $servicio->getDescripcion();
            $precio = $servicio->getPrecio();

            $stmt->bindParam(":nombre_servicio", $nombreServicio);
            $stmt->bindParam(":duracion_minutos", $duracionMinutos);
            $stmt->bindParam(":descripcion", $descripcion);
            $stmt->bindParam(":precio", $precio);

            $stmt->execute();
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al crear servicio: " . $e->getMessage(),
            );
        }
    }

    public function updateService(Servicio $servicio): void
    {
        try {
            $query = "UPDATE servicios 
                      SET nombre_servicio = :nombre_servicio, 
                          duracion_minutos = :duracion_minutos, 
                          descripcion = :descripcion, 
                          precio = :precio 
                      WHERE id_servicio = :id";
            $stmt = $this->db->prepare($query);

            $id = $servicio->getIdServicio();
            $nombreServicio = $servicio->getNombreServicio();
            $duracionMinutos = $servicio->getDuracionMinutos();
            $descripcion = $servicio->getDescripcion();
            $precio = $servicio->getPrecio();

            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":nombre_servicio", $nombreServicio);
            $stmt->bindParam(":duracion_minutos", $duracionMinutos);
            $stmt->bindParam(":descripcion", $descripcion);
            $stmt->bindParam(":precio", $precio);

            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al actualizar servicio: " . $e->getMessage(),
            );
        }
    }

    public function deleteService(int $id): void
    {
        try {
            $query = "DELETE FROM servicios WHERE id_servicio = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al eliminar servicio: " . $e->getMessage(),
            );
        }
    }
    // TODO: Implementar la eliminacion en cascada
    public function deleteServiceCascade(int $id): void {}
}
