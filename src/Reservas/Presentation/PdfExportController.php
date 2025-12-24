<?php

namespace Reservas\Presentation;

use Dompdf\Dompdf;
use Latte\Engine;
use Reservas\Application\ReservaService;

/**
 * Controlador para exportar reservas a formato PDF
 * 
 * Genera documentos PDF de las reservas del usuario usando Dompdf.
 * Respeta los filtros aplicados (fecha y estado) para exportar solo
 * las reservas seleccionadas.
 */
class PdfExportController
{
    private Engine $latte;
    private ReservaService $reservaService;

    public function __construct(Engine $latte, ReservaService $reservaService)
    {
        $this->latte = $latte;
        $this->reservaService = $reservaService;
    }

    /**
     * Exporta las reservas del usuario a PDF aplicando filtros opcionales
     * 
     * Genera un documento PDF con todas las reservas que cumplan los filtros.
     * El PDF se muestra en el navegador (no se descarga automáticamente).
     * Limita a 1000 reservas máximo para evitar problemas de memoria.
     * 
     * @return void Envía el PDF directamente al navegador
     */
    public function exportReservas(): void
    {
        // Get filter parameters
        $fechaDesde = $_GET['fecha_desde'] ?? null;
        $fechaHasta = $_GET['fecha_hasta'] ?? null;
        $estado = $_GET['estado'] ?? null;

        // Get user ID from session
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        // Get all bookings (no pagination for PDF)
        $bookings = $this->reservaService->getAllReservasByFilter(
            $userId,
            1000, // Max bookings
            0,
            $fechaDesde,
            $fechaHasta,
            $estado
        );

        // Render HTML using Latte
        $html = $this->latte->renderToString(
            __DIR__ . '/../../../views/pdf/reservas-pdf.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'bookings' => $bookings,
                'fechaDesde' => $fechaDesde,
                'fechaHasta' => $fechaHasta,
                'estado' => $estado
            ]
        );

        // Generate PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Output PDF
        $dompdf->stream("mis-reservas.pdf", ["Attachment" => false]);
    }

    /**
     * Exporta todas las reservas (visión admin) a PDF aplicando filtros
     *
     * @return void Envía el PDF directamente al navegador
     */
    public function exportAdminReservas(): void
    {
        // Get filter parameters mapped from query string
        $filtros = [];

        if (!empty($_GET['cliente'])) {
            $filtros['cliente'] = (int)$_GET['cliente'];
        }

        if (!empty($_GET['especialista'])) {
            $filtros['especialista'] = (int)$_GET['especialista'];
        }

        if (!empty($_GET['estado'])) {
            $filtros['estado'] = $_GET['estado'];
        }

        if (!empty($_GET['fecha_desde'])) {
            $filtros['fecha_desde'] = $_GET['fecha_desde'];
        }

        if (!empty($_GET['fecha_hasta'])) {
            $filtros['fecha_hasta'] = $_GET['fecha_hasta'];
        }

        if (!empty($_GET['sort'])) {
            $filtros['sort'] = $_GET['sort'];
        }

        if (!empty($_GET['order'])) {
            $filtros['order'] = $_GET['order'];
        }

        // Get all bookings (limit 1000 for PDF performance)
        $bookings = $this->reservaService->getAllReservasWithFilters(
            $filtros,
            1000,
            0
        );

        // Render HTML using Latte
        $html = $this->latte->renderToString(
            __DIR__ . '/../../../views/pdf/admin-reservas-pdf.latte',
            [
                'bookings' => $bookings,
                'fechaDesde' => $filtros['fecha_desde'] ?? null,
                'fechaHasta' => $filtros['fecha_hasta'] ?? null,
                'estado' => $filtros['estado'] ?? null,
                'clienteId' => $filtros['cliente'] ?? null,
                'especialistaId' => $filtros['especialista'] ?? null
            ]
        );

        // Generate PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); // Landscape for better table fit
        $dompdf->render();

        // Output PDF
        $dompdf->stream("gestion-reservas.pdf", ["Attachment" => false]);
    }
}
