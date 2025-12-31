<?php

/**
 * Tool used to send emails to users using the Brevo service.
 */

namespace Shared\Infrastructure\Email;


/**
 * Communicates with the Brevo API to deliver transactional emails.
 */
class EmailService
{
    private string $apiKey;
    private string $senderEmail;
    private string $apiUrl = 'https://api.brevo.com/v3/smtp/email';

    /**
     * Email service constructor
     *
     * @throws \RuntimeException If environment variables are missing
     */
    public function __construct()
    {
        $this->apiKey = $_ENV['EMAIL_API_KEY'] ?? '';
        $this->senderEmail = $_ENV['EMAIL_SENDER'] ?? '';

        if (empty($this->apiKey) || empty($this->senderEmail)) {
            throw new \RuntimeException('Missing email configurations in .env');
        }
    }

    /**
     * Sends a generic email using Brevo API
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $htmlContent HTML content of the email
     * @return array API response
     * @throws \RuntimeException If sending fails
     */
    public function sendEmail(
        string $to,
        string $subject,
        string $htmlContent
    ): array {
        $data = [
            'sender' => [
                'email' => $this->senderEmail
            ],
            'to' => [
                ['email' => $to]
            ],
            'subject' => $subject,
            'htmlContent' => $htmlContent
        ];

        return $this->sendRequest($data);
    }

    /**
     * Sends a password recovery email
     *
     * @param string $to Recipient email
     * @param string $resetLink Recovery link
     * @return array API response
     * @throws \RuntimeException If sending fails
     */
    public function sendPasswordRecoveryEmail(string $to, string $resetLink): array
    {
        $subject = 'Recuperación de contraseña';

        $htmlContent = "
            <html>
                <body style='font-family: Arial, sans-serif;'>
                    <h2>Recuperación de contraseña</h2>
                    <p>Has solicitado restablecer tu contraseña.</p>
                    <p>Haz clic en el siguiente enlace para continuar:</p>
                    <p>
                        <a href='{$resetLink}' style='background-color: #E83E8C; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                            Restablecer contraseña
                        </a>
                    </p>
                    <p>Si no solicitaste este cambio, ignora este mensaje.</p>
                    <p>Este enlace expirará en 1 hora.</p>
                </body>
            </html>
        ";


        return $this->sendEmail($to, $subject, $htmlContent);
    }


    /**
     * Sends the request to Brevo API using curl
     *
     * @param array $data Data to send
     * @return array API response
     * @throws \RuntimeException If the request fails
     */
    private function sendRequest(array $data): array
    {
        $ch = curl_init($this->apiUrl);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'api-key: ' . $this->apiKey,
                'content-type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        if ($curlError) {
            throw new \RuntimeException("Curl error: {$curlError}");
        }

        $result = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300) {
            $errorMsg = $result['message'] ?? 'Unknown error';
            throw new \RuntimeException("Error sending email: {$errorMsg} (HTTP {$httpCode})");
        }

        return $result;
    }
}
