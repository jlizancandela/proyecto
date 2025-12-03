<?php

use Usuarios\Application\UserService;
use Usuarios\Infrastructure\UserRepository;
use Usuarios\Domain\Usuario;
use PHPUnit\Framework\MockObject\MockObject;

describe("UserService", function () {
    it("can get all users", function () {
        /** @var MockObject&UserRepository */
        $repository = $this->createMock(UserRepository::class);
        $service = new UserService($repository);

        $mockUsers = [
            new Usuario(
                rol: "Admin",
                nombre: "Admin",
                apellidos: "User",
                email: "admin@example.com",
                password_hash: "hashed",
                id_usuario: 1,
            ),
            new Usuario(
                rol: "Cliente",
                nombre: "Cliente",
                apellidos: "Test",
                email: "cliente@example.com",
                password_hash: "hashed",
                id_usuario: 2,
            ),
        ];

        $repository
            ->expects($this->once())
            ->method("getAllUsers")
            ->willReturn($mockUsers);

        $users = $service->getAllUsers();

        expect($users)
            ->toBeArray()
            ->and($users)
            ->toHaveCount(2)
            ->and($users[0]->getEmail())
            ->toBe("admin@example.com");
    });

    it("can get user by id", function () {
        /** @var MockObject&UserRepository */
        $repository = $this->createMock(UserRepository::class);
        $service = new UserService($repository);

        $mockUser = new Usuario(
            rol: "Cliente",
            nombre: "Juan",
            apellidos: "Pérez",
            email: "juan@example.com",
            password_hash: "hashed",
            id_usuario: 5,
        );

        $repository
            ->expects($this->once())
            ->method("getUserById")
            ->with(5)
            ->willReturn($mockUser);

        $user = $service->getUserById(5);

        expect($user)
            ->toBeInstanceOf(Usuario::class)
            ->and($user->getId())
            ->toBe(5)
            ->and($user->getNombre())
            ->toBe("Juan");
    });

    it("can delete user by id", function () {
        /** @var MockObject&UserRepository */
        $repository = $this->createMock(UserRepository::class);
        $service = new UserService($repository);

        $repository->expects($this->once())->method("deleteUser")->with(5);

        $service->deleteUser(5);

        // If no exception is thrown, the test passes
        expect(true)->toBeTrue();
    });
});
