<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('sobre_hero')) return; ?>
<?php
// includes/sobre-hero.php
//
// Portada propia de la página Sobre Nosotros. No es la del Inicio: aquí no hay
// botón, ni estadísticas con estrellas, ni texto giratorio, ni botón de video.
// Solo la foto de fondo, el título de la página y la ruta de navegación
// ("Inicio > Sobre Nosotros").
//
// Se edita en el panel en Páginas → Sobre Nosotros → HERO (PORTADA), en la
// sección 'sobre_hero', sin tocar la del Inicio.
//
// Del hero del Inicio se reaprovecha SOLO el fondo: las clases .hero__slideshow
// / .hero__slide son las que ya animan js/main.js y viste css/hero.css, así que
// el carrusel y el efecto de acercamiento funcionan igual sin duplicar código.
// Lo de encima (título y ruta) es propio y vive en css/sobre-hero.css.

// 1. Fotos de fondo, con su marca de "imagen fija" (mismo criterio que
//    includes/hero.php: se aceptan booleanos y los '1'/'0' de texto).
$imagenes = content_raw('sobre_hero', 'imagenes_fondo', [
    ['archivo' => 'img/hero_2.jpg'],
    ['archivo' => 'img/hero_3.jpg'],
]);

$sobre_hero_encendido = function ($v) {
    return $v === true || $v === 1 || $v === '1';
};

$slides = [];
if (is_array($imagenes)) {
    foreach ($imagenes as $img) {
        if (!empty($img['archivo'])) {
            $slides[] = [
                'archivo' => $img['archivo'],
                'estatica' => $sobre_hero_encendido($img['estatica'] ?? false),
            ];
        }
    }
}
// Fallback por si borraron todas
if (empty($slides)) {
    $slides[] = ['archivo' => 'img/hero_2.jpg', 'estatica' => false];
}

// 2. Elegir el modo de la portada (misma regla que el hero del Inicio):
//    manda la foto marcada como fija; si no hay ninguna, manda el interruptor
//    de animaciones; y con una sola foto no hay nada que rotar.
$animaciones = $sobre_hero_encendido(content_raw('sobre_hero', 'animaciones', true));

$indice_fija = null;
foreach ($slides as $i => $s) {
    if ($s['estatica']) { $indice_fija = $i; break; }
}

$hero_estatico = ($indice_fija !== null) || !$animaciones || count($slides) === 1;

if ($hero_estatico) {
    $slides = [$slides[$indice_fija ?? 0]];
}
?>
<!-- ============================================= -->
<!-- SOBRE NOSOTROS — PORTADA (BANNER)             -->
<!-- ============================================= -->
<section class="sobre-hero" id="sobre-hero">
    <!-- Fondo: mismo motor de slideshow del hero del Inicio -->
    <div class="hero__slideshow<?= $hero_estatico ? ' hero__slideshow--estatica' : '' ?>">
        <?php foreach ($slides as $index => $slide): ?>
            <div class="hero__slide <?= $index === 0 ? 'hero__slide--active hero__slide--init' : '' ?>">
                <div class="hero__slide-img" style="background-image: url('<?= htmlspecialchars($slide['archivo'], ENT_QUOTES, 'UTF-8') ?>')"></div>
            </div>
        <?php endforeach; ?>
        <div class="hero__overlay"></div>

        <?php if (count($slides) > 1): ?>
            <div class="hero__contador carrusel-contador carrusel-contador--sobre-foto js-contador"
                 data-total="<?= count($slides) ?>"
                 aria-live="polite" aria-atomic="true">
                <span class="carrusel-contador__actual">1</span>/<span><?= count($slides) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <div class="sobre-hero__container">
        <h1 class="sobre-hero__title">
            <?= content_get('sobre_hero', 'titulo', 'Sobre nosotros') ?>
        </h1>

        <nav class="sobre-hero__ruta" aria-label="Ruta de navegación">
            <a href="index.php" class="sobre-hero__ruta-link">
                <?= content_get('sobre_hero', 'ruta_inicio', 'Inicio') ?>
            </a>
            <i class="fas fa-chevron-right sobre-hero__ruta-sep" aria-hidden="true"></i>
            <span class="sobre-hero__ruta-actual" aria-current="page">
                <?= content_get('sobre_hero', 'ruta_actual', 'Sobre Nosotros') ?>
            </span>
        </nav>
    </div>
</section>
