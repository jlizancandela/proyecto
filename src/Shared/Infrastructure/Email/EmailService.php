<?php

namespace Shared\Infrastructure\Email;

/**
 * Servicio para enviar emails usando Brevo API con curl
 */
class EmailService
{
    private string $apiKey;
    private string $senderEmail;
    private string $apiUrl = 'https://api.brevo.com/v3/smtp/email';

    /**
     * Constructor del servicio de email
     * 
     * @throws \RuntimeException Si faltan las variables de entorno
     */
    public function __construct()
    {
        $this->apiKey = $_ENV['EMAIL_API_KEY'] ?? '';
        $this->senderEmail = $_ENV['EMAIL_SENDER'] ?? '';

        if (empty($this->apiKey) || empty($this->senderEmail)) {
            throw new \RuntimeException('Faltan configuraciones de email en .env');
        }
    }

    /**
     * Envía un email genérico usando Brevo API
     * 
     * @param string $to Email del destinatario
     * @param string $subject Asunto del email
     * @param string $htmlContent Contenido HTML del email
     * @param string $textContent Contenido en texto plano (opcional)
     * @return array Respuesta de la API
     * @throws \RuntimeException Si falla el envío
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

        return $this->sendRequest($data);
    }

    /**
     * Envía un email de recuperación de contraseña
     * 
     * @param string $to Email del destinatario
     * @param string $resetLink Link de recuperación
     * @return array Respuesta de la API
     * @throws \RuntimeException Si falla el envío
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

        return $this->sendEmail($to, $subject, $htmlContent, $textContent);
    }

    /**
     * Envía la petición a la API de Brevo usando curl
     * 
     * @param array $data Datos a enviar
     * @return array Respuesta de la API
     * @throws \RuntimeException Si falla la petición
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
            throw new \RuntimeException("Error en curl: {$curlError}");
        }

        $result = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300) {
            $errorMsg = $result['message'] ?? 'Error desconocido';
            throw new \RuntimeException("Error al enviar email: {$errorMsg} (HTTP {$httpCode})");
        }

        return $result;
    }
}
