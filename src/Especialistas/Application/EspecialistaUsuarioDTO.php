<?php

namespace Especialistas\Application;

use Usuarios\Domain\UserRole;

class EspecialistaUsuarioDTO
{
    public int $id_usuario;
    public string $rol;
    public string $nombre;
    public string $apellidos;
    public string $email;
    public ?string $telefono;
    public string $fecha_registro;
    public bool $activo;

    public int $id_especialista;
    public ?string $descripcion;
    public ?string $foto_url;

    public static function fromDatabase(array $data): self
    {
        $dto = new self();

        $dto->id_usuario = (int) $data["id_usuario"];
        $dto->rol = $data["rol"];
        $dto->nombre = $data["nombre"];
        $dto->apellidos = $data["apellidos"];
        $dto->email = $data["email"];
        $dto->telefono = $data["telefono"] ?? null;
        $dto->fecha_registro = $data["fecha_registro"];
        $dto->activo = (bool) ($data["activo"] ?? true);
        $dto->id_especialista = (int) $data["id_especialista"];
        $dto->descripcion = $data["descripcion"] ?? null;
        $dto->foto_url = $data["foto_url"] ?? null;

        return $dto;
    }

    public function getRol(): UserRole
    {
        return UserRole::from($this->rol);
    }

    public function getNombreCompleto(): string
    {
        return $this->nombre . " " . $this->apellidos;
    }

    public function getFechaRegistro(): \DateTime
    {
        return new \DateTime($this->fecha_registro);
    }
}
