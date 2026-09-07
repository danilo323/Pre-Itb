<?php
// admin/404.php
require_once __DIR__ . '/base_url.php';
$SB = site_base();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #ffffff;
            font-family: 'Inter', sans-serif;
            text-align: center;
        }

        .error-code {
            font-family: 'Playfair Display', serif;
            font-size: 15rem;
            font-weight: 700;
            color: #E8712A; /* Naranja ITB */
            margin: 0;
            line-height: 1;
        }

        .error-title {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 700;
            color: #000000;
            margin: 10px 0 20px 0;
        }

        .error-desc {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 40px;
        }

        .btn-back {
            display: inline-block;
            background-color: #E8712A;
            color: #ffffff;
            font-weight: 600;
            font-size: 1rem;
            padding: 15px 40px;
            border-radius: 12px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #D4631F;
        }
    </style>
</head>
<body>
    <h1 class="error-code">404</h1>
    <h2 class="error-title">Page Not Found</h2>
    <p class="error-desc">La página que buscas no existe o ha sido movida.</p>
    <a href="<?= htmlspecialchars($SB, ENT_QUOTES, 'UTF-8') ?>" class="btn-back">Back To Homepage</a>
</body>
</html>
