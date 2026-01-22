<?php

/**
 * Domain object that represents a user in the hair salon system.
 */

namespace Usuarios\Domain;

use Usuarios\Domain\UserRole;

/**
 * The main user class with all basic profile information.
 */
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
    private int $activo;

    /**
     * Usuario constructor.
     *
     * @param string $rol The user's role (e.g., 'Admin', 'Cliente').
     * @param string $nombre The user's first name.
     * @param string $apellidos The user's last name.
     * @param string $email The user's email address.
     * @param string $password_hash The hashed password.
     * @param string|null $telefono The user's phone number (optional).
     * @param array $options Optional settings (fecha_registro, activo, id_usuario).
     */
    public function __construct(
        string $rol,
        string $nombre,
        string $apellidos,
        string $email,
        string $password_hash,
        ?string $telefono = null,
        array $options = []
    ) {
        $this->rol = $rol;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->email = $email;
        $this->password_hash = $password_hash;
        $this->telefono = $telefono;
        $this->fecha_registro = $options['fecha_registro'] ?? date("Y-m-d H:i:s");
        $this->activo = (int)($options['activo'] ?? 1);
        if (isset($options['id_usuario'])) {
            $this->id_usuario = $options['id_usuario'];
        }
    }

    /**
     * Gets the user's ID.
     * @return int The unique ID of the user.
     */
    public function getId(): int
    {
        return $this->id_usuario;
    }

    /**
     * Sets the user's ID.
     * @param int $id The unique ID of the user.
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id_usuario = $id;
    }

    /**
     * Gets the user's first name.
     * @return string The first name of the user.
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * Gets the user's last name.
     * @return string The last names of the user.
     */
    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    /**
     * Gets the user's email address.
     * @return string The email address.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Gets the user's password hash.
     * @return string The hashed password.
     */
    public function getPassword(): string
    {
        return $this->password_hash;
    }

    /**
     * Gets the user's phone number.
     * @return string|null The phone number, or null if not set.
     */
    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    /**
     * Checks if the user account is active.
     * @return int 1 if active, 0 if inactive, 2 if banned.
     */
    public function getActivo(): int
    {
        return $this->activo;
    }

    /**
     * Gets the user's role.
     * @return UserRole The user's role enum.
     */
    public function getRol(): UserRole
    {
        return UserRole::from($this->rol);
    }

    /**
     * Gets the user's registration date.
     * @return \DateTime The registration timestamp.
     */
    public function getFechaRegistro(): \DateTime
    {
        return new \DateTime($this->fecha_registro);
    }

    /**
     * Sets the user's password hash.
     * @param string $password The new hashed password.
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = $password;
    }

    /**
     * Sets the user's first name.
     * @param string $nombre The new first name.
     * @return void
     */
    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    /**
     * Sets the user's last name.
     * @param string $apellidos The new last names.
     * @return void
     */
    public function setApellidos(string $apellidos): void
    {
        $this->apellidos = $apellidos;
    }

    /**
     * Sets the user's email address.
     * @param string $email The new email address.
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Sets the user's phone number.
     * @param string|null $telefono The new phone number.
     * @return void
     */
    public function setTelefono(?string $telefono): void
    {
        $this->telefono = $telefono;
    }

    /**
     * Hydrates a Usuario object from a database associative array.
     *
     * @param array $data The raw record from the database.
     * @return self A new instance of Usuario.
     */
    public static function fromDatabase(array $data): self
    {
        return new self(
            $data["rol"],
            $data["nombre"],
            $data["apellidos"],
            $data["email"],
            $data["password_hash"],
            $data["telefono"] ?? null,
            [
                'fecha_registro' => $data["fecha_registro"],
                'activo' => (int) ($data["activo"] ?? 1),
                'id_usuario' => $data["id_usuario"] ?? null
            ]
        );
    }
}
