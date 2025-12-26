<?php

/**
 * Ejemplo de uso del EmailService
 * 
 * Para probar este servicio:
 * 1. Asegúrate de que las variables EMAIL_API_KEY y EMAIL_SENDER estén en .env
 * 2. Ejecuta: php ejemplo-email.php tu-email@example.com
 */

require_once __DIR__ . '/vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Incluir dependencias
require_once __DIR__ . '/src/Shared/Infrastructure/dependencies.php';

if ($argc < 2) {
    echo "Uso: php ejemplo-email.php <email-destinatario>\n";
    exit(1);
}

$emailDestino = $argv[1];

try {
    // Ejemplo 1: Email de recuperación de contraseña
    echo "Enviando email de recuperación de contraseña a {$emailDestino}...\n";

    $resetLink = 'https://tu-dominio.com/reset-password?token=abc123xyz';
    $resultado = $emailService->sendPasswordRecoveryEmail($emailDestino, $resetLink);

    echo "✓ Email enviado exitosamente\n";
    echo "Message ID: " . ($resultado['messageId'] ?? 'N/A') . "\n";

    // Ejemplo 2: Email genérico (comentado)
    /*
    $resultado = $emailService->sendEmail(
        $emailDestino,
        'Asunto de prueba',
        '<h1>Hola</h1><p>Este es un email de prueba.</p>',
        'Hola, este es un email de prueba.'
    );
    */
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
