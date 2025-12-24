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
     * Obtiene la conexión PDO
     * 
     * @return PDO Conexión a la base de datos
     */
    public function getConnection(): PDO
    {
        return $this->db;
    }

    public function getAllUsers($limit = 10, $offset = 0, $sort = '', $order = 'asc'): array
    {
        try {
            // Dynamic ORDER BY
            $orderBy = "id_usuario DESC";
            if (!empty($sort)) {
                $orderDirection = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
                switch ($sort) {
                    case 'nombre':
                        $orderBy = "nombre $orderDirection, apellidos $orderDirection";
                        break;
                    case 'email':
                        $orderBy = "email $orderDirection";
                        break;
                    case 'rol':
                        $orderBy = "rol $orderDirection";
                        break;
                    case 'fecha':
                        $orderBy = "fecha_registro $orderDirection";
                        break;
                }
            }

            $query = "SELECT * FROM USUARIO ORDER BY $orderBy LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
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

    public function searchUsers(string $search, int $limit = 10, int $offset = 0, $sort = '', $order = 'asc'): array
    {
        try {
            // Dynamic ORDER BY
            $orderBy = "id_usuario DESC";
            if (!empty($sort)) {
                $orderDirection = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
                switch ($sort) {
                    case 'nombre':
                        $orderBy = "nombre $orderDirection, apellidos $orderDirection";
                        break;
                    case 'email':
                        $orderBy = "email $orderDirection";
                        break;
                    case 'rol':
                        $orderBy = "rol $orderDirection";
                        break;
                    case 'fecha':
                        $orderBy = "fecha_registro $orderDirection";
                        break;
                }
            }

            $query = "SELECT * FROM USUARIO 
                      WHERE nombre LIKE :search1 
                      OR apellidos LIKE :search2 
                      OR email LIKE :search3 
                      OR telefono LIKE :search4 
                      ORDER BY $orderBy
                      LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($query);
            $searchParam = "%{$search}%";
            $stmt->bindValue(":search1", $searchParam);
            $stmt->bindValue(":search2", $searchParam);
            $stmt->bindValue(":search3", $searchParam);
            $stmt->bindValue(":search4", $searchParam);
            $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
            $stmt->execute();

            $users = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = Usuario::fromDatabase($row);
            }
            return $users;
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al buscar usuarios: " . $e->getMessage(),
            );
        }
    }

    public function getTotalSearchResults(string $search): int
    {
        try {
            $query = "SELECT COUNT(*) as total FROM USUARIO 
                      WHERE nombre LIKE :search1 
                      OR apellidos LIKE :search2 
                      OR email LIKE :search3 
                      OR telefono LIKE :search4";
            $stmt = $this->db->prepare($query);
            $searchParam = "%{$search}%";
            $stmt->bindValue(":search1", $searchParam);
            $stmt->bindValue(":search2", $searchParam);
            $stmt->bindValue(":search3", $searchParam);
            $stmt->bindValue(":search4", $searchParam);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result["total"];
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al contar resultados de búsqueda: " . $e->getMessage(),
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

    /**
     * Obtiene usuarios por rol con paginación
     */
    public function getUsersByRole(string $rol, int $limit = 10, int $offset = 0, $sort = '', $order = 'asc'): array
    {
        try {
            // Dynamic ORDER BY
            $orderBy = "id_usuario DESC";
            if (!empty($sort)) {
                $orderDirection = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
                switch ($sort) {
                    case 'nombre':
                        $orderBy = "nombre $orderDirection, apellidos $orderDirection";
                        break;
                    case 'email':
                        $orderBy = "email $orderDirection";
                        break;
                    case 'rol':
                        $orderBy = "rol $orderDirection";
                        break;
                    case 'fecha':
                        $orderBy = "fecha_registro $orderDirection";
                        break;
                }
            }

            $query = "SELECT * FROM USUARIO WHERE rol = :rol ORDER BY $orderBy LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":rol", $rol);
            $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
            $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
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

    /**
     * Cuenta usuarios por rol
     */
    public function getTotalUsersByRole(string $rol): int
    {
        try {
            $query = "SELECT COUNT(*) as total FROM USUARIO WHERE rol = :rol";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(":rol", $rol);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result["total"];
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al contar usuarios por rol: " . $e->getMessage(),
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

    /**
     * Guarda el token de recuperación de contraseña y su expiración
     * 
     * @param int $userId ID del usuario
     * @param string $token Token de recuperación
     * @param string $expiration Fecha y hora de expiración (formato: Y-m-d H:i:s)
     * @return void
     * @throws PDOException Si hay un error en la base de datos
     */
    public function savePasswordResetToken(int $userId, string $token, string $expiration): void
    {
        try {
            $query = "UPDATE USUARIO SET reset_token = :token, reset_expiration = :expiration WHERE id_usuario = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":token", $token);
            $stmt->bindParam(":expiration", $expiration);
            $stmt->bindParam(":id", $userId);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al guardar token de recuperación: " . $e->getMessage(),
            );
        }
    }

    /**
     * Obtiene un usuario por su token de recuperación
     * 
     * @param string $token Token de recuperación
     * @return Usuario|null Usuario si existe, null si no
     * @throws PDOException Si hay un error en la base de datos
     */
    public function getUserByResetToken(string $token): ?Usuario
    {
        try {
            $query = "SELECT * FROM USUARIO WHERE reset_token = :token";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":token", $token);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? Usuario::fromDatabase($row) : null;
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al obtener usuario por token: " . $e->getMessage(),
            );
        }
    }

    /**
     * Limpia el token de recuperación de un usuario
     * 
     * @param int $userId ID del usuario
     * @return void
     * @throws PDOException Si hay un error en la base de datos
     */
    public function clearResetToken(int $userId): void
    {
        try {
            $query = "UPDATE USUARIO SET reset_token = NULL, reset_expiration = NULL WHERE id_usuario = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":id", $userId);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al limpiar token de recuperación: " . $e->getMessage(),
            );
        }
    }
}
