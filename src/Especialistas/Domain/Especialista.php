<?php

namespace Especialistas\Domain;

use Usuarios\Domain\Usuario;

class Especialista extends Usuario
{
    private int $id_especialista;
    private int $idUsuario;
    private ?string $descripcion;
    private ?string $foto_url;

    public function __construct(
        string $rol,
        string $nombre,
        string $apellidos,
        string $email,
        string $password_hash,
        int $idUsuario,
        ?string $descripcion = null,
        ?string $foto_url = null,
        ?string $telefono = null,
        ?string $fecha_registro = null,
        bool $activo = true,
        ?int $usuario_id = null,
        ?int $id_especialista = null
    ) {
        parent::__construct(
            $rol,
            $nombre,
            $apellidos,
            $email,
            $password_hash,
            $telefono,
            $fecha_registro,
            $activo,
            $usuario_id
        );

        $this->idUsuario = $idUsuario;
        $this->descripcion = $descripcion;
        $this->foto_url = $foto_url;
        if ($id_especialista !== null) {
            $this->id_especialista = $id_especialista;
        }
    }

    public function getIdEspecialista(): int
    {
        return $this->id_especialista;
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
        return $this->foto_url;
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
