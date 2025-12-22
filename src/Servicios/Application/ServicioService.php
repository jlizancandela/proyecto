<?php

namespace Servicios\Application;

use Servicios\Domain\Servicio;
use Servicios\Infrastructure\ServicioRepository;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

class ServicioService
{
    private ServicioRepository $repository;

    public function __construct(ServicioRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Creates a new service after validation
     * @param array $data Service data
     * @return Servicio Created service
     * @throws \Exception If validation fails
     */
    public function createService(array $data): Servicio
    {
        $this->validateServiceData($data);

        $servicio = new Servicio(
            $data['nombre_servicio'],
            (int) $data['duracion_minutos'],
            $data['descripcion'],
            (float) $data['precio']
        );

        $id = $this->repository->save($servicio);

        if (!$id) {
            throw new \Exception('Error creating service');
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
     * @throws \Exception If validation fails or service not found
     */
    public function updateService(int $id, array $data): Servicio
    {
        $existingService = $this->repository->getServicioById($id);

        if (!$existingService) {
            throw new \Exception('Service not found');
        }

        $this->validateServiceData($data);

        $servicio = new Servicio(
            $data['nombre_servicio'],
            (int) $data['duracion_minutos'],
            $data['descripcion'],
            (float) $data['precio'],
            $id
        );

        $success = $this->repository->update($servicio);

        if (!$success) {
            throw new \Exception('Error updating service');
        }

        return $servicio;
    }

    /**
     * Deletes a service
     * @param int $id Service ID
     * @throws \Exception If service not found
     */
    public function deleteService(int $id): void
    {
        $existingService = $this->repository->getServicioById($id);

        if (!$existingService) {
            throw new \Exception('Service not found');
        }

        $success = $this->repository->delete($id);

        if (!$success) {
            throw new \Exception('Error deleting service');
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
     * Gets all services
     * @return array Array of Servicio objects
     */
    public function getAllServices(): array
    {
        return $this->repository->getAllServicios();
    }

    /**
     * Validates service data
     * @param array $data Service data to validate
     * @throws \Exception If validation fails
     */
    private function validateServiceData(array $data): void
    {
        try {
            v::key('nombre_servicio', v::stringType()->notEmpty()->length(3, 100))
                ->key('descripcion', v::stringType()->notEmpty())
                ->key('duracion_minutos', v::intType()->between(15, 300))
                ->key('precio', v::numericVal()->min(0))
                ->assert($data);
        } catch (ValidationException $e) {
            throw new \Exception('Validation error: ' . $e->getMessage());
        }
    }
}
