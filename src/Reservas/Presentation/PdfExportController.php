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
}
