<?php

namespace Especialistas\Presentation;

use Especialistas\Infrastructure\EspecialistaRepository;

class EspecialistaApiController
{
    private EspecialistaRepository $repository;

    public function __construct(EspecialistaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDisponibles(): void
    {
        header('Content-Type: application/json');

        try {
            // Obtener parámetros de la query string
            $idServicio = $_GET['servicio'] ?? null;
            $fecha = $_GET['fecha'] ?? null;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
            $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : null;

            // Validar parámetros requeridos
            if (!$idServicio || !$fecha) {
                http_response_code(400);
                echo json_encode([
                    'error' => 'Parámetros requeridos: servicio y fecha'
                ]);
                return;
            }

            // Validar formato de fecha
            $fechaObj = \DateTime::createFromFormat('Y-m-d', $fecha);
            if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fecha) {
                http_response_code(400);
                echo json_encode([
                    'error' => 'Formato de fecha inválido. Use Y-m-d (ejemplo: 2024-12-09)'
                ]);
                return;
            }

            // Obtener total de especialistas disponibles
            $total = $this->repository->countEspecialistasDisponibles(
                (int)$idServicio,
                $fecha
            );

            // Obtener especialistas disponibles con paginación
            $especialistas = $this->repository->getEspecialistasDisponibles(
                (int)$idServicio,
                $fecha,
                $limit,
                $offset
            );

            echo json_encode([
                'data' => $especialistas,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]);
        } catch (\Exception $e) {
            error_log("Error en getDisponibles: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'error' => 'Error al obtener especialistas disponibles'
            ]);
        }
    }
}
