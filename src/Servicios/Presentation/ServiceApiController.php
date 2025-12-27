<?php

/**
 * ServiceApiController
 *
 * Handles API requests related to service management, allowing to retrieve, create, update,
 * activate, and deactivate services.
 */

namespace Servicios\Presentation;

use Servicios\Application\ServicioService;

class ServiceApiController
{
    private ServicioService $service;

    /**
     * ServiceApiController constructor.
     * @param ServicioService $service The service application service instance.
     */
    public function __construct(ServicioService $service)
    {
        $this->service = $service;
    }

    /**
     * Retrieves all services.
     * @return void
     */
    public function getAll(): void
    {
        header('Content-Type: application/json');
        try {
            $servicios = $this->service->getAllServices();

            $data = array_map(function ($servicio) {
                return [
                    'id' => $servicio->getIdServicio(),
                    'nombre_servicio' => $servicio->getNombreServicio(),
                    'duracion_minutos' => $servicio->getDuracionMinutos(),
                    'precio' => $servicio->getPrecio(),
                    'descripcion' => $servicio->getDescripcion(),
                    'activo' => $servicio->isActivo()
                ];
            }, $servicios);

            echo json_encode([
                'success' => true,
                'servicios' => $data
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener servicios'
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Retrieves a single service by its ID.
     * @param int $id The ID of the service to retrieve.
     * @return void
     */
    public function getServiceById(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $servicio = $this->service->getServiceById($id);

            if (!$servicio) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'error' => 'Servicio no encontrado'
                ], JSON_PRETTY_PRINT);
                return;
            }

            echo json_encode([
                'success' => true,
                'data' => [
                    'id' => $servicio->getIdServicio(),
                    'nombre_servicio' => $servicio->getNombreServicio(),
                    'descripcion' => $servicio->getDescripcion(),
                    'duracion_minutos' => $servicio->getDuracionMinutos(),
                    'precio' => $servicio->getPrecio(),
                    'activo' => $servicio->isActivo()
                ]
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Creates a new service.
     * @return void
     */
    public function createService(): void
    {
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Datos inválidos'
                ], JSON_PRETTY_PRINT);
                return;
            }

            $servicio = $this->service->createService($data);

            echo json_encode([
                'success' => true,
                'message' => 'Servicio creado correctamente',
                'data' => [
                    'id' => $servicio->getIdServicio(),
                    'nombre_servicio' => $servicio->getNombreServicio(),
                    'descripcion' => $servicio->getDescripcion(),
                    'duracion_minutos' => $servicio->getDuracionMinutos(),
                    'precio' => $servicio->getPrecio(),
                    'activo' => $servicio->isActivo()
                ]
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Updates an existing service.
     * @param int $id The ID of the service to update.
     * @return void
     */
    public function updateService(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Datos inválidos'
                ], JSON_PRETTY_PRINT);
                return;
            }

            $servicio = $this->service->updateService($id, $data);

            echo json_encode([
                'success' => true,
                'message' => 'Servicio actualizado correctamente',
                'data' => [
                    'id' => $servicio->getIdServicio(),
                    'nombre_servicio' => $servicio->getNombreServicio(),
                    'descripcion' => $servicio->getDescripcion(),
                    'duracion_minutos' => $servicio->getDuracionMinutos(),
                    'precio' => $servicio->getPrecio(),
                    'activo' => $servicio->isActivo()
                ]
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Deactivates a service (sets its 'activo' status to false).
     * @param int $id The ID of the service to deactivate.
     * @return void
     */
    public function deactivateService(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $this->service->deactivateService($id);

            echo json_encode([
                'success' => true,
                'message' => 'Servicio desactivado correctamente'
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Activates a service (sets its 'activo' status to true).
     * @param int $id The ID of the service to activate.
     * @return void
     */
    public function activateService(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $this->service->activateService($id);

            echo json_encode([
                'success' => true,
                'message' => 'Servicio activado correctamente'
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ], JSON_PRETTY_PRINT);
        }
    }
}
