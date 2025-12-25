<?php
// Domain entity for specialists, extends base user with specialist-specific fields

namespace Especialistas\Domain;

use Usuarios\Domain\Usuario;

class Especialista extends Usuario
{
    private int $idEspecialista;
    private int $idUsuario;
    private ?string $descripcion;
    private ?string $fotoUrl;

    /**
     * Creates a new Especialista instance
     *
     * @param EspecialistaDTO $dto Data transfer object with all required fields
     */
    public function __construct(EspecialistaDTO $dto)
    {
        parent::__construct(
            $dto->rol,
            $dto->nombre,
            $dto->apellidos,
            $dto->email,
            $dto->passwordHash,
            $dto->telefono,
            $dto->fechaRegistro,
            $dto->activo,
            $dto->usuarioId
        );

        $this->idUsuario = $dto->idUsuario;
        $this->descripcion = $dto->descripcion;
        $this->fotoUrl = $dto->fotoUrl;
        if ($dto->idEspecialista !== null) {
            $this->idEspecialista = $dto->idEspecialista;
        }
    }

    public function getIdEspecialista(): int
    {
        return $this->idEspecialista;
    }

    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function getFotoUrl(): ?string
    {
        return $this->fotoUrl;
    }

    /**
     * Creates an Especialista instance from database row data
     *
     * @param array $data Database row data
     * @return self
     */
    public static function fromDatabase(array $data): self
    {
        $dto = EspecialistaDTO::fromArray([
            'rol' => $data["rol"],
            'nombre' => $data["nombre"],
            'apellidos' => $data["apellidos"],
            'email' => $data["email"],
            'passwordHash' => $data["password_hash"],
            'idUsuario' => $data["id_usuario"],
            'descripcion' => $data["descripcion"] ?? null,
            'fotoUrl' => $data["foto_url"] ?? null,
            'telefono' => $data["telefono"] ?? null,
            'fechaRegistro' => $data["fecha_registro"],
            'activo' => (bool) ($data["activo"] ?? true),
            'usuarioId' => $data["id_usuario"] ?? null,
            'idEspecialista' => $data["id_especialista"] ?? null
        ]);

        return new self($dto);
    }
}
