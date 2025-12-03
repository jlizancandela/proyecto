<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>405 - Método no permitido</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
            max-width: 600px;
        }
        h1 {
            color: #f59e0b;
            font-size: 4em;
            margin-bottom: 20px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
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
        <h1>405</h1>
        <h2>Método no permitido</h2>
        <p>El método HTTP <strong><?= htmlspecialchars(
            $requestMethod,
        ) ?></strong> no está permitido para esta ruta.</p>
        <a href="/">← Volver al inicio</a>
    </div>
</body>
</html>
