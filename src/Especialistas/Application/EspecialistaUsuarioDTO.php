<?php

namespace Especialistas\Application;

use Usuarios\Domain\UserRole;

/**
 * DTO que combina los datos de Especialista y Usuario
 * Útil para transferir información completa del especialista con sus datos de usuario
 */
class EspecialistaUsuarioDTO
{
    // Propiedades de Usuario
    private int $id_usuario;
    private string $rol;
    private string $nombre;
    private string $apellidos;
    private string $email;
    private ?string $telefono;
    private string $fecha_registro;
    private bool $activo;

    // Propiedades de Especialista
    private int $id_especialista;
    private ?string $descripcion;
    private ?string $foto_url;

    public function __construct(
        int $id_usuario,
        string $rol,
        string $nombre,
        string $apellidos,
        string $email,
        ?string $telefono,
        string $fecha_registro,
        bool $activo,
        int $id_especialista,
        ?string $descripcion,
        ?string $foto_url,
    ) {
        $this->id_usuario = $id_usuario;
        $this->rol = $rol;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->fecha_registro = $fecha_registro;
        $this->activo = $activo;
        $this->id_especialista = $id_especialista;
        $this->descripcion = $descripcion;
        $this->foto_url = $foto_url;
    }

    // Getters para Usuario
    public function getIdUsuario(): int
    {
        return $this->id_usuario;
    }

    public function getRol(): UserRole
    {
        return UserRole::from($this->rol);
    }

    public function getRolString(): string
    {
        return $this->rol;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    public function getNombreCompleto(): string
    {
        return $this->nombre . " " . $this->apellidos;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function getFechaRegistro(): \DateTime
    {
        return new \DateTime($this->fecha_registro);
    }

    public function getFechaRegistroString(): string
    {
        return $this->fecha_registro;
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    // Getters para Especialista
    public function getIdEspecialista(): int
    {
        return $this->id_especialista;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function getFotoUrl(): ?string
    {
        return $this->foto_url;
    }

    /**
     * Creates a DTO from database row data (typically from a JOIN query)
     *
     * @param array $data Associative array from database
     * @return self
     */
    public static function fromDatabase(array $data): self
    {
        return new self(
            id_usuario: (int) $data["id_usuario"],
            rol: $data["rol"],
            nombre: $data["nombre"],
            apellidos: $data["apellidos"],
            email: $data["email"],
            telefono: $data["telefono"] ?? null,
            fecha_registro: $data["fecha_registro"],
            activo: (bool) ($data["activo"] ?? true),
            id_especialista: (int) $data["id_especialista"],
            descripcion: $data["descripcion"] ?? null,
            foto_url: $data["foto_url"] ?? null,
        );
    }
}
