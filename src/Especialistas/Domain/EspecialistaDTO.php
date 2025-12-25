<?php
// Simple data container for creating specialist instances

namespace Especialistas\Domain;

/**
 * Data Transfer Object for Especialista creation
 */
class EspecialistaDTO
{
    public string $rol;
    public string $nombre;
    public string $apellidos;
    public string $email;
    public string $passwordHash;
    public int $idUsuario;
    public ?string $descripcion;
    public ?string $fotoUrl;
    public ?string $telefono;
    public ?string $fechaRegistro;
    public bool $activo;
    public ?int $usuarioId;
    public ?int $idEspecialista;

    /**
     * Creates a new EspecialistaDTO instance
     *
     * @param array $data Associative array with specialist data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->rol = $data['rol'];
        $dto->nombre = $data['nombre'];
        $dto->apellidos = $data['apellidos'];
        $dto->email = $data['email'];
        $dto->passwordHash = $data['passwordHash'];
        $dto->idUsuario = $data['idUsuario'];
        $dto->descripcion = $data['descripcion'] ?? null;
        $dto->fotoUrl = $data['fotoUrl'] ?? null;
        $dto->telefono = $data['telefono'] ?? null;
        $dto->fechaRegistro = $data['fechaRegistro'] ?? null;
        $dto->activo = $data['activo'] ?? true;
        $dto->usuarioId = $data['usuarioId'] ?? null;
        $dto->idEspecialista = $data['idEspecialista'] ?? null;

        return $dto;
    }
}
