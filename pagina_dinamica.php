<?php
// pagina_dinamica.php
// Plantilla Maestra Genérica para Renderizar Páginas Personalizadas Creadas desde el Panel Admin

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/includes/content_helper.php';
require_once __DIR__ . '/admin/storage.php';
// pagina_custom_base(): las secciones heredadas de otra página creada se
// guardan como 'pg_xxxx:valores', y aquí hace falta solo la parte 'valores'
// para saber qué componente incluir.
require_once __DIR__ . '/admin/pagina_custom_helpers.php';

$all_data = storage_load();
$paginas_creadas = $all_data['_paginas_creadas'] ?? [];

$target_page = null;

// 1. Verificar si viene inyectado desde router.php o vía parámetro GET
if (isset($GLOBALS['CURRENT_DYNAMIC_PAGE'])) {
    $target_page = $GLOBALS['CURRENT_DYNAMIC_PAGE'];
} else {
    $requested_route = trim($_GET['route'] ?? $_GET['p'] ?? '', '/');
    $clean_slug = preg_replace('/\.php$/', '', $requested_route);

    if (!empty($clean_slug)) {
        foreach ($paginas_creadas as $p) {
            if (($p['slug'] ?? '') === $clean_slug) {
                $target_page = $p;
                break;
            }
        }
    }
}

// 2. Si no se encontró la página personalizada, lanzar 404
if (!$target_page) {
    http_response_code(404);
    require __DIR__ . '/admin/404.php';
    exit;
}

// También se usa cuando la página se abre por ?route= o ?p (sin router.php).
// content_helper.php la lee para aplicar la copia de contenido de esta página.
$GLOBALS['CURRENT_DYNAMIC_PAGE'] = $target_page;

$nombre_pagina = htmlspecialchars($target_page['nombre'] ?? 'Página Institucional', ENT_QUOTES, 'UTF-8');
$secciones     = $target_page['secciones'] ?? [];

// Mapeo de claves de sección a sus respectivos componentes PHP
$mapa_secciones = [
    'hero'          => __DIR__ . '/includes/hero.php',
    'sobre_hero'    => __DIR__ . '/includes/sobre-hero.php',
    'presentacion'  => __DIR__ . '/includes/sobre-intro.php',
    'mision_vision' => __DIR__ . '/includes/sobre-mision-vision.php',
    'valores'       => __DIR__ . '/includes/sobre-valores.php',
    'autoridades'   => __DIR__ . '/includes/autoridades.php',
    'cogobierno'    => __DIR__ . '/includes/sobre-cogobierno.php',
    'himno'         => __DIR__ . '/includes/nosotros_himno.php',
    'areas'         => __DIR__ . '/includes/areas.php',
    'programas'     => __DIR__ . '/includes/programas.php',
    'servicios'     => __DIR__ . '/includes/servicios.php',
    'noticias'      => __DIR__ . '/includes/noticias.php',
    'transparencia' => __DIR__ . '/includes/transparencia.php',
    'admision'      => __DIR__ . '/includes/admision.php',
    'alianzas'      => __DIR__ . '/includes/alianzas.php'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= content_get('ajustes', 'description', 'Instituto Superior Tecnológico Bolivariano de Tecnología - Educación Superior de Excelencia.') ?>">
    <meta name="keywords" content="<?= content_get('ajustes', 'keywords', 'educación, instituto, guayaquil, carreras, tecnología, bolivariano, itb') ?>">
    <title><?= $nombre_pagina ?> — <?= content_get('ajustes', 'site_name', 'ITB - Instituto Superior Tecnológico Bolivariano de Tecnología') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;0,800;0,900;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jarallax/2.1.4/jarallax.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/sobre-nosotros.css">
    <link rel="stylesheet" href="css/pagina-interna.css">
</head>
<!-- page-sobre-nosotros activa los estilos modulares de las secciones heredadas. -->
<body class="page-dinamica page-sobre-nosotros">

    <?php include __DIR__ . '/includes/header.php'; ?>

    <main>
        <?php
        if (empty($secciones)) {
            echo '<div style="padding: 100px 20px; text-align: center; color: #6B7280;"><h2>Página en construcción</h2><p>Esta página aún no contiene secciones asignadas desde el panel de administración.</p></div>';
        } else {
            foreach ($secciones as $sec_key) {
                $sec_base = pagina_custom_base((string)$sec_key);
                if (isset($mapa_secciones[$sec_base]) && file_exists($mapa_secciones[$sec_base])) {
                    if ($sec_base === 'alianzas') {
                        $alianzas_solo_carrusel = true;
                    }
                    include $mapa_secciones[$sec_base];
                }
            }
        }
        ?>
    </main>

    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jarallax/2.1.4/jarallax.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/countup.js@2.8.0/dist/countUp.umd.js"></script>
    <script src="js/main.js"></script>
    <script src="js/sobre-nosotros.js"></script>
    <script src="js/pagina-interna.js"></script>

</body>
</html>
