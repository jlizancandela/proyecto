<?php

namespace Especialistas\Presentation;

use Shared\Infrastructure\Database\Database;
use Especialistas\Application\EspecialistaService;
use Especialistas\Infrastructure\EspecialistaRepository;

class EspecialistaController
{
    private EspecialistaService $especialistaService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $especialistaRepo = new EspecialistaRepository($db);

        $this->especialistaService = new EspecialistaService(
            $especialistaRepo,
        );
    }

    public function index(): string
    {
        $especialistas = $this->especialistaService->getAllEspecialistas();

        ob_start();
?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <title>Especialistas</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    padding: 20px;
                }

                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 15px;
                    padding: 40px;
                }

                h1 {
                    color: #667eea;
                    margin-bottom: 20px;
                }

                .back-link {
                    display: inline-block;
                    margin-bottom: 20px;
                    color: #667eea;
                    text-decoration: none;
                }

                .grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                    gap: 20px;
                    margin-top: 20px;
                }

                .card {
                    background: #f8f9fa;
                    border-radius: 10px;
                    padding: 20px;
                    border-left: 4px solid #667eea;
                }

                .card h3 {
                    color: #333;
                    margin-bottom: 10px;
                }

                .card p {
                    color: #666;
                    margin: 5px 0;
                }

                .card a {
                    display: inline-block;
                    margin-top: 10px;
                    color: #667eea;
                    text-decoration: none;
                    font-weight: 500;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <a href="/" class="back-link">← Volver</a>
                <h1>👨‍⚕️ Especialistas</h1>
                <p>Total: <?= count($especialistas) ?></p>

                <div class="grid">
                    <?php foreach ($especialistas as $esp): ?>
                        <div class="card">
                            <h3><?= htmlspecialchars(
                                    $esp->getNombre() . " " . $esp->getApellidos(),
                                ) ?></h3>
                            <p><strong>Email:</strong> <?= htmlspecialchars(
                                                            $esp->getEmail(),
                                                        ) ?></p>
                            <p><strong>Descripción:</strong> <?= htmlspecialchars(
                                                                    $esp->getDescripcion() ?? "Sin descripción",
                                                                ) ?></p>
                            <a href="/especialistas/<?= $esp->getIdEspecialista() ?>">Ver detalles →</a>
                            <a href="/especialistas/<?= $esp->getIdEspecialista() ?>/horarios">Ver horarios →</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </body>

        </html>
    <?php return ob_get_clean();
    }

    public function show(int $id): string
    {
        $especialista = $this->especialistaService->getEspecialistaById($id);

        if (!$especialista):
            http_response_code(404);
            return "<h1>404 - Especialista no encontrado</h1>";
        endif;

        $servicios = $this->especialistaService->getServiciosEspecialista($id);

        ob_start();
    ?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <title>Especialista #<?= $id ?></title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    padding: 20px;
                }

                .container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 15px;
                    padding: 40px;
                }

                h1,
                h2 {
                    color: #667eea;
                }

                .back-link {
                    display: inline-block;
                    margin-bottom: 20px;
                    color: #667eea;
                    text-decoration: none;
                }

                .info-item {
                    padding: 15px;
                    background: #f8f9fa;
                    border-radius: 8px;
                    margin: 10px 0;
                }

                .servicios {
                    margin-top: 20px;
                }

                .servicio-tag {
                    display: inline-block;
                    background: #667eea;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 20px;
                    margin: 5px;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <a href="/especialistas" class="back-link">← Volver</a>
                <h1><?= htmlspecialchars(
                        $especialista->getNombre() .
                            " " .
                            $especialista->getApellidos(),
                    ) ?></h1>

                <div class="info-item">
                    <strong>Email:</strong> <?= htmlspecialchars(
                                                $especialista->getEmail(),
                                            ) ?>
                </div>
                <div class="info-item">
                    <strong>Descripción:</strong> <?= htmlspecialchars(
                                                        $especialista->getDescripcion() ?? "Sin descripción",
                                                    ) ?>
                </div>

                <div class="servicios">
                    <h2>Servicios que ofrece</h2>
                    <?php if (empty($servicios)): ?>
                        <p>No hay servicios asignados</p>
                    <?php else: ?>
                        <?php foreach ($servicios as $servicio): ?>
                            <span class="servicio-tag"><?= htmlspecialchars(
                                                            $servicio->getNombre(),
                                                        ) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </body>

        </html>
    <?php return ob_get_clean();
    }

    public function horarios(int $id): string
    {
        $horarios = $this->especialistaService->getHorariosSemanalEspecialista(
            $id,
        );
        $dias = [
            "Domingo",
            "Lunes",
            "Martes",
            "Miércoles",
            "Jueves",
            "Viernes",
            "Sábado",
        ];

        ob_start();
    ?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <title>Horarios - Especialista #<?= $id ?></title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    padding: 20px;
                }

                .container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 15px;
                    padding: 40px;
                }

                h1 {
                    color: #667eea;
                    margin-bottom: 20px;
                }

                .back-link {
                    display: inline-block;
                    margin-bottom: 20px;
                    color: #667eea;
                    text-decoration: none;
                }

                .dia {
                    margin: 20px 0;
                    padding: 15px;
                    background: #f8f9fa;
                    border-radius: 8px;
                }

                .dia h3 {
                    color: #333;
                    margin-bottom: 10px;
                }

                .horario {
                    display: inline-block;
                    background: #667eea;
                    color: white;
                    padding: 8px 16px;
                    border-radius: 20px;
                    margin: 5px;
                }
            </style>
        </head>

        <body>
            <div class="container">
                <a href="/especialistas/<?= $id ?>" class="back-link">← Volver</a>
                <h1>📅 Horarios del Especialista</h1>

                <?php if (empty($horarios)): ?>
                    <p>No hay horarios configurados</p>
                <?php else: ?>
                    <?php for ($i = 0; $i < 7; $i++): ?>
                        <?php if (isset($horarios[$i])): ?>
                            <div class="dia">
                                <h3><?= $dias[$i] ?></h3>
                                <?php foreach ($horarios[$i] as $horario): ?>
                                    <span class="horario">
                                        <?= $horario->getHoraInicio() ?> - <?= $horario->getHoraFin() ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                <?php endif; ?>
            </div>
        </body>

        </html>
<?php return ob_get_clean();
    }
}
