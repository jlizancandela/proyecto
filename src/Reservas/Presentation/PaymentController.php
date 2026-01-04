<?php

namespace Reservas\Presentation;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Usuarios\Application\UserService;
use Reservas\Application\ReservaService;

class PaymentController
{
    private UserService $userService;
    private ReservaService $reservaService;

    public function __construct(UserService $userService, ReservaService $reservaService)
    {
        $this->userService = $userService;
        $this->reservaService = $reservaService;
    }

    /**
     * Creates a Stripe Checkout Session.
     * Receives bookingId via JSON POST request.
     */
    public function createCheckoutSession(): void
    {
        header('Content-Type: application/json');

        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            $bookingId = $data['bookingId'] ?? null;

            if (!$bookingId) {
                throw new \Exception('Booking ID is required');
            }

            // Get Booking Details
            $reserva = $this->reservaService->getReservaById((int)$bookingId);
            if (!$reserva) {
                throw new \Exception('Reserva no encontrada');
            }

            // Config Stripe
            $stripeKey = $_ENV['STRIPE_SECRET_KEY'] ?? $_SERVER['STRIPE_SECRET_KEY'] ?? getenv('STRIPE_SECRET_KEY');
            if (!$stripeKey) {
                 throw new \Exception('Stripe Secret Key not configured');
            }
            Stripe::setApiKey($stripeKey);
            
            $domain = $_ENV['APP_URL'] ?? 'http://localhost:8000';

            // Determine price and name
            $price = 2000; // 20.00 EUR fallback
            $serviceName = 'Servicio de Peluquería';
            
            // Check properties safely
            if (isset($reserva->nombre_servicio)) {
                $serviceName = $reserva->nombre_servicio;
            }
            
            $customerEmail = null;
            if (isset($reserva->email_cliente)) {
                $customerEmail = $reserva->email_cliente;
            }
            
            $customerId = null;
            if (isset($reserva->id_cliente)) {
                $customerId = $reserva->id_cliente;
            }

            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $serviceName,
                        ],
                        'unit_amount' => $price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $domain . '/payment/success?session_id={CHECKOUT_SESSION_ID}&booking_id=' . $bookingId,
                'cancel_url' => $domain . '/payment/cancel',
                'customer_email' => $customerEmail,
                'metadata' => [
                    'booking_id' => $bookingId,
                    'user_id' => $customerId
                ]
            ]);

            http_response_code(200);
            echo json_encode(['url' => $checkout_session->url]);

        } catch (\Exception $e) {
            error_log("Error creating checkout session: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Handles the success callback from Stripe
     */
    public function handleSuccess(): void
    {
        $sessionId = $_GET['session_id'] ?? null;
        $bookingId = $_GET['booking_id'] ?? null;

        if (!$sessionId || !$bookingId) {
            header('Location: /user/reservas');
            exit;
        }

        try {
            $stripeKey = $_ENV['STRIPE_SECRET_KEY'] ?? $_SERVER['STRIPE_SECRET_KEY'] ?? getenv('STRIPE_SECRET_KEY');
            Stripe::setApiKey($stripeKey);

            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $this->reservaService->updateReservaStatus((int)$bookingId, 'Pagada');
                header('Location: /user/reservas?status=payment_success');
                exit;
            }

        } catch (\Exception $e) {
            error_log("Payment verification failed: " . $e->getMessage());
        }

        header('Location: /user/reservas?status=payment_error');
        exit;
    }
}
