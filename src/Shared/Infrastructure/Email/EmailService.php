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
     * @param string $textContent Plain text content (optional)
     * @return array API response
     * @throws \RuntimeException If sending fails
     */
    public function sendEmail(
        string $to,
        string $subject,
        string $htmlContent,
        string $textContent = ''
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

        if (!empty($textContent)) {
            $data['textContent'] = $textContent;
        }

        error_log(sprintf(
            '[EmailService] Sending email to: %s | Subject: %s | Time: %s',
            $to,
            $subject,
            date('Y-m-d H:i:s')
        ));

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

        $textContent = "Recuperación de contraseña\n\n"
            . "Has solicitado restablecer tu contraseña.\n\n"
            . "Visita el siguiente enlace para continuar:\n"
            . "{$resetLink}\n\n"
            . "Si no solicitaste este cambio, ignora este mensaje.\n"
            . "Este enlace expirará en 1 hora.";

        // In development, save token to file for testing
        $host = $_SERVER['SERVER_NAME'] ?? '';
        $isDev = str_contains($host, '.ddev.site') || $host === 'localhost';

        if ($isDev) {
            // Extract token from reset link
            if (preg_match('/token=([^&]+)/', $resetLink, $matches)) {
                $token = $matches[1];
                // Use public directory which is accessible from both container and host
                $logDir = $_SERVER['DOCUMENT_ROOT'] . '/.tokens';

                // Create directory if it doesn't exist
                if (!is_dir($logDir)) {
                    mkdir($logDir, 0777, true);
                }

                // Write token to file (one file per email)
                $filename = $logDir . '/' . md5($to) . '.txt';
                file_put_contents($filename, $token);
            }
        }

        // Log password recovery email sent (metadata only for security)
        error_log(sprintf(
            '[EmailService] Password recovery sent to: %s | Time: %s',
            $to,
            date('Y-m-d H:i:s')
        ));

        return $this->sendEmail($to, $subject, $htmlContent, $textContent);
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
