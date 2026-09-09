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
    <title>Sobre Nosotros — <?= content_get('ajustes', 'site_name', 'ITB - Instituto Superior Tecnológico Bolivariano de Tecnología') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;0,800;0,900;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jarallax/2.1.4/jarallax.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/sobre-nosotros.css">
</head>

<body class="page-sobre-nosotros">

    <?php include 'includes/header.php'; ?>

    <main>
        <?php include 'includes/sobre-hero.php'; ?>
        <?php include 'includes/sobre-intro.php'; ?>
        <?php include 'includes/sobre-mision-vision.php'; ?>
        <?php include 'includes/sobre-valores.php'; ?>
        <?php include 'includes/autoridades.php'; ?>
        <?php include 'includes/sobre-cogobierno.php'; ?>
        <?php
        $alianzas_solo_carrusel = true;
        include 'includes/alianzas.php';
        ?>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jarallax/2.1.4/jarallax.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/countup.js@2.8.0/dist/countUp.umd.js"></script>
    <script src="js/main.js"></script>
    <script src="js/sobre-nosotros.js"></script>

</body>

</html>
