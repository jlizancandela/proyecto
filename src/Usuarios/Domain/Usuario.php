<?php

namespace Usuarios\Domain;

use Usuarios\Domain\UserRole;

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

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function setApellidos(string $apellidos): void
    {
        $this->apellidos = $apellidos;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setTelefono(?string $telefono): void
    {
        $this->telefono = $telefono;
    }

    public static function fromDatabase(array $data): self
    {
        $usuario = new self(
            $data["rol"],
            $data["nombre"],
            $data["apellidos"],
            $data["email"],
            $data["password_hash"],
            $data["telefono"] ?? null,
            $data["fecha_registro"],
            (bool) ($data["activo"] ?? true),
            $data["id_usuario"] ?? null
        );
        return $usuario;
    }
}
