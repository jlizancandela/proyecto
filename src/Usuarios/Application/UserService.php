<?php

/**
 * Management of user data and profiles in the application.
 */

namespace Usuarios\Application;

use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;
use Usuarios\Infrastructure\UserRepository;
use Respect\Validation\Validator as v;
use Shared\Domain\Exceptions\InvalidEmailException;
use Shared\Domain\Exceptions\InvalidUserDataException;

/**
 * User management service
 *
 * Implements CRUD operations for users, search, and data validation.
 * Verifies that emails are unique before creating or updating users.
 */
class UserService
{
    /**
     * @var UserRepository The repository for user data operations.
     */
    private UserRepository $userRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository The repository for user data operations.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Retrieves all users with pagination.
     *
     * @param int $limit Maximum number of results
     * @param int $offset Offset for pagination
     * @param string $sort Field to sort by
     * @param string $order Sort direction (asc/desc)
     * @return Usuario[] Array of users
     */
    public function getAllUsers($limit = 10, $offset = 0, $sort = '', $order = 'asc'): array
    {
        return $this->userRepository->getAllUsers($limit, $offset, $sort, $order);
    }

    /**
     * Counts the total number of users in the system.
     *
     * @return int Total number of users
     */
    public function getTotalUsers(): int
    {
        return $this->userRepository->getTotalUsers();
    }

    /**
     * Finds a user by their ID.
     *
     * @param int $id User ID
     * @return Usuario|null Found user or null
     */
    public function getUserById(int $id): ?Usuario
    {
        return $this->userRepository->getUserById($id);
    }

    /**
     * Retrieves all users with a specific role.
     *
     * @param UserRole $role Role to filter (ADMIN, SPECIALIST, CLIENT)
     * @return Usuario[] Array of users with that role
     */
    public function getUserByRole(UserRole $role): array
    {
        return $this->userRepository->getUserByRole($role);
    }

    /**
     * Retrieves users by role with pagination.
     *
     * @param string $rol Role name (Admin, Specialist, Client)
     * @param int $limit Maximum number of results
     * @param int $offset Offset for pagination
     * @param string $sort Field to sort by
     * @param string $order Sort direction (asc/desc)
     * @return Usuario[] Array of users with that role
     */
    public function getUsersByRole(string $rol, int $limit = 10, int $offset = 0, $sort = '', $order = 'asc'): array
    {
        return $this->userRepository->getUsersByRole($rol, $limit, $offset, $sort, $order);
    }

    /**
     * Counts the total number of users with a specific role.
     *
     * @param string $rol Role name (Admin, Specialist, Client)
     * @return int Number of users with that role
     */
    public function getTotalUsersByRole(string $rol): int
    {
        return $this->userRepository->getTotalUsersByRole($rol);
    }

    /**
     * Searches users by name, surname, or email with pagination.
     *
     * @param string $search Search term
     * @param int $limit Maximum number of results
     * @param int $offset Offset for pagination
     * @param string $sort Field to sort by
     * @param string $order Sort direction (asc/desc)
     * @return Usuario[] Array of users matching the search
     */
    public function searchUsers(string $search, int $limit = 10, int $offset = 0, $sort = '', $order = 'asc'): array
    {
        return $this->userRepository->searchUsers($search, $limit, $offset, $sort, $order);
    }

    /**
     * Counts the total number of search results.
     *
     * @param string $search Search term
     * @return int Number of matching users
     */
    public function getTotalSearchResults(string $search): int
    {
        return $this->userRepository->getTotalSearchResults($search);
    }

    /**
     * Retrieves users applying multiple filters (admin view).
     *
     * @param array $filters Associative array of filters
     * @param int $limit Limit
     * @param int $offset Offset
     * @return Usuario[] Array of users
     */
    public function getAllUsersWithFilters(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        return $this->userRepository->findAllFiltered($filters, $limit, $offset);
    }

    /**
     * Counts the total number of users applying multiple filters.
     *
     * @param array $filters Associative array of filters
     * @return int Total users
     */
    public function countAllUsersWithFilters(array $filters = []): int
    {
        return $this->userRepository->countAllFiltered($filters);
    }

    /**
     * Creates a new user by validating data and checking for unique email.
     *
     * Validates user data and verifies that the email is not registered.
     * Assigns the generated ID to the Usuario object.
     *
     * @param Usuario $user User to create
     * @return void
     * @throws InvalidEmailException If the email already exists
     * @throws InvalidUserDataException If user data is invalid
     */
    public function setUser(Usuario $user): void
    {
        $this->validateUser($user);

        $existingUser = $this->userRepository->getUserByEmail($user->getEmail());
        if ($existingUser !== null) {
            throw new InvalidEmailException("El email ya está registrado en el sistema");
        }

        $id = $this->userRepository->addUser($user);
        $user->setId($id);
    }

    /**
     * Updates an existing user validating data and unique email.
     *
     * Validates data and verifies that the email is not used by another user.
     * Allows keeping the same email if it hasn't changed.
     *
     * @param Usuario $user User with updated data
     * @return void
     * @throws InvalidEmailException If the email is already in use by another user
     * @throws InvalidUserDataException If user data is invalid
     */
    public function updateUser(Usuario $user): void
    {
        $this->validateUser($user);

        $existingUser = $this->userRepository->getUserByEmail($user->getEmail());
        if ($existingUser !== null && $existingUser->getId() !== $user->getId()) {
            throw new InvalidEmailException("El email ya está registrado en el sistema");
        }

        $this->userRepository->updateUser($user);
    }

    /**
     * Validates user data.
     *
     * Verifies that email, name, surname, and phone (optional) meet
     * format and length requirements.
     *
     * @param Usuario $user User to validate
     * @return void
     * @throws InvalidUserDataException If any data does not meet rules
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
            throw new InvalidUserDataException('Datos de usuario inválidos: ' . $e->getMessage());
        }
    }

    /**
     * Deletes a user from the system.
     *
     * @param int $id ID of the user to delete
     * @return void
     */
    public function deleteUser(int $id): void
    {
        $this->userRepository->deleteUser($id);
    }

    /**
     * Deactivates a user (logical deletion).
     *
     * @param int $id ID of the user to deactivate
     * @return void
     */
    public function deactivateUser(int $id): void
    {
        $this->userRepository->setUserStatus($id, 0);
    }

    /**
     * Activates a user.
     *
     * @param int $id ID of the user to activate
     * @return void
     */
    public function activateUser(int $id): void
    {
        $this->userRepository->setUserStatus($id, 1);
    }
}
