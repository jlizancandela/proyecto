<?php

namespace Reservas\Presentation;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Usuarios\Application\UserService;
use Usuarios\Application\AuthService;
use Reservas\Application\ReservaService;

class PaymentController
{
    private UserService $userService;
    private AuthService $authService;
    private ReservaService $reservaService;

    public function __construct(UserService $userService, ReservaService $reservaService, AuthService $authService)
    {
        $this->userService = $userService;
        $this->reservaService = $reservaService;
        $this->authService = $authService;
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

            $reserva = $this->reservaService->getReservaById((int)$bookingId);
            if (!$reserva) {
                throw new \Exception('Reserva no encontrada');
            }

            $currentUser = $this->authService->getCurrentUser();
            if (!$currentUser || (int)$reserva->id_cliente !== (int)$currentUser->getId()) {
                throw new \Exception('No tienes permiso para pagar esta reserva');
            }

            if ($reserva->estado === 'Pagada') {
                throw new \Exception('Esta reserva ya ha sido pagada');
            }

            $stripeKey = $_ENV['STRIPE_SECRET_KEY'] ?? $_SERVER['STRIPE_SECRET_KEY'] ?? getenv('STRIPE_SECRET_KEY');
            if (!$stripeKey) {
                 throw new \Exception('Stripe Secret Key not configured');
            }
            Stripe::setApiKey($stripeKey);
            
            $domain = $_ENV['APP_URL'] ?? 'http://localhost:8000';

            $price = isset($reserva->servicio_precio) ? (int)($reserva->servicio_precio * 100) : 2000;
            $serviceName = $reserva->servicio_nombre ?? 'Servicio de Peluquería';
            $customerEmail = $reserva->cliente_email ?? $currentUser->getEmail() ?? null;

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
                'success_url' => $domain . '/payment/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $domain . '/payment/cancel',
                'customer_email' => $customerEmail,
                'metadata' => [
                    'booking_id' => $bookingId,
                    'user_id' => $currentUser->getId()
                ]
            ]);

            http_response_code(200);
            echo json_encode(['url' => $checkout_session->url]);

        } catch (\Exception $e) {
            error_log("Error creating checkout session for booking {$bookingId}: " . $e->getMessage());
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

        if (!$sessionId) {
            header('Location: /user/reservas');
            exit;
        }

        try {
            $stripeKey = $_ENV['STRIPE_SECRET_KEY'] ?? $_SERVER['STRIPE_SECRET_KEY'] ?? getenv('STRIPE_SECRET_KEY');
            Stripe::setApiKey($stripeKey);

            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $bookingId = $session->metadata->booking_id ?? null;
                
                if ($bookingId) {
                    $this->reservaService->updateReservaStatus((int)$bookingId, 'Pagada');
                    header('Location: /user/reservas?status=payment_success');
                    exit;
                }
            }

        } catch (\Exception $e) {
            error_log("Payment verification failed for session {$sessionId}: " . $e->getMessage());
        }

        header('Location: /user/reservas?status=payment_error');
        exit;
    }
}
