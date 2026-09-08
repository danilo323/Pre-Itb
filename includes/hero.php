<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('hero')) return; ?>
<?php
// includes/hero.php

// 1. Fotos de la portada, con su marca de "imagen fija" (Inicio → Hero).
$imagenes = content_raw('hero', 'imagenes_fondo', [
    ['archivo' => 'img/hero_1.jpeg'],
    ['archivo' => 'img/hero_2.jpg'],
    ['archivo' => 'img/hero_3.jpg']
]);

// field_bool_parse() guarda booleanos de verdad, pero los datos de ejemplo y
// los guardados a mano usan '1'/'0' como texto. Se aceptan ambos (mismo
// criterio que en includes/autoridades.php e includes/servicios.php).
$hero_encendido = function ($v) {
    return $v === true || $v === 1 || $v === '1';
};

$slides = [];
if (is_array($imagenes)) {
    foreach ($imagenes as $img) {
        if (!empty($img['archivo'])) {
            $slides[] = [
                'archivo' => $img['archivo'],
                'estatica' => $hero_encendido($img['estatica'] ?? false),
            ];
        }
    }
}
// Fallback por si borraron todas
if (empty($slides)) {
    $slides[] = ['archivo' => 'img/hero_1.jpeg', 'estatica' => false];
}

// 2. Elegir el modo de la portada.
//
// En el panel, "Animaciones y efectos" y el "Imagen fija" de cada foto forman
// un grupo exclusivo: siempre hay exactamente uno encendido. Aquí no se confía
// en que eso se cumpla —los datos pueden venir de una versión anterior, o
// editados a mano—, así que se vuelve a decidir a partir de lo que haya:
//
//   - Si alguna foto está marcada como fija, gana ella: portada quieta con esa
//     foto y nada más (las otras ni se descargan).
//   - Si no hay ninguna marcada, manda el interruptor de animaciones, que por
//     defecto está encendido para no cambiarle el sitio a nadie.
//   - Con una sola foto no hay nada que rotar, así que la portada se comporta
//     como fija aunque las animaciones estén encendidas.
$animaciones = $hero_encendido(content_raw('hero', 'animaciones', true));

$indice_fija = null;
foreach ($slides as $i => $s) {
    if ($s['estatica']) { $indice_fija = $i; break; }
}

$hero_estatico = ($indice_fija !== null) || !$animaciones || count($slides) === 1;

if ($hero_estatico) {
    // Solo se pinta la foto elegida. Si el interruptor está apagado pero nadie
    // marcó cuál, se usa la primera.
    $slides = [$slides[$indice_fija ?? 0]];
}

// 2. Formatear la URL de YouTube a modo "embed" (helper compartido en content_helper.php)
$raw_video_url = content_raw('hero', 'video_url', 'https://youtu.be/eTgzLxWGgS4');
$embed_url = youtube_embed_url($raw_video_url);
?>
<!-- ============================================= -->
<!-- HERO SECTION                                  -->
<!-- ============================================= -->
<section class="hero" id="hero">
    <!-- Slideshow con efecto Ken Burns (o foto fija, si solo hay una) -->
    <div class="hero__slideshow<?= $hero_estatico ? ' hero__slideshow--estatica' : '' ?>">
        <?php foreach ($slides as $index => $slide): ?>
            <?php
                $active_class = $index === 0 ? 'hero__slide--active hero__slide--init' : '';
            ?>
            <div class="hero__slide <?= $active_class ?>">
                <div class="hero__slide-img" style="background-image: url('<?= htmlspecialchars($slide['archivo'], ENT_QUOTES, 'UTF-8') ?>')"></div>
            </div>
        <?php endforeach; ?>
        <div class="hero__overlay"></div>

        <?php if (count($slides) > 1): ?>
            <!-- Contador del carrusel. Va aqui dentro del slideshow, no en el
                 contenido, para que quede sobre la foto y no empuje al texto. -->
            <div class="hero__contador carrusel-contador carrusel-contador--sobre-foto js-contador"
                 data-total="<?= count($slides) ?>"
                 aria-live="polite" aria-atomic="true">
                <span class="carrusel-contador__actual">1</span>/<span><?= count($slides) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <div class="hero__container">
        <div class="hero__content">
            <span class="hero__subtitle"><?= content_get('hero', 'subtitulo', 'Educación Superior de Excelencia') ?></span>

            <h1 class="hero__title">
                <?= str_replace('\n', '<br>', content_get('hero', 'titulo', 'Construye tu Futuro, Lidera el Mañana')) ?>
            </h1>

            <p class="hero__description">
                <?= content_get('hero', 'descripcion', 'Bienvenida al ITB. Formamos profesionales de alto nivel con educación práctica, tecnología e innovación para ayudarte a alcanzar el éxito laboral.') ?>
            </p>

            <div class="hero__actions">
                <a href="#" class="btn btn--solid" id="hero-cta">
                    <?= content_get('hero', 'cta_texto', 'Explorar Programas') ?>
                    <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                </a>

                <div class="hero__stats">
                    <div class="hero__stat">
                        <span class="hero__stat-number"><?= content_get('hero', 'stat_numero', '+20K Estudiantes Graduados') ?></span>
                        <div class="hero__stat-rating">
                            <div class="hero__stars">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                            <span class="hero__rating-number"><?= content_get('hero', 'rating', '4.9') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Círculo de video -->
        <div class="hero__media">
            <div class="hero__video-wrapper">
                <div class="hero__circular-text" id="hero-circular-text">
                    <svg viewBox="0 0 160 160" class="hero__circular-svg">
                        <defs>
                            <path id="circlePath" d="M 80,80 m -55,0 a 55,55 0 1,1 110,0 a 55,55 0 1,1 -110,0" />
                        </defs>
                        <text>
                            <textPath href="#circlePath" class="hero__circular-text-path" textLength="345" lengthAdjust="spacing">
                                <?= content_circular('hero', 'circular_text', "EST. 1995\nITB INSTITUTO UNIVERSITARIO") ?>
                            </textPath>
                        </text>
                    </svg>
                </div>
                <div class="hero__video-card">
                    <button type="button" class="hero__play-btn js-video-modal-trigger"
                        id="hero-play-btn" aria-label="Reproducir video institucional" data-video-url="<?= htmlspecialchars($embed_url, ENT_QUOTES, 'UTF-8') ?>">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para el Video -->
    <div class="hero__video-modal" id="video-modal">
        <div class="hero__video-modal-overlay" id="video-modal-overlay"></div>
        <div class="hero__video-modal-content">
            <button type="button" class="hero__video-modal-close" id="video-modal-close" aria-label="Cerrar video">
                <i class="fas fa-times"></i>
            </button>
            <div class="hero__video-modal-iframe-wrapper">
                <iframe id="video-modal-iframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</section>