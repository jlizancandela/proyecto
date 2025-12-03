<?php

namespace Usuarios\Infrastructure;

use PDO;
use PDOException;
use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;

class UserRepository
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @return Usuario[]
     */
    public function getAllUsers($limit = 10, $offset = 0): array
    {
        try {
            $query = "SELECT * FROM USUARIO LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":limit", $limit);
            $stmt->bindParam(":offset", $offset);
            $stmt->execute();

            $users = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = Usuario::fromDatabase($row);
            }
            return $users;
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al obtener todos los usuarios: " . $e->getMessage(),
            );
        }
    }

    public function getTotalUsers(): int
    {
        try {
            $query = "SELECT COUNT(*) as total FROM USUARIO";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result["total"];
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al contar usuarios: " . $e->getMessage(),
            );
        }
    }

    public function getUserById(int $id): ?Usuario
    {
        try {
            $query = "SELECT * FROM USUARIO WHERE id_usuario = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? Usuario::fromDatabase($row) : null;
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al obtener usuario por ID: " . $e->getMessage(),
            );
        }
    }

    public function getUserByRole(UserRole $role): array
    {
        try {
            $query = "SELECT * FROM USUARIO WHERE rol = :role";
            $stmt = $this->db->prepare($query);
            $roleValue = $role->value;
            $stmt->bindParam(":role", $roleValue);
            $stmt->execute();

            $users = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = Usuario::fromDatabase($row);
            }
            return $users;
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al obtener usuarios por rol: " . $e->getMessage(),
            );
        }
    }

    public function getUserByEmail(string $email): ?Usuario
    {
        try {
            $query = "SELECT * FROM USUARIO WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? Usuario::fromDatabase($row) : null;
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al obtener usuario por email: " . $e->getMessage(),
            );
        }
    }

    public function addUser(Usuario $user): int
    {
        try {
            $query = "INSERT INTO USUARIO (rol, nombre, apellidos, email, telefono, password_hash, fecha_registro, activo) 
                  VALUES (:rol, :nombre, :apellidos, :email, :telefono, :password_hash, :fecha_registro, :activo)";
            $stmt = $this->db->prepare($query);

            $rol = $user->getRol()->value;
            $nombre = $user->getNombre();
            $apellidos = $user->getApellidos();
            $email = $user->getEmail();
            $telefono = $user->getTelefono();
            $passwordHash = $user->getPassword();
            $fechaRegistro = $user->getFechaRegistro()->format("Y-m-d");
            $activo = $user->getActivo() ? 1 : 0;

            $stmt->bindParam(":rol", $rol);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":apellidos", $apellidos);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":password_hash", $passwordHash);
            $stmt->bindParam(":fecha_registro", $fechaRegistro);
            $stmt->bindParam(":activo", $activo);

            $stmt->execute();

            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al insertar usuario: " . $e->getMessage(),
            );
        }
    }

    public function updateUser(Usuario $user): void
    {
        try {
            $query =
                "UPDATE USUARIO SET rol = :rol, nombre = :nombre, apellidos = :apellidos, email = :email, telefono = :telefono, password_hash = :password_hash, fecha_registro = :fecha_registro, activo = :activo WHERE id_usuario = :id";
            $stmt = $this->db->prepare($query);

            $rol = $user->getRol()->value;
            $nombre = $user->getNombre();
            $apellidos = $user->getApellidos();
            $email = $user->getEmail();
            $telefono = $user->getTelefono();
            $passwordHash = $user->getPassword();
            $fechaRegistro = $user->getFechaRegistro()->format("Y-m-d");
            $activo = $user->getActivo() ? 1 : 0;
            $id = $user->getId();

            $stmt->bindParam(":rol", $rol);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":apellidos", $apellidos);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":password_hash", $passwordHash);
            $stmt->bindParam(":fecha_registro", $fechaRegistro);
            $stmt->bindParam(":activo", $activo);
            $stmt->bindParam(":id", $id);

            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al actualizar usuario: " . $e->getMessage(),
            );
        }
    }

    public function changeUserRole(int $id, UserRole $newRole): void
    {
        try {
            $query = "UPDATE USUARIO SET rol = :rol WHERE id_usuario = :id";
            $stmt = $this->db->prepare($query);
            $roleValue = $newRole->value;
            $stmt->bindParam(":rol", $roleValue);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al cambiar el rol del usuario: " . $e->getMessage(),
            );
        }
    }

    public function setUserStatus(int $id, bool $active): void
    {
        try {
            $query =
                "UPDATE USUARIO SET activo = :activo WHERE id_usuario = :id";
            $stmt = $this->db->prepare($query);
            $activoValue = $active ? 1 : 0;
            $stmt->bindParam(":activo", $activoValue);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al cambiar el estado del usuario: " . $e->getMessage(),
            );
        }
    }

    public function deleteUser(int $id): void
    {
        try {
            $query = "DELETE FROM USUARIO WHERE id_usuario = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al eliminar usuario: " . $e->getMessage(),
            );
        }
    }
}
