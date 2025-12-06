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
