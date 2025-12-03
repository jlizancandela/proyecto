<?php

use Usuarios\Domain\Usuario;
use Usuarios\Domain\UserRole;

describe("Usuario Model", function () {
    it("can be created with valid data", function () {
        $usuario = new Usuario(
            rol: "Cliente",
            nombre: "Juan",
            apellidos: "Pérez García",
            email: "juan@example.com",
            password_hash: "hashed_password_123",
            telefono: "+34612345678",
            fecha_registro: "2024-01-15 10:30:00",
            activo: true,
            id_usuario: 1,
        );

        expect($usuario->getId())
            ->toBe(1)
            ->and($usuario->getNombre())
            ->toBe("Juan")
            ->and($usuario->getApellidos())
            ->toBe("Pérez García")
            ->and($usuario->getEmail())
            ->toBe("juan@example.com")
            ->and($usuario->getTelefono())
            ->toBe("+34612345678")
            ->and($usuario->getActivo())
            ->toBeTrue()
            ->and($usuario->getRol())
            ->toBeInstanceOf(UserRole::class)
            ->and($usuario->getRol()->value)
            ->toBe("Cliente");
    });

    it("can be created without optional fields", function () {
        $usuario = new Usuario(
            rol: "Admin",
            nombre: "María",
            apellidos: "López",
            email: "maria@example.com",
            password_hash: "hashed_password_456",
        );

        expect($usuario->getNombre())
            ->toBe("María")
            ->and($usuario->getTelefono())
            ->toBeNull()
            ->and($usuario->getActivo())
            ->toBeTrue(); // Default value
    });

    it("returns correct fecha_registro as DateTime", function () {
        $usuario = new Usuario(
            rol: "Especialista",
            nombre: "Carlos",
            apellidos: "Ruiz",
            email: "carlos@example.com",
            password_hash: "hashed_password_789",
            fecha_registro: "2024-03-20 14:25:30",
        );

        $fecha = $usuario->getFechaRegistro();

        expect($fecha)
            ->toBeInstanceOf(\DateTime::class)
            ->and($fecha->format("Y-m-d"))
            ->toBe("2024-03-20");
    });

    it("can be created from database data", function () {
        $data = [
            "id_usuario" => 5,
            "rol" => "Cliente",
            "nombre" => "Ana",
            "apellidos" => "Martínez",
            "email" => "ana@example.com",
            "password_hash" => "hashed_password",
            "telefono" => "+34600123456",
            "fecha_registro" => "2024-02-10 09:15:00",
            "activo" => 1,
        ];

        $usuario = Usuario::fromDatabase($data);

        expect($usuario->getId())
            ->toBe(5)
            ->and($usuario->getNombre())
            ->toBe("Ana")
            ->and($usuario->getEmail())
            ->toBe("ana@example.com")
            ->and($usuario->getActivo())
            ->toBeTrue();
    });

    it("handles activo as boolean correctly", function () {
        $data = [
            "rol" => "Cliente",
            "nombre" => "Pedro",
            "apellidos" => "Sánchez",
            "email" => "pedro@example.com",
            "password_hash" => "hashed",
            "fecha_registro" => "2024-01-01 00:00:00",
            "activo" => 0, // Database stores as int
        ];

        $usuario = Usuario::fromDatabase($data);

        expect($usuario->getActivo())->toBeFalse();
    });

    it("validates usuario data correctly", function () {
        $usuario = new Usuario(
            rol: "Cliente",
            nombre: "Test",
            apellidos: "User",
            email: "test@example.com",
            password_hash: "Password123!@#",
            telefono: "+34612345678",
        );

        $validator = $usuario->getValidation();

        expect($validator)->toBeInstanceOf(
            \Respect\Validation\Validator::class,
        );
    });
});
