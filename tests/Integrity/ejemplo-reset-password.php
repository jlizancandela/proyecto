<?php

/**
 * Ejemplo de uso del sistema de recuperación de contraseña
 * 
 * Para probar:
 * php ejemplo-reset-password.php maria.lopez@email.com
 */

require_once __DIR__ . '/vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Incluir dependencias
require_once __DIR__ . '/src/Shared/Infrastructure/dependencies.php';

if ($argc < 2) {
    echo "Uso: php ejemplo-reset-password.php <email-usuario>\n";
    exit(1);
}

$emailUsuario = $argv[1];

try {
    echo "Generando token de recuperación para {$emailUsuario}...\n";

    // Generar token
    $token = $authService->generatePasswordResetToken($emailUsuario);

    echo "✓ Token generado: {$token}\n";
    echo "✓ Expiración: 1 hora desde ahora\n\n";

    // Construir URL de recuperación
    $resetUrl = "https://proyecto.ddev.site/reset-password?token={$token}";

    echo "Enviando email de recuperación...\n";

    // Enviar email
    $resultado = $emailService->sendPasswordRecoveryEmail($emailUsuario, $resetUrl);

    echo "✓ Email enviado exitosamente\n";
    echo "Message ID: " . ($resultado['messageId'] ?? 'N/A') . "\n";
    echo "\nURL de recuperación: {$resetUrl}\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
