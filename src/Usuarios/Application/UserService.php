<?php

namespace Usuarios\Application;

use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;
use Usuarios\Infrastructure\UserRepository;
use Respect\Validation\Validator as v;

/**
 * Servicio de gestión de usuarios
 * 
 * Implementa operaciones CRUD para usuarios, búsqueda y validación de datos.
 * Verifica que los emails sean únicos antes de crear o actualizar usuarios.
 */
class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Obtiene todos los usuarios con paginación
     * 
     * @param int $limit Número máximo de resultados
     * @param int $offset Desplazamiento para paginación
     * @return array Array de usuarios
     */
    public function getAllUsers($limit = 10, $offset = 0): array
    {
        return $this->userRepository->getAllUsers($limit, $offset);
    }

    /**
     * Cuenta el total de usuarios en el sistema
     * 
     * @return int Número total de usuarios
     */
    public function getTotalUsers(): int
    {
        return $this->userRepository->getTotalUsers();
    }

    /**
     * Busca un usuario por su ID
     * 
     * @param int $id ID del usuario
     * @return Usuario|null Usuario encontrado o null
     */
    public function getUserById(int $id): ?Usuario
    {
        return $this->userRepository->getUserById($id);
    }

    /**
     * Obtiene todos los usuarios con un rol específico
     * 
     * @param UserRole $role Rol a filtrar (ADMIN, ESPECIALISTA, CLIENTE)
     * @return array Array de usuarios con ese rol
     */
    public function getUserByRole(UserRole $role): array
    {
        return $this->userRepository->getUserByRole($role);
    }

    /**
     * Busca usuarios por nombre, apellidos o email con paginación
     * 
     * @param string $search Término de búsqueda
     * @param int $limit Número máximo de resultados
     * @param int $offset Desplazamiento para paginación
     * @return array Array de usuarios que coinciden con la búsqueda
     */
    public function searchUsers(string $search, int $limit = 10, int $offset = 0): array
    {
        return $this->userRepository->searchUsers($search, $limit, $offset);
    }

    /**
     * Cuenta el total de resultados de una búsqueda
     * 
     * @param string $search Término de búsqueda
     * @return int Número de usuarios que coinciden
     */
    public function getTotalSearchResults(string $search): int
    {
        return $this->userRepository->getTotalSearchResults($search);
    }

    /**
     * Crea un nuevo usuario validando datos y verificando email único
     * 
     * Valida los datos del usuario y verifica que el email no esté registrado.
     * Asigna el ID generado al objeto Usuario.
     * 
     * @param Usuario $user Usuario a crear
     * @return void
     * @throws \RuntimeException Si los datos son inválidos o el email ya existe
     */
    public function setUser(Usuario $user): void
    {
        $this->validateUser($user);

        $existingUser = $this->userRepository->getUserByEmail($user->getEmail());
        if ($existingUser !== null) {
            throw new \RuntimeException("El email ya está registrado en el sistema");
        }

        $id = $this->userRepository->addUser($user);
        $user->setId($id);
    }

    /**
     * Actualiza un usuario existente validando datos y email único
     * 
     * Valida los datos y verifica que el email no esté usado por otro usuario.
     * Permite mantener el mismo email si no ha cambiado.
     * 
     * @param Usuario $user Usuario con datos actualizados
     * @return void
     * @throws \RuntimeException Si los datos son inválidos o el email está en uso
     */
    public function updateUser(Usuario $user): void
    {
        $this->validateUser($user);

        $existingUser = $this->userRepository->getUserByEmail($user->getEmail());
        if ($existingUser !== null && $existingUser->getId() !== $user->getId()) {
            throw new \RuntimeException("El email ya está registrado en el sistema");
        }

        $this->userRepository->updateUser($user);
    }

    /**
     * Valida los datos de un usuario
     * 
     * Verifica que email, nombre, apellidos y teléfono (opcional) cumplan
     * con los requisitos de formato y longitud.
     * 
     * @param Usuario $user Usuario a validar
     * @return void
     * @throws \RuntimeException Si algún dato no cumple las reglas
     */
    private function validateUser(Usuario $user): void
    {
        $emailValidator = v::email();
        $nombreValidator = v::stringType()->notEmpty()->length(2, 50);
        $apellidosValidator = v::stringType()->notEmpty()->length(2, 100);
        $telefonoValidator = v::optional(v::phone());

        try {
            $emailValidator->assert($user->getEmail());
            $nombreValidator->assert($user->getNombre());
            $apellidosValidator->assert($user->getApellidos());

            if ($user->getTelefono() !== null) {
                $telefonoValidator->assert($user->getTelefono());
            }
        } catch (\Respect\Validation\Exceptions\ValidationException $e) {
            throw new \RuntimeException('Datos de usuario inválidos: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un usuario del sistema
     * 
     * @param int $id ID del usuario a eliminar
     * @return void
     */
    public function deleteUser(int $id): void
    {
        $this->userRepository->deleteUser($id);
    }
}
