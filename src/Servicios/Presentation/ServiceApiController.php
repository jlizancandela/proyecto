<?php

namespace Servicios\Presentation;

use Servicios\Infrastructure\ServicioRepository;

class ServiceApiController
{
    private ServicioRepository $repository;

    public function __construct(ServicioRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll(): void
    {
        header('Content-Type: application/json');
        try {
            $servicios = $this->repository->getAllServicios();

            $data = array_map(function ($servicio) {
                return [
                    'id' => $servicio->getIdServicio(),
                    'nombre' => $servicio->getNombreServicio(),
                    'duracion' => $servicio->getDuracionMinutos(),
                    'precio' => $servicio->getPrecio(),
                    'descripcion' => $servicio->getDescripcion()
                ];
            }, $servicios);

            echo json_encode($data);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener servicios']);
        }
    }
}
