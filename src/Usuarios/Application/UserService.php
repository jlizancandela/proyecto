<?php

namespace Usuarios\Application;

use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;
use Usuarios\Infrastructure\UserRepository;
use Respect\Validation\Validator as v;

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

    public function searchUsers(string $search, int $limit = 10, int $offset = 0): array
    {
        return $this->userRepository->searchUsers($search, $limit, $offset);
    }

    public function getTotalSearchResults(string $search): int
    {
        return $this->userRepository->getTotalSearchResults($search);
    }

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

    public function updateUser(Usuario $user): void
    {
        $this->validateUser($user);

        $existingUser = $this->userRepository->getUserByEmail($user->getEmail());
        if ($existingUser !== null && $existingUser->getId() !== $user->getId()) {
            throw new \RuntimeException("El email ya está registrado en el sistema");
        }

        $this->userRepository->updateUser($user);
    }

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

    public function deleteUser(int $id): void
    {
        $this->userRepository->deleteUser($id);
    }
}
