<?php

namespace Usuarios\Domain;

use Usuarios\Domain\UserRole;
use Respect\Validation\Validator as v;

class Usuario
{
    private int $id_usuario;
    private string $rol;
    private string $nombre;
    private string $apellidos;
    private string $email;
    private ?string $telefono;
    private string $password_hash;
    private string $fecha_registro;
    private bool $activo;

    public function __construct(
        string $rol,
        string $nombre,
        string $apellidos,
        string $email,
        string $password_hash,
        ?string $telefono = null,
        ?string $fecha_registro = null,
        bool $activo = true,
        ?int $id_usuario = null,
    ) {
        $this->rol = $rol;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->telefono = $telefono;
        $this->fecha_registro = $fecha_registro ?? date("Y-m-d H:i:s");
        $this->activo = $activo;
        if ($id_usuario !== null) {
            $this->id_usuario = $id_usuario;
        }
    }

    public function getId(): int
    {
        return $this->id_usuario;
    }

    public function setId(int $id): void
    {
        $this->id_usuario = $id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password_hash;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function getActivo(): bool
    {
        return $this->activo;
    }

    public function getRol(): UserRole
    {
        return UserRole::from($this->rol);
    }

    public function getFechaRegistro(): \DateTime
    {
        return new \DateTime($this->fecha_registro);
    }

    public function setPassword(string $password): void
    {
        $this->password_hash = $password;
    }

    public static function fromDatabase(array $data): self
    {
        $usuario = new self(
            rol: $data["rol"],
            nombre: $data["nombre"],
            apellidos: $data["apellidos"],
            email: $data["email"],
            password_hash: $data["password_hash"],
            telefono: $data["telefono"] ?? null,
            fecha_registro: $data["fecha_registro"],
            activo: (bool) ($data["activo"] ?? true),
            id_usuario: $data["id_usuario"] ?? null,
        );
        return $usuario;
    }

    public function getValidation(): \Respect\Validation\Validator
    {
        return v::attribute(
            "nombre",
            v::stringType()->notEmpty(),
            "El nombre es obligatorio",
        )
            ->attribute(
                "apellidos",
                v::stringType()->notEmpty(),
                "El apellido es obligatorio",
            )
            ->attribute(
                "email",
                v::stringType()->notEmpty()->email()->length(null, 100),
                "El email es obligatorio y debe ser válido",
            )
            ->attribute(
                "telefono",
                v::optional(v::stringType()->regex('/^\+?[0-9]{9,15}$/')),
                "El telefono no tiene un formato correcto",
            )
            ->attribute(
                "password_hash",
                v::stringType()->notEmpty(),
                "La contraseña es obligatoria",
            )
            ->attribute(
                "rol",
                v::stringType()
                    ->notEmpty()
                    ->in(["Admin", "Cliente", "Especialista"]),
                "El rol es obligatorio",
            )
            ->attribute(
                "activo",
                v::boolType()->notEmpty(),
                "El estado es obligatorio",
            );
    }
}
