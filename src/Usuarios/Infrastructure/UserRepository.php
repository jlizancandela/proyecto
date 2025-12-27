<?php

namespace Usuarios\Infrastructure;

use PDO;
use PDOException;
use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;

class UserRepository
{
    private $db;

    /**
     * UserRepository constructor.
     * @param PDO $db The PDO database connection.
     */
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

    /**
     * Builds the ORDER BY clause for queries.
     * 
     * @param string $sort The column to sort by ('nombre', 'email', 'rol', 'fecha').
     * @param string $order The sorting order ('asc' or 'desc').
     * @return string The ORDER BY clause.
     */
    private function buildOrderBy(string $sort = '', string $order = 'asc'): string
    {
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

        return $orderBy;
    }

    /**
     * Retrieves all users from the database with pagination and sorting.
     *
     * @param int $limit The maximum number of users to retrieve.
     * @param int $offset The number of users to skip for pagination.
     * @param string $sort The column to sort by (e.g., 'nombre', 'email', 'rol', 'fecha').
     * @param string $order The sorting order ('asc' or 'desc').
     * @return array An array of Usuario objects.
     * @throws PDOException If there is a database error.
     */
    public function getAllUsers($limit = 10, $offset = 0, $sort = '', $order = 'asc'): array
    {
        try {
            $orderBy = $this->buildOrderBy($sort, $order);

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

    /**
     * Counts the total number of users in the database.
     *
     * @return int The total number of users.
     * @throws PDOException If there is a database error.
     */
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

    /**
     * Searches for users based on a search term with pagination and sorting.
     *
     * @param string $search The search term.
     * @param int $limit The maximum number of users to retrieve.
     * @param int $offset The number of users to skip for pagination.
     * @param string $sort The column to sort by.
     * @param string $order The sorting order ('asc' or 'desc').
     * @return array An array of Usuario objects matching the search criteria.
     * @throws PDOException If there is a database error.
     */
    public function searchUsers(string $search, int $limit = 10, int $offset = 0, $sort = '', $order = 'asc'): array
    {
        try {
            $orderBy = $this->buildOrderBy($sort, $order);

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

    /**
     * Counts the total number of users matching a search term.
     *
     * @param string $search The search term.
     * @return int The total number of search results.
     * @throws PDOException If there is a database error.
     */
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

    /**
     * Retrieves a user by their ID.
     *
     * @param int $id The ID of the user to retrieve.
     * @return Usuario|null The Usuario object if found, null otherwise.
     * @throws PDOException If there is a database error.
     */
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

    /**
     * Retrieves users by their role.
     *
     * @param UserRole $role The role to filter by.
     * @return array An array of Usuario objects matching the specified role.
     * @throws PDOException If there is a database error.
     */
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
            $orderBy = $this->buildOrderBy($sort, $order);

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

    /**
     * Adds a new user to the database.
     *
     * @param Usuario $user The Usuario object to add.
     * @return int The ID of the newly created user.
     * @throws PDOException If there is a database error.
     */
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

    /**
     * Updates an existing user in the database.
     *
     * @param Usuario $user The Usuario object with updated data.
     * @return void
     * @throws PDOException If there is a database error.
     */
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


    /**
     * Sets the active status of a user.
     *
     * @param int $id The ID of the user.
     * @param bool $active The new active status (true for active, false for inactive).
     * @return void
     * @throws PDOException If there is a database error.
     */
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

    /**
     * Deletes a user from the database.
     *
     * @param int $id The ID of the user to delete.
     * @return void
     * @throws PDOException If there is a database error.
     */
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

    /**
     * Obtiene usuarios aplicando múltiples filtros
     * 
     * @param array $filters Filtros (rol, search, sort, order)
     * @param int $limit Límite de resultados
     * @param int $offset Desplazamiento
     * @return array Array de usuarios
     */
    public function findAllFiltered(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        try {
            $sql = "SELECT * FROM USUARIO WHERE 1=1";
            $params = [];

            // Filter by search term
            if (!empty($filters['search'])) {
                $sql .= " AND (nombre LIKE :search1 OR apellidos LIKE :search2 OR email LIKE :search3 OR telefono LIKE :search4)";
                $searchParam = "%{$filters['search']}%";
                $params['search1'] = $searchParam;
                $params['search2'] = $searchParam;
                $params['search3'] = $searchParam;
                $params['search4'] = $searchParam;
            }

            // Filter by role
            if (!empty($filters['rol'])) {
                $sql .= " AND rol = :rol";
                $params['rol'] = $filters['rol'];
            }

            // Filter by active status
            if (isset($filters['estado']) && $filters['estado'] !== '') {
                $sql .= " AND activo = :estado";
                $params['estado'] = (int)$filters['estado'];
            }

            // Order By
            $sort = $filters['sort'] ?? '';
            $order = $filters['order'] ?? 'asc';
            $orderBy = $this->buildOrderBy($sort, $order);
            $sql .= " ORDER BY $orderBy";

            // Pagination
            $sql .= " LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            $users = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = Usuario::fromDatabase($row);
            }

            return $users;
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al filtrar usuarios: " . $e->getMessage(),
            );
        }
    }

    /**
     * Cuenta usuarios aplicando múltiples filtros
     * 
     * @param array $filters Filtros (rol, search, estado)
     * @return int Total de usuarios
     */
    public function countAllFiltered(array $filters = []): int
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM USUARIO WHERE 1=1";
            $params = [];

            // Filter by search term
            if (!empty($filters['search'])) {
                $sql .= " AND (nombre LIKE :search1 OR apellidos LIKE :search2 OR email LIKE :search3 OR telefono LIKE :search4)";
                $searchParam = "%{$filters['search']}%";
                $params['search1'] = $searchParam;
                $params['search2'] = $searchParam;
                $params['search3'] = $searchParam;
                $params['search4'] = $searchParam;
            }

            // Filter by role
            if (!empty($filters['rol'])) {
                $sql .= " AND rol = :rol";
                $params['rol'] = $filters['rol'];
            }

            // Filter by active status
            if (isset($filters['estado']) && $filters['estado'] !== '') {
                $sql .= " AND activo = :estado";
                $params['estado'] = (int)$filters['estado'];
            }

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return (int) $result['total'];
        } catch (PDOException $e) {
            throw new PDOException(
                "Error al contar usuarios filtrados: " . $e->getMessage(),
            );
        }
    }
}
