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

    public function __construct(
        string $rol,
        string $nombre,
        string $apellidos,
        string $email,
        string $passwordHash,
        int $idUsuario,
        ?string $descripcion = null,
        ?string $fotoUrl = null,
        ?string $telefono = null,
        ?string $fechaRegistro = null,
        bool $activo = true,
        ?int $usuarioId = null,
        ?int $idEspecialista = null
    ) {
        parent::__construct(
            $rol,
            $nombre,
            $apellidos,
            $email,
            $passwordHash,
            $telefono,
            $fechaRegistro,
            $activo,
            $usuarioId
        );

        $this->idUsuario = $idUsuario;
        $this->descripcion = $descripcion;
        $this->fotoUrl = $fotoUrl;
        if ($idEspecialista !== null) {
            $this->idEspecialista = $idEspecialista;
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

    public static function fromDatabase(array $data): self
    {
        $especialista = new self(
            $data["rol"],
            $data["nombre"],
            $data["apellidos"],
            $data["email"],
            $data["password_hash"],
            $data["id_usuario"],
            $data["descripcion"] ?? null,
            $data["foto_url"] ?? null,
            $data["telefono"] ?? null,
            $data["fecha_registro"],
            (bool) ($data["activo"] ?? true),
            $data["id_usuario"] ?? null,
            $data["id_especialista"] ?? null
        );
        return $especialista;
    }
}
