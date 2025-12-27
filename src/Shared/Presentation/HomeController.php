<?php

/**
 * HomeController
 *
 * Handles the main public-facing pages of the application, such as the homepage and contact form.
 */

namespace Shared\Presentation;

use Latte\Engine;
use Shared\Infrastructure\Email\EmailService;
use Respect\Validation\Validator as v;

class HomeController
{
    private Engine $latte;
    private EmailService $emailService;

    /**
     * HomeController constructor.
     *
     * @param Engine $latte The Latte templating engine instance.
     * @param EmailService $emailService The email service instance.
     */
    public function __construct(Engine $latte, EmailService $emailService)
    {
        $this->latte = $latte;
        $this->emailService = $emailService;
    }

    /**
     * Displays the homepage.
     *
     * @return string The rendered homepage content.
     */
    public function index(): string
    {
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;

        unset($_SESSION['success'], $_SESSION['error']);

        return $this->latte->renderToString(
            __DIR__ . '/../../../views/pages/Home.latte',
            [
                'currentUrl' => $_SERVER['REQUEST_URI'] ?? '/',
                'success' => $success,
                'error' => $error
            ]
        );
    }

    /**
     * Handles contact form submissions.
     * Validates input, sends an email to the administrator, and redirects the user.
     *
     * @return void
     */
    public function contact(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $message = $_POST['message'] ?? '';

        try {
            v::stringType()->notEmpty()->length(2, 100)->assert($name);
            v::email()->assert($email);
            v::stringType()->notEmpty()->length(10, 2000)->assert($message);

            $subject = "Nuevo mensaje de contacto de: $name";
            $htmlContent = "
                <html>
                    <body>
                        <h2>Nuevo mensaje de contacto</h2>
                        <p><strong>Nombre:</strong> $name</p>
                        <p><strong>Email:</strong> $email</p>
                        <p><strong>Mensaje:</strong></p>
                        <p>$message</p>
                    </body>
                </html>
            ";

            // Enviar email al administrador (usando el sender como receptor por simplicidad o configurar uno específico)
            $this->emailService->sendEmail(
                $_ENV['EMAIL_SENDER'],
                $subject,
                $htmlContent,
                "Mensaje de $name ($email):\n\n$message"
            );

            $_SESSION['success'] = '¡Gracias por tu mensaje! Te responderemos lo antes posible.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Error al enviar el mensaje: ' . $e->getMessage();
        }

        header('Location: /#contact');
        exit;
    }
}
