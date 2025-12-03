<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>500 - Error del servidor</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            max-width: 800px;
        }
        h1 {
            color: #8b5cf6;
            font-size: 4em;
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
            font-family: monospace;
            font-size: 0.9em;
            overflow-x: auto;
        }
        a {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>500</h1>
        <h2>Error del servidor</h2>
        <p>Ha ocurrido un error al procesar tu solicitud.</p>
        <div class="error">
            <strong>Error:</strong> <?= htmlspecialchars(
                $e->getMessage(),
            ) ?><br>
            <strong>Archivo:</strong> <?= htmlspecialchars($e->getFile()) ?><br>
            <strong>Línea:</strong> <?= htmlspecialchars($e->getLine()) ?>
        </div>
        <a href="/">← Volver al inicio</a>
    </div>
</body>
</html>
