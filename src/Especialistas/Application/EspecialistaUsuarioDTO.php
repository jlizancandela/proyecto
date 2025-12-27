<?php

/**
 * Data transfer object combining specialist and user information
 */

namespace Especialistas\Application;

use Usuarios\Domain\UserRole;

class EspecialistaUsuarioDTO
{
    public int $idUsuario;
    public string $rol;
    public string $nombre;
    public string $apellidos;
    public string $email;
    public ?string $telefono;
    public string $fechaRegistro;
    public bool $activo;

    public int $idEspecialista;
    public ?string $descripcion;
    public ?string $fotoUrl;

    /**
     * Creates an EspecialistaUsuarioDTO instance from database an array of data.
     * @param array $data Associative array of data from the database.
     * @return self A new EspecialistaUsuarioDTO instance.
     */
    public static function fromDatabase(array $data): self
    {
        $dto = new self();

        $dto->idUsuario = (int) $data["id_usuario"];
        $dto->rol = $data["rol"];
        $dto->nombre = $data["nombre"];
        $dto->apellidos = $data["apellidos"];
        $dto->email = $data["email"];
        $dto->telefono = $data["telefono"] ?? null;
        $dto->fechaRegistro = $data["fecha_registro"];
        $dto->activo = (bool) ($data["activo"] ?? true);
        $dto->idEspecialista = (int) $data["id_especialista"];
        $dto->descripcion = $data["descripcion"] ?? null;
        $dto->fotoUrl = $data["foto_url"] ?? null;

        return $dto;
    }

    /**
     * Get the user role.
     * @return UserRole The user's role as a UserRole enum.
     */
    public function getRol(): UserRole
    {
        return UserRole::from($this->rol);
    }

    /**
     * Get the registration date.
     * @return \DateTime A DateTime object representing the registration date.
     */
    public function getFechaRegistro(): \DateTime
    {
        return new \DateTime($this->fechaRegistro);
    }
}
