<?php

/**
 * BookingController
 *
 * Handles presentation logic for user booking pages.
 */

namespace Reservas\Presentation;

use Latte\Engine;

class BookingController
{
    private Engine $latte;

    /**
     * BookingController constructor.
     * @param Engine $latte The Latte templating engine instance.
     */
    public function __construct(Engine $latte)
    {
        $this->latte = $latte;
    }

    /**
     * Displays the new booking page for users.
     * @return string The rendered HTML content of the new booking page.
     */
    public function index()
    {
        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/NewBooking.latte',
            [
                'userName' => ucfirst($_SESSION['name'] ?? 'Usuario'),
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/user/reservas/nueva'
            ]
        );
    }
}
