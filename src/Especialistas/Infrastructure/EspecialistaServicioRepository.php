<?php

namespace Especialistas\Infrastructure;

use Especialistas\Domain\EspecialistaServicio;
use Servicios\Domain\Servicio;
use Especialistas\Domain\Especialista;
use PDO;

class EspecialistaServicioRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getEspecialistaServicio(int $id_especialista, int $id_servicio): ?EspecialistaServicio
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM especialista_servicios 
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

    public function getServiciosForEspecialista(int $id_especialista): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT s.* FROM servicios s
                 INNER JOIN especialista_servicios es ON s.id_servicio = es.id_servicio
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

    public function getEspecialistasForServicio(int $id_servicio): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT e.* FROM especialistas e
                 INNER JOIN especialista_servicios es ON e.id_especialista = es.id_especialista
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

    public function addEspecialistaServicio(EspecialistaServicio $especialistaServicio): void
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO especialista_servicios (id_especialista, id_servicio) 
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

    public function deleteEspecialistaServicio(int $id_especialista, int $id_servicio): void
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM especialista_servicios 
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

    public function deleteAllServiciosForEspecialista(int $id_especialista): void
    {
        try {
            $stmt = $this->db->prepare(
                "DELETE FROM especialista_servicios WHERE id_especialista = :id_especialista"
            );
            $stmt->execute(["id_especialista" => $id_especialista]);
        } catch (\Exception $e) {
            error_log("Error al eliminar servicios: " . $e->getMessage());
        }
    }
}
