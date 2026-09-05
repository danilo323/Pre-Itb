<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('hero')) return; ?>
<?php
// includes/hero.php

// 1. Extraer URLs de imágenes para el slideshow
$img1 = content_get('hero', 'imagen_1', 'img/salud.jpg');
$img2 = content_get('hero', 'imagen_2', 'img/student.jpg');
$img3 = content_get('hero', 'imagen_3', 'img/student 2.jpg');
$slides = [];
if (!empty($img1)) $slides[] = $img1;
if (!empty($img2)) $slides[] = $img2;
if (!empty($img3)) $slides[] = $img3;
// Fallback por si borraron todas
if (empty($slides)) {
    $slides[] = 'img/salud.jpg';
}

// 2. Formatear la URL de YouTube a modo "embed"
$raw_video_url = content_raw('hero', 'video_url', 'https://youtu.be/eTgzLxWGgS4');
$embed_url = $raw_video_url;
// Regex para encontrar el ID del video de youtube
if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $raw_video_url, $matches)) {
    $embed_url = 'https://www.youtube.com/embed/' . $matches[1] . '?autoplay=1';
}
?>
<!-- ============================================= -->
<!-- HERO SECTION                                  -->
<!-- ============================================= -->
<section class="hero" id="hero">
    <!-- Slideshow con efecto Ken Burns -->
    <div class="hero__slideshow">
        <?php foreach ($slides as $index => $img_path): ?>
            <?php 
                $class_num = $index + 1;
                $active_class = $index === 0 ? 'hero__slide--active hero__slide--init' : '';
            ?>
            <div class="hero__slide hero__slide--<?= $class_num ?> <?= $active_class ?>">
                <div class="hero__slide-img" style="background-image: url('<?= htmlspecialchars($img_path, ENT_QUOTES, 'UTF-8') ?>')"></div>
            </div>
        <?php endforeach; ?>
        <div class="hero__overlay"></div>
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
                <a href="#" class="hero__cta" id="hero-cta">
                    <?= content_get('hero', 'cta_texto', 'Explorar Programas') ?>
                    <span class="hero__cta-icon"><i class="fas fa-arrow-up-right-from-square"></i></span>
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
                                <?php 
                                    $circ = content_get('hero', 'circular_text', '• EST. 1995 • ITB INSTITUTO UNIVERSITARIO ');
                                    // Si por error se quedó guardado el texto doble en la sesión de prueba, lo corregimos a la fuerza:
                                    if (strpos($circ, 'EST. 1995 • ITB INSTITUTO UNIVERSITARIO • EST. 1995') !== false) {
                                        $circ = '• EST. 1995 • ITB INSTITUTO UNIVERSITARIO ';
                                    }
                                    echo $circ;
                                ?>
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