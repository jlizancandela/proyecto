<?php

/**
 * EspecialistaServicioRepository
 *
 * Repository for managing the many-to-many relationship between specialists and services.
 * Provides methods to link and unlink specialists with services they provide.
 */

namespace Especialistas\Infrastructure;

use Especialistas\Domain\EspecialistaServicio;
use Servicios\Domain\Servicio;
use Especialistas\Domain\Especialista;
use PDO;

class EspecialistaServicioRepository
{
    private PDO $db;

    /**
     * EspecialistaServicioRepository constructor.
     * @param PDO $db The PDO database connection.
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Retrieves a specific specialist-service link.
     *
     * @param int $id_especialista The ID of the specialist.
     * @param int $id_servicio The ID of the service.
     * @return EspecialistaServicio|null The EspecialistaServicio object if found, null otherwise.
     */
    public function getEspecialistaServicio(int $id_especialista, int $id_servicio): ?EspecialistaServicio
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM ESPECIALISTA_SERVICIO
                 WHERE id_especialista = :id_especialista AND id_servicio = :id_servicio"
            );
            $stmt->execute([
                "id_especialista" => $id_especialista,
                "id_servicio" => $id_servicio,
            ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? EspecialistaServicio::fromDatabase($row) : null;
        } catch (\Exception $e) {
            error_log("Error al obtener especialista_servicio: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieves all services offered by a specific specialist.
     *
     * @param int $id_especialista The ID of the specialist.
     * @return Servicio[] An array of Servicio objects.
     */
    public function getServiciosForEspecialista(int $id_especialista): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT s.* FROM SERVICIO s
                 INNER JOIN ESPECIALISTA_SERVICIO es ON s.id_servicio = es.id_servicio
                 WHERE es.id_especialista = :id_especialista"
            );
            $stmt->execute(["id_especialista" => $id_especialista]);

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
     * Retrieves all specialists who provide a specific service.
     *
     * @param int $id_servicio The ID of the service.
     * @return Especialista[] An array of Especialista objects.
     */
    public function getEspecialistasForServicio(int $id_servicio): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT e.* FROM ESPECIALISTA e
                 INNER JOIN ESPECIALISTA_SERVICIO es ON e.id_especialista = es.id_especialista
                 WHERE es.id_servicio = :id_servicio"
            );
            $stmt->execute(["id_servicio" => $id_servicio]);

            $especialistas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $especialistas[] = Especialista::fromDatabase($row);
            }
            return $especialistas;
        } catch (\Exception $e) {
            error_log("Error al obtener especialistas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Adds a new specialist-service link to the database.
     *
     * @param EspecialistaServicio $especialistaServicio The EspecialistaServicio object to add.
     * @return void
     */
    public function addEspecialistaServicio(EspecialistaServicio $especialistaServicio): void
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO ESPECIALISTA_SERVICIO (id_especialista, id_servicio)
                 VALUES (:id_especialista, :id_servicio)"
            );
            $stmt->execute([
                "id_especialista" => $especialistaServicio->getIdEspecialista(),
                "id_servicio" => $especialistaServicio->getIdServicio(),
            ]);
        } catch (\Exception $e) {
            error_log("Error al agregar especialista_servicio: " . $e->getMessage());
        }
    }

    /**
     * Deletes a specific specialist-service link from the database.
     *
     * @param int $id_especialista The ID of the specialist.
     * @param int $id_servicio The ID of the service.
     * @return void
     */
    public function deleteEspecialistaServicio(int $id_especialista, int $id_servicio): void
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM ESPECIALISTA_SERVICIO
                 WHERE id_especialista = :id_especialista AND id_servicio = :id_servicio"
            );
            $stmt->execute([
                "id_especialista" => $id_especialista,
                "id_servicio" => $id_servicio,
            ]);
        } catch (\Exception $e) {
            error_log("Error al eliminar especialista_servicio: " . $e->getMessage());
        }
    }

    /**
     * Deletes all service links for a specific specialist.
     *
     * @param int $id_especialista The ID of the specialist.
     * @return void
     */
    public function deleteAllServiciosForEspecialista(int $id_especialista): void
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM ESPECIALISTA_SERVICIO WHERE id_especialista = :id_especialista"
            );
            $stmt->execute(["id_especialista" => $id_especialista]);
        } catch (\Exception $e) {
            error_log("Error al eliminar servicios: " . $e->getMessage());
        }
    }
}
