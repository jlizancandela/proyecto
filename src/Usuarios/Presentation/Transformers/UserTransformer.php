<?php

/**
 * Utility to convert user objects into arrays or other formats for the API.
 */

namespace Usuarios\Presentation\Transformers;

use Usuarios\Domain\Usuario;

/**
 * Transforms user domain models into presentation-friendly data structures.
 */
class UserTransformer
{
    /**
     * Transforms a single user domain model into a flat array for general presentation.
     *
     * @param Usuario $user The user domain model to transform.
     * @return array The transformed array containing user data.
     */
    public static function toArray(Usuario $user): array
    {
        return [
            'id' => $user->getId(),
            'nombreCompleto' => $user->getNombre() . ' ' . $user->getApellidos(),
            'nombre' => $user->getNombre(),
            'apellidos' => $user->getApellidos(),
            'email' => $user->getEmail(),
            'telefono' => $user->getTelefono(),
            'rol' => $user->getRol()->value,
            'activo' => $user->getActivo(),
            'fechaRegistro' => $user->getFechaRegistro()->format('d/m/Y'),
            'fechaRegistroISO' => $user->getFechaRegistro()->format('Y-m-d')
        ];
    }

    /**
     * Transforms a collection of user domain models into an array of arrays.
     *
     * @param Usuario[] $users The list of users to transform.
     * @return array[] A collection of user data arrays.
     */
    public static function toArrayCollection(array $users): array
    {
        return array_map([self::class, 'toArray'], $users);
    }

    /**
     * Transforms a single user domain model into a structure suitable for JSON APIs.
     *
     * @param Usuario $user The user domain model to transform.
     * @return array The user data as an associative array.
     */
    public static function toJsonApi(Usuario $user): array
    {
        return [
            'id' => $user->getId(),
            'nombre' => $user->getNombre(),
            'apellidos' => $user->getApellidos(),
            'email' => $user->getEmail(),
            'telefono' => $user->getTelefono(),
            'rol' => $user->getRol()->value,
            'activo' => $user->getActivo(),
            'fecha_registro' => $user->getFechaRegistro()->format('Y-m-d')
        ];
    }

    /**
     * Transforms a collection of user domain models into a JSON API-ready structure.
     *
     * @param Usuario[] $users The list of users to transform.
     * @return array[] A collection of user associative arrays.
     */
    public static function toJsonApiCollection(array $users): array
    {
        return array_map([self::class, 'toJsonApi'], $users);
    }

    /**
     * Determines the Bootstrap badge color based on the user's role.
     *
     * @param string $role The user role name.
     * @return string The associated CSS color class.
     */
    private static function getRoleBadgeColor(string $role): string
    {
        $colors = [
            'Admin' => 'primary',
            'Especialista' => 'info',
            'Cliente' => 'secondary'
        ];
        return $colors[$role] ?? 'secondary';
    }
}
