<?php

/**
 * Handles admin statistics API endpoints for dashboard analytics.
 */

namespace Shared\Presentation;

use Especialistas\Infrastructure\EspecialistaRepository;
use Reservas\Application\ReservaService;
use Servicios\Application\ServicioService;

class StatsApiController
{
    private EspecialistaRepository $especialistaRepository;
    private ReservaService $reservaService;
    private ServicioService $servicioService;

    public function __construct(
        EspecialistaRepository $especialistaRepository,
        ReservaService $reservaService,
        ServicioService $servicioService
    ) {
        $this->especialistaRepository = $especialistaRepository;
        $this->reservaService = $reservaService;
        $this->servicioService = $servicioService;
    }

    /**
     * Returns all specialists with basic user data for admin panel.
     *
     * @return void
     */
    public function getEspecialistas(): void
    {
        header('Content-Type: application/json');
        try {
            $especialistas = $this->especialistaRepository->getAllEspecialistasWithUserData();
            echo json_encode([
                'success' => true,
                'especialistas' => $especialistas
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener especialistas'
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Returns specialist occupancy statistics for dashboard charts.
     *
     * @return void
     */
    public function getSpecialistOccupancy(): void
    {
        header('Content-Type: application/json');
        try {
            $especialistas = $this->especialistaRepository->getAllEspecialistasWithUserData();

            $labels = [];
            $data = [];
            $colors = [
                'rgba(255, 99, 132, 0.8)',
                'rgba(54, 162, 235, 0.8)',
                'rgba(255, 206, 86, 0.8)',
                'rgba(75, 192, 192, 0.8)',
                'rgba(153, 102, 255, 0.8)',
                'rgba(255, 159, 64, 0.8)',
                'rgba(199, 199, 199, 0.8)',
                'rgba(83, 102, 255, 0.8)',
                'rgba(255, 99, 255, 0.8)',
                'rgba(99, 255, 132, 0.8)'
            ];

            foreach ($especialistas as $especialista) {
                $labels[] = $especialista['nombre'] . ' ' . $especialista['apellidos'];

                $bookings = $this->reservaService->getAllReservasWithFilters([
                    'especialista' => $especialista['id'],
                    'estado' => null
                ], 1000, 0);

                $activeCount = count(array_filter($bookings, function ($booking) {
                    return $booking->estado !== 'Cancelada';
                }));

                $data[] = $activeCount;
            }

            echo json_encode([
                'success' => true,
                'labels' => $labels,
                'data' => $data,
                'colors' => array_slice($colors, 0, count($labels))
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener estadísticas de ocupación'
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Returns popular services statistics for dashboard charts.
     *
     * @return void
     */
    public function getPopularServices(): void
    {
        header('Content-Type: application/json');
        try {
            $servicios = $this->servicioService->getAllServices();

            $labels = [];
            $data = [];

            foreach ($servicios as $servicio) {
                $labels[] = $servicio->getNombreServicio();

                $bookings = $this->reservaService->getAllReservasWithFilters([
                    'servicio' => $servicio->getIdServicio()
                ], 1000, 0);

                $activeCount = count(array_filter($bookings, function ($booking) {
                    return $booking->estado !== 'Cancelada';
                }));

                $data[] = $activeCount;
            }

            echo json_encode([
                'success' => true,
                'labels' => $labels,
                'data' => $data
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener estadísticas de servicios'
            ], JSON_PRETTY_PRINT);
        }
    }

    /**
     * Returns today's KPIs (Key Performance Indicators) for dashboard.
     *
     * @return void
     */
    public function getTodayKpis(): void
    {
        header('Content-Type: application/json');
        try {
            $today = date('Y-m-d');

            $todayBookings = $this->reservaService->getAllReservasWithFilters([
                'fecha_desde' => $today,
                'fecha_hasta' => $today
            ], 1000, 0);

            $activeBookings = array_filter($todayBookings, function ($booking) {
                return $booking->estado !== 'Cancelada';
            });

            $totalBookings = count($activeBookings);

            $estimatedRevenue = array_reduce($activeBookings, function ($total, $booking) {
                return $total + ($booking->servicio_precio ?? 0);
            }, 0);

            echo json_encode([
                'success' => true,
                'totalBookings' => $totalBookings,
                'estimatedRevenue' => number_format($estimatedRevenue, 2, '.', '')
            ], JSON_PRETTY_PRINT);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Error al obtener KPIs del día'
            ], JSON_PRETTY_PRINT);
        }
    }
}
