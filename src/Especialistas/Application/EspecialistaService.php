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
     * @param EspecialistaRepository $especialistaRepository
     */
    public function __construct(EspecialistaRepository $especialistaRepository)
    {
        $this->especialistaRepository = $especialistaRepository;
    }

    /**
     * @return EspecialistaUsuarioDTO[]
     */
    public function getAllEspecialistas(): array
    {
        return $this->especialistaRepository->getAllEspecialistasConUsuario();
    }
}
