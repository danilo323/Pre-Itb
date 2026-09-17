<?php
// includes/transparencia-hero.php
$hero_imgs = content_raw('hero', 'imagenes_fondo', [['archivo' => 'img/hero_1.jpeg']]);
$page_hero_img = htmlspecialchars($hero_imgs[0]['archivo'] ?? 'img/hero_1.jpeg', ENT_QUOTES, 'UTF-8');
?>
<section class="page-hero" style="background-image: linear-gradient(105deg, rgba(15, 34, 67, 0.92) 0%, rgba(26, 59, 112, 0.82) 55%, rgba(26, 59, 112, 0.55) 100%), url('<?= $page_hero_img ?>')">
    <div class="page-hero__container">
        <h1 class="page-hero__title">Cumplimiento Legal y Acceso a la Información</h1>
        <nav class="page-hero__breadcrumb">
            <a href="index.php">Inicio</a>
            <span>&gt;</span>
            <span>Instituto</span>
            <span>&gt;</span>
            <span class="is-active">Transparencia / Leyes</span>
        </nav>
    </div>
</section>
