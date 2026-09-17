<?php
session_start();
require_once 'includes/content_helper.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= content_get('ajustes', 'description', 'Instituto Superior Tecnológico Bolivariano de Tecnología - Educación Superior de Excelencia.') ?>">
    <meta name="keywords" content="<?= content_get('ajustes', 'keywords', 'educación, instituto, guayaquil, carreras, tecnología, bolivariano, itb') ?>">
    <title>Oferta Académica — <?= content_get('ajustes', 'site_name', 'ITB - Instituto Superior Tecnológico Bolivariano de Tecnología') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;0,800;0,900;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jarallax/2.1.4/jarallax.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/oferta-academica.css?v=<?= filemtime('css/oferta-academica.css') ?>">
    <link rel="stylesheet" href="css/oferta-programas.css?v=<?= filemtime('css/oferta-programas.css') ?>">
    <link rel="stylesheet" href="css/pagina-interna.css?v=<?= filemtime('css/pagina-interna.css') ?>">
    <?php /* PENDIENTE: esta hoja pesa 12 KB y se descarga en cada visita, pero
             ahora mismo no la usa nadie: includes/oferta-catalogo.php y
             js/oferta-catalogo.js existen y no se incluyen en ninguna parte.
             Es una segunda versión del buscador, en marcha. Cuando se decida,
             o se engancha el componente o se quitan los tres archivos. */ ?>
    <link rel="stylesheet" href="css/oferta-catalogo.css">
</head>

<body class="page-oferta-academica">

    <?php include 'includes/header.php'; ?>

    <main>
        <?php 
        $hero_key = 'oferta_hero';
        include 'includes/sobre-hero.php'; 
        ?>
        
        <?php include 'includes/oferta-intro.php'; ?>
        <?php include 'includes/oferta-programas.php'; ?>

        <?php
        // Mismo carrusel de fotos de Alianzas que Inicio, reutilizado tal
        // cual como en Sobre Nosotros: $alianzas_solo_carrusel oculta el
        // título/botón y la fila de logos, y deja solo el carril de fotos.
        $alianzas_solo_carrusel = true;
        include 'includes/alianzas.php';
        ?>

    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jarallax/2.1.4/jarallax.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/pagina-interna.js"></script>
    <script src="js/oferta-programas.js?v=<?= filemtime('js/oferta-programas.js') ?>"></script>

</body>

</html>
