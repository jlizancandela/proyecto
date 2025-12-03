<?php

namespace Especialistas\Domain;

use Usuarios\Domain\Usuario;
use Respect\Validation\Validator as v;

/**
 * Represents a Especialista in the system.
 */
class Especialista extends Usuario
{
    private int $id_especialista;
    private int $id_usuario;
    private ?string $descripcion;
    private ?string $foto_url;

    public function __construct(
        // Usuario properties
        string $rol,
        string $nombre,
        string $apellidos,
        string $email,
        string $password_hash,
        // Especialista properties
        int $id_usuario,
        ?string $descripcion = null,
        ?string $foto_url = null,
        // Optional Usuario properties
        ?string $telefono = null,
        ?string $fecha_registro = null,
        bool $activo = true,
        ?int $usuario_id = null,
        ?int $id_especialista = null,
    ) {
        parent::__construct(
            rol: $rol,
            nombre: $nombre,
            apellidos: $apellidos,
            email: $email,
            password_hash: $password_hash,
            telefono: $telefono,
            fecha_registro: $fecha_registro,
            activo: $activo,
            id_usuario: $usuario_id,
        );

        $this->id_usuario = $id_usuario;
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
        return $this->id_usuario;
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
     * Creates an Especialista instance from database row data
     *
     * @param array $data Associative array from database
     * @return self
     */
    public static function fromDatabase(array $data): self
    {
        $especialista = new self(
            rol: $data["rol"],
            nombre: $data["nombre"],
            apellidos: $data["apellidos"],
            email: $data["email"],
            password_hash: $data["password_hash"],
            id_usuario: $data["id_usuario"],
            descripcion: $data["descripcion"] ?? null,
            foto_url: $data["foto_url"] ?? null,
            telefono: $data["telefono"] ?? null,
            fecha_registro: $data["fecha_registro"],
            activo: (bool) ($data["activo"] ?? true),
            usuario_id: $data["id_usuario"] ?? null,
            id_especialista: $data["id_especialista"] ?? null,
        );
        return $especialista;
    }

    public function getValidation(): \Respect\Validation\Validator
    {
        return v::attribute(
            "id_especialista",
            v::intType()->positive()->noWhitespace(),
        )
            ->attribute("id_usuario", v::intType()->positive()->noWhitespace())
            ->attribute("descripcion", v::stringType()->noWhitespace())
            ->attribute("foto_url", v::stringType()->noWhitespace());
    }
}
