<?php

/**
 * Service logic for handling everything related to hair salon services.
 */

namespace Servicios\Application;

use Servicios\Domain\Servicio;
use Servicios\Infrastructure\ServicioRepository;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;
use Shared\Domain\Exceptions\{ServiceNotFoundException, ServiceOperationException, ServiceValidationException};

/**
 * Service class that coordinates service-related operations.
 */
class ServicioService
{
    private ServicioRepository $repository;

    /**
     * ServicioService constructor.
     * @param ServicioRepository $repository The repository for service data operations.
     */
    public function __construct(ServicioRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Creates a new service after validation
     * @param array $data Service data
     * @return Servicio Created service
     * @throws ServiceValidationException If validation fails
     * @throws ServiceOperationException If the service could not be saved
     */
    public function createService(array $data): Servicio
    {
        $this->validateServiceData($data);

        $servicio = new Servicio(
            $data['nombre_servicio'],
            (int) $data['duracion_minutos'],
            $data['descripcion'],
            (float) $data['precio'],
            null,
            $data['activo'] ?? true
        );

        $id = $this->repository->save($servicio);

        if (!$id) {
            throw new ServiceOperationException('Error creating service');
        }

        return new Servicio(
            $data['nombre_servicio'],
            (int) $data['duracion_minutos'],
            $data['descripcion'],
            (float) $data['precio'],
            $id
        );
    }

    /**
     * Updates an existing service after validation
     * @param int $id Service ID
     * @param array $data Updated service data
     * @return Servicio Updated service
     * @throws ServiceNotFoundException If service not found
     * @throws ServiceValidationException If validation fails
     * @throws ServiceOperationException If the update fails
     */
    public function updateService(int $id, array $data): Servicio
    {
        $existingService = $this->repository->getServicioById($id);

        if (!$existingService) {
            throw new ServiceNotFoundException('Service not found');
        }

        $this->validateServiceData($data);

        $servicio = new Servicio(
            $data['nombre_servicio'],
            (int) $data['duracion_minutos'],
            $data['descripcion'],
            (float) $data['precio'],
            $id,
            $data['activo'] ?? $existingService->isActivo()
        );

        $success = $this->repository->update($servicio);

        if (!$success) {
            throw new ServiceOperationException('Error updating service');
        }

        return $servicio;
    }

    /**
     * Deactivates a service (soft delete)
     * @param int $id Service ID
     * @throws ServiceNotFoundException If service not found
     * @throws ServiceOperationException If the deactivation fails
     */
    public function deactivateService(int $id): void
    {
        $existingService = $this->repository->getServicioById($id);

        if (!$existingService) {
            throw new ServiceNotFoundException('Service not found');
        }

        $success = $this->repository->deactivate($id);

        if (!$success) {
            throw new ServiceOperationException('Error deactivating service');
        }
    }

    /**
     * Activates a service
     * @param int $id Service ID
     * @throws ServiceNotFoundException If service not found
     * @throws ServiceOperationException If the activation fails
     */
    public function activateService(int $id): void
    {
        $existingService = $this->repository->getServicioById($id);

        if (!$existingService) {
            throw new ServiceNotFoundException('Service not found');
        }

        $success = $this->repository->activate($id);

        if (!$success) {
            throw new ServiceOperationException('Error activating service');
        }
    }

    /**
     * Gets a service by ID
     * @param int $id Service ID
     * @return Servicio|null Service or null if not found
     */
    public function getServiceById(int $id): ?Servicio
    {
        return $this->repository->getServicioById($id);
    }

    /**
     * Gets all services, optionally filtered by active status
     * @param bool|null $activo Filter by active status (null = all)
     * @return Servicio[] Array of Servicio objects
     */
    public function getAllServices(?bool $activo = null): array
    {
        return $this->repository->getAllServicios($activo);
    }

    /**
     * Validates service data
     * @param array $data Service data to validate
     * @throws ServiceValidationException If validation fails
     */
    private function validateServiceData(array $data): void
    {
        try {
            v::key('nombre_servicio', v::stringType()->notEmpty()->length(3, 100))
                ->key('descripcion', v::stringType()->notEmpty())
                ->key('duracion_minutos', v::intType()->between(15, 300))
                ->key('precio', v::numericVal()->min(0))
                ->key('activo', v::boolType(), false)
                ->assert($data);
        } catch (ValidationException $e) {
            throw new ServiceValidationException('Validation error: ' . $e->getMessage());
        }
    }
}
