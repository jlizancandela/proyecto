<?php

namespace Usuarios\Presentation;

use Latte\Engine;
use Reservas\Application\ReservaService;

class UserController
{
    private Engine $latte;
    private ReservaService $reservaService;

    public function __construct(Engine $latte, ReservaService $reservaService)
    {
        $this->latte = $latte;
        $this->reservaService = $reservaService;
    }

    public function index()
    {
        $userId = $_SESSION['user_id'] ?? null;
        $latestReserva = null;

        if ($userId) {
            $latestReserva = $this->reservaService->getLatestReserva((int)$userId);
        }

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/User.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/user',
                'latestReserva' => $latestReserva
            ]
        );
    }
}
