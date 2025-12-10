<?php

namespace Reservas\Presentation;

use Latte\Engine;

class BookingController
{
    private Engine $latte;

    public function __construct(Engine $latte)
    {
        $this->latte = $latte;
    }

    public function index()
    {
        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Bookings.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/bookings'
            ]
        );
    }
}
