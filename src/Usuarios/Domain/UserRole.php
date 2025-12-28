<?php

/**
 * All the different types of users that can access the application.
 */

namespace Usuarios\Domain;

/**
 * Enum-like class for user roles (Admin, Specialist, Client).
 */
enum UserRole: string
{
    case Admin = "Admin";
    case Cliente = "Cliente";
    case Especialista = "Especialista";
}
