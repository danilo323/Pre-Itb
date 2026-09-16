<?php
// Heredamos la imagen de portada de la sección Sobre Nosotros
$imagenes = content_raw('sobre_hero', 'imagenes_fondo', [
    ['archivo' => 'img/hero_2.jpg', 'estatica' => true]
]);

$hero_estatico = true;
$archivo_imagen = 'img/hero_2.jpg';

// Buscar la imagen estática del hero principal
if (is_array($imagenes)) {
    foreach ($imagenes as $img) {
        if (!empty($img['estatica']) && ($img['estatica'] === '1' || $img['estatica'] === true)) {
            $archivo_imagen = $img['archivo'];
            break;
        }
        if (!empty($img['archivo'])) {
            $archivo_imagen = $img['archivo'];
        }
    }
}
?>
<!-- ============================================= -->
<!-- HERO NOTICIAS                               -->
<!-- ============================================= -->
<section class="sobre-hero" id="noticias-hero">
    <!-- Fondo oscuro difuminado con imagen heredada -->
    <div class="hero__slideshow hero__slideshow--estatica">
        <div class="hero__slide hero__slide--active hero__slide--init">
            <div class="hero__slide-img" style="background-image: url('<?= htmlspecialchars($archivo_imagen, ENT_QUOTES, 'UTF-8') ?>')"></div>
        </div>
        <div class="hero__overlay"></div>
    </div>

    <div class="sobre-hero__container">
        <h1 class="sobre-hero__title">
            Conoce las noticias de ITB
        </h1>
        <nav class="sobre-hero__ruta" aria-label="Ruta de navegación">
            <a href="index.php" class="sobre-hero__ruta-link">Inicio</a>
            <i class="fas fa-chevron-right sobre-hero__ruta-sep" aria-hidden="true"></i>
            <span class="sobre-hero__ruta-actual" aria-current="page">Noticias</span>
        </nav>
    </div>
</section>
