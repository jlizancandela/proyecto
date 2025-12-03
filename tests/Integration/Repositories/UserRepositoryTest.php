<?php

use Usuarios\Infrastructure\UserRepository;
use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;
use Shared\Infrastructure\Database\Database;

describe("UserRepository", function () {
    $pdo = null;

    beforeEach(function () use (&$pdo) {
        // Use real database connection
        $pdo = Database::getInstance()->getConnection();
        // Start transaction for test isolation
        $pdo->beginTransaction();
    });

    afterEach(function () use (&$pdo) {
        // Rollback transaction to keep database clean
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
    });

    it("can get all users", function () use (&$pdo) {
        /** @var \PDO $pdo */
        $repository = new UserRepository($pdo);
        $users = $repository->getAllUsers();

        expect($users)
            ->toBeArray()
            ->and(count($users))
            ->toBeGreaterThan(0) // Should have users from init.sql
            ->and($users[0])
            ->toBeInstanceOf(Usuario::class);

        // Verify first user is the admin from init.sql
        $adminUser = array_filter(
            $users,
            fn($u) => $u->getEmail() === "admin@sistema.com",
        );
        expect($adminUser)->toHaveCount(1);
    });

    it("can get user by id", function () use (&$pdo) {
        /** @var \PDO $pdo */
        $repository = new UserRepository($pdo);
        // Get the admin user (id=1 from init.sql)
        $user = $repository->getUserById(1);

        expect($user)
            ->toBeInstanceOf(Usuario::class)
            ->and($user->getId())
            ->toBe(1)
            ->and($user->getEmail())
            ->toBe("admin@sistema.com")
            ->and($user->getNombre())
            ->toBe("Carlos")
            ->and($user->getRol())
            ->toBeInstanceOf(UserRole::class)
            ->and($user->getRol()->value)
            ->toBe("Admin");
    });

    it("returns null when user not found by id", function () use (&$pdo) {
        /** @var \PDO $pdo */
        $repository = new UserRepository($pdo);
        $user = $repository->getUserById(99999);

        expect($user)->toBeNull();
    });

    it("can get user by email", function () use (&$pdo) {
        /** @var \PDO $pdo */
        $repository = new UserRepository($pdo);
        $user = $repository->getUserByEmail("maria.lopez@email.com");

        expect($user)
            ->toBeInstanceOf(Usuario::class)
            ->and($user->getEmail())
            ->toBe("maria.lopez@email.com")
            ->and($user->getNombre())
            ->toBe("María")
            ->and($user->getRol()->value)
            ->toBe("Cliente");
    });

    it("returns null when user not found by email", function () use (&$pdo) {
        /** @var \PDO $pdo */
        $repository = new UserRepository($pdo);
        $user = $repository->getUserByEmail("nonexistent@email.com");

        expect($user)->toBeNull();
    });

    it("can get users by role especialista", function () use (&$pdo) {
        /** @var \PDO $pdo */
        $repository = new UserRepository($pdo);
        $users = $repository->getUserByRole(UserRole::Especialista);

        expect($users)->toBeArray();
        // Ana, Pedro, Laura from init.sql
        expect($users)->toHaveCount(3);

        // Verify all are specialists
        foreach ($users as $user) {
            expect($user->getRol()->value)->toBe("Especialista");
        }
    });

    it("can get users by role cliente", function () use (&$pdo) {
        /** @var \PDO $pdo */
        $repository = new UserRepository($pdo);
        $users = $repository->getUserByRole(UserRole::Cliente);

        expect($users)->toBeArray();
        // María, Juan, David, Elena from init.sql
        expect(count($users))->toBeGreaterThanOrEqual(4);

        // Verify all are clients
        foreach ($users as $user) {
            expect($user->getRol()->value)->toBe("Cliente");
        }
    });

    it("can create new user", function () use (&$pdo) {
        /** @var \PDO $pdo */
        $repository = new UserRepository($pdo);
        $newUser = new Usuario(
            UserRole::Cliente->value,
            "Test",
            "User",
            "test.new@example.com",
            "password123",
            null,
            null,
            true,
            null,
        );

        $userId = $repository->addUser($newUser);

        expect($userId)->toBeInt()->toBeGreaterThan(0);

        // Verify user was created
        $createdUser = $repository->getUserById($userId);
        expect($createdUser)
            ->toBeInstanceOf(Usuario::class)
            ->and($createdUser->getEmail())
            ->toBe("test.new@example.com")
            ->and($createdUser->getNombre())
            ->toBe("Test");
    });

    it("can update existing user", function () use (&$pdo) {
        /** @var \PDO $pdo */
        $repository = new UserRepository($pdo);
        // Get existing user
        $user = $repository->getUserById(2); // María López

        // Update user data
        $updatedUser = new Usuario(
            $user->getRol()->value,
            "María Updated",
            $user->getApellidos(),
            $user->getEmail(),
            $user->getPassword(),
            "+34999888777",
            $user->getFechaRegistro()->format("Y-m-d H:i:s"),
            $user->getActivo(),
            $user->getId(),
        );

        $repository->updateUser($updatedUser);

        // Verify update
        $fetchedUser = $repository->getUserById($user->getId());
        expect($fetchedUser->getNombre())
            ->toBe("María Updated")
            ->and($fetchedUser->getTelefono())
            ->toBe("+34999888777");
    });
});
