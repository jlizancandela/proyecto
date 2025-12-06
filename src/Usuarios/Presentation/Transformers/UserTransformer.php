<?php

namespace Usuarios\Presentation\Transformers;

use Usuarios\Domain\Usuario;

class UserTransformer
{
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

    public static function toArrayCollection(array $users): array
    {
        return array_map([self::class, 'toArray'], $users);
    }

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

    public static function toJsonApiCollection(array $users): array
    {
        return array_map([self::class, 'toJsonApi'], $users);
    }

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
