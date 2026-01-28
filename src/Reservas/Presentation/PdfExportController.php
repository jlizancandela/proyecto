<?php

/**
 * Utility to generate PDF documents for salon appointments or reports.
 */

namespace Reservas\Presentation;

use Dompdf\Dompdf;
use Latte\Engine;
use Reservas\Application\ReservaService;
use Shared\Domain\Exceptions\MissingDependencyException;

/**
 * Utility to generate PDF documents for salon appointments or reports.
 */
class PdfExportController
{
    private const MAX_EXPORT_ROWS = 1000;

    private Engine $latte;
    private  ?\Usuarios\Application\UserService $userService = null;
    private ReservaService $reservaService;

    /**
     * PdfExportController constructor.
     * @param Engine $latte The Latte templating engine instance.
     * @param ReservaService $reservaService The booking service instance.
     * @param \Usuarios\Application\UserService|null $userService The user service instance (optional).
     */
    public function __construct(Engine $latte, ReservaService $reservaService, ?\Usuarios\Application\UserService $userService = null)
    {
        $this->latte = $latte;
        $this->reservaService = $reservaService;
        $this->userService = $userService;
    }

    /**
     * Exports user bookings to PDF applying optional filters.
     *
     * Generates a PDF document with all bookings matching the filters.
     * The PDF is displayed in the browser (not downloaded automatically).
     * Limits to 1000 bookings maximum to avoid memory issues.
     *
     * @return void Sends the PDF directly to the browser
     */
    public function exportReservas(): void
    {
        $fromDate = $_GET['fecha_desde'] ?? null;
        $toDate = $_GET['fecha_hasta'] ?? null;
        $status = $_GET['estado'] ?? null;

        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            header('Location: /login');
            exit;
        }

        $bookings = $this->reservaService->getAllReservasByFilter(
            $userId,
            1000,
            0,
            $fromDate,
            $toDate,
            $status
        );

        $html = $this->latte->renderToString(
            __DIR__ . '/../../../views/pdf/reservas-pdf.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'bookings' => $bookings,
                'fechaDesde' => $fromDate,
                'fechaHasta' => $toDate,
                'estado' => $status
            ]
        );

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("mis-reservas.pdf", ["Attachment" => false]);
    }

    /**
     * Exports all bookings (admin view) to PDF applying filters.
     *
     * @return void Sends the PDF directly to the browser
     */
    public function exportAdminReservas(): void
    {
        $filters = [];

        if (!empty($_GET['cliente'])) {
            $filters['cliente'] = (int)$_GET['cliente'];
        }

        if (!empty($_GET['especialista'])) {
            $filters['especialista'] = (int)$_GET['especialista'];
        }

        if (!empty($_GET['estado'])) {
            $filters['estado'] = $_GET['estado'];
        }

        if (!empty($_GET['fecha_desde'])) {
            $filters['fecha_desde'] = $_GET['fecha_desde'];
        }

        if (!empty($_GET['fecha_hasta'])) {
            $filters['fecha_hasta'] = $_GET['fecha_hasta'];
        }

        if (!empty($_GET['sort'])) {
            $filters['sort'] = $_GET['sort'];
        }

        if (!empty($_GET['order'])) {
            $filters['order'] = $_GET['order'];
        }

        $bookings = $this->reservaService->getAllReservasWithFilters(
            $filters,
            self::MAX_EXPORT_ROWS,
            0
        );

        $html = $this->latte->renderToString(
            __DIR__ . '/../../../views/pdf/admin-reservas-pdf.latte',
            [
                'bookings' => $bookings,
                'fechaDesde' => $filters['fecha_desde'] ?? null,
                'fechaHasta' => $filters['fecha_hasta'] ?? null,
                'estado' => $filters['estado'] ?? null,
                'clienteId' => $filters['cliente'] ?? null,
                'especialistaId' => $filters['especialista'] ?? null
            ]
        );

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dompdf->stream("gestion-reservas.pdf", ["Attachment" => false]);
    }

    /**
     * Exports users to PDF applying filters and sorting.
     *
     * @return void Sends the PDF directly to the browser
     */
    public function exportAdminUsers(): void
    {
        if (!$this->userService) {
            throw new MissingDependencyException('UserService is required for this action');
        }

        $filters = [];

        if (!empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }

        if (!empty($_GET['rol'])) {
            $filters['rol'] = $_GET['rol'];
        }

        if (isset($_GET['estado']) && $_GET['estado'] !== '') {
            $filters['estado'] = $_GET['estado'];
        }

        if (!empty($_GET['sort'])) {
            $filters['sort'] = $_GET['sort'];
        }

        if (!empty($_GET['order'])) {
            $filters['order'] = $_GET['order'];
        }

        $users = $this->userService->getAllUsersWithFilters($filters, self::MAX_EXPORT_ROWS, 0);

        $html = $this->latte->renderToString(
            __DIR__ . '/../../../views/pdf/admin-users-pdf.latte',
            [
                'users' => $users,
                'search' => $filters['search'] ?? null,
                'rol' => $filters['rol'] ?? null,
                'estado' => $filters['estado'] ?? null,
                'sort' => $filters['sort'] ?? null,
                'order' => $filters['order'] ?? null
            ]
        );

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dompdf->stream("gestion-usuarios.pdf", ["Attachment" => false]);
    }
}
