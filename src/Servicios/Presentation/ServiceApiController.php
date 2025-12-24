<?php

namespace Servicios\Presentation;

use Servicios\Application\ServicioService;

class ServiceApiController
{
    private ServicioService $service;

    public function __construct(ServicioService $service)
    {
        $this->service = $service;
    }

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
                    'descripcion' => $servicio->getDescripcion()
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
     * Gets a single service by ID
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
                    'precio' => $servicio->getPrecio()
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
     * Creates a new service
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
                    'precio' => $servicio->getPrecio()
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
     * Updates an existing service
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
                    'precio' => $servicio->getPrecio()
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
     * Deletes a service
     */
    public function deleteService(int $id): void
    {
        header('Content-Type: application/json');

        try {
            $this->service->deleteService($id);

            echo json_encode([
                'success' => true,
                'message' => 'Servicio eliminado correctamente'
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
