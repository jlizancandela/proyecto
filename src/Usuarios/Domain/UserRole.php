<?php

namespace Usuarios\Domain;

enum UserRole: string
{
    case Admin = "Admin";
    case Cliente = "Cliente";
    case Especialista = "Especialista";
}
