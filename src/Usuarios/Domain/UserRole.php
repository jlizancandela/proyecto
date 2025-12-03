<?php

namespace Usuarios\Domain;

/**
 * Defines the possible roles for a user.
 */
enum UserRole: string
{
    case Admin = "Admin";
    case Cliente = "Cliente";
    case Especialista = "Especialista";
}
