<?php

/**
 * Service layer for managing specialists and their schedules
 */

namespace Especialistas\Application;

use Especialistas\Infrastructure\EspecialistaRepository;
use Especialistas\Application\EspecialistaUsuarioDTO;

class EspecialistaService
{
    private EspecialistaRepository $especialistaRepository;

    /**
     * EspecialistaService constructor.
     * @param EspecialistaRepository $especialistaRepository The specialist repository instance.
     */
    public function __construct(EspecialistaRepository $especialistaRepository)
    {
        $this->especialistaRepository = $especialistaRepository;
    }

    /**
     * Retrieves all specialists with their associated user data.
     * @return EspecialistaUsuarioDTO[] An array of EspecialistaUsuarioDTO objects.
     */
    public function getAllEspecialistas(): array
    {
        return $this->especialistaRepository->getAllEspecialistasConUsuario();
    }
}
