<?php

namespace Usuarios\Application;

use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;
use Usuarios\Infrastructure\UserRepository;
use Shared\Domain\Exceptions\InvalidValidation;
use Respect\Validation\Exceptions\NestedValidationException;

class UserService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers($limit = 10, $offset = 0): array
    {
        return $this->userRepository->getAllUsers($limit, $offset);
    }

    public function getTotalUsers(): int
    {
        return $this->userRepository->getTotalUsers();
    }

    public function getUserById(int $id): ?Usuario
    {
        return $this->userRepository->getUserById($id);
    }

    public function getUserByRole(UserRole $role): array
    {
        return $this->userRepository->getUserByRole($role);
    }

    /**
     * Crea un nuevo usuario con validaciones
     * NOTA: La contraseña debe venir ya hasheada desde AuthService
     *
     * @param Usuario $user Usuario a crear
     * @throws ValidationException Si los datos no son válidos
     * @throws \RuntimeException Si el email ya existe
     */
    public function setUser(Usuario $user): void
    {
        try {
            $validation = $user->getValidation();
            $validation->assert($user);
        } catch (NestedValidationException $e) {
            $errorMessage = $e->getFullMessage();
            throw new InvalidValidation($errorMessage);
        }

        $existingUser = $this->userRepository->getUserByEmail(
            $user->getEmail(),
        );
        if ($existingUser !== null) {
            throw new \RuntimeException(
                "El email ya está registrado en el sistema",
            );
        }

        $id = $this->userRepository->addUser($user);
        $user->setId($id);
    }

    /**
     * Actualiza un usuario con validaciones
     * NOTA: Si se actualiza la contraseña, debe venir ya hasheada desde AuthService
     *
     * @param Usuario $user Usuario a actualizar
     * @throws ValidationException Si los datos no son válidos
     * @throws \RuntimeException Si el email ya existe
     */
    public function updateUser(Usuario $user): void
    {
        try {
            $validation = $user->getValidation();
            $validation->assert($user);
        } catch (NestedValidationException $e) {
            $errorMessage = $e->getFullMessage();
            throw new InvalidValidation($errorMessage);
        }

        $existingUser = $this->userRepository->getUserByEmail(
            $user->getEmail(),
        );
        if (
            $existingUser !== null &&
            $existingUser->getId() !== $user->getId()
        ) {
            throw new \RuntimeException(
                "El email ya está registrado en el sistema",
            );
        }

        $this->userRepository->updateUser($user);
    }

    public function deleteUser(int $id): void
    {
        $this->userRepository->deleteUser($id);
    }
}
