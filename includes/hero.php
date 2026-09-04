<?php /* includes/hero.php */ ?>
<!-- ============================================= -->
<!-- HERO SECTION                                  -->
<!-- ============================================= -->
<section class="hero" id="hero">
    <!-- Slideshow con efecto Ken Burns -->
    <div class="hero__slideshow">
        <div class="hero__slide hero__slide--1 hero__slide--active hero__slide--init">
            <div class="hero__slide-img" style="background-image: url('img/salud.jpg')"></div>
        </div>
        <div class="hero__slide hero__slide--2">
            <div class="hero__slide-img" style="background-image: url('img/student.jpg')"></div>
        </div>
        <div class="hero__slide hero__slide--3">
            <div class="hero__slide-img" style="background-image: url('img/student 2.jpg')"></div>
        </div>
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

        <!-- Círculo de video con texto giratorio -->
        <div class="hero__media">
            <div class="hero__video-wrapper">
                <div class="hero__circular-text" id="hero-circular-text">
                    <svg viewBox="0 0 160 160" class="hero__circular-svg">
                        <defs>
                            <path id="circlePath" d="M 80,80 m -55,0 a 55,55 0 1,1 110,0 a 55,55 0 1,1 -110,0" />
                        </defs>
                        <text>
                            <textPath href="#circlePath" class="hero__circular-text-path" textLength="345" lengthAdjust="spacing">
                                <?= content_get('hero', 'circular_text', 'ITB INSTITUTO UNIVERSITARIO • EST. 1995 • ITB INSTITUTO UNIVERSITARIO • EST. 1995 •') ?>
                            </textPath>
                        </text>
                    </svg>
                </div>
                <div class="hero__video-card">
                    <button type="button" class="hero__play-btn js-video-modal-trigger"
                        id="hero-play-btn" aria-label="Reproducir video institucional" data-video-url="<?= content_raw('hero', 'video_url', 'https://www.youtube.com/embed/eTgzLxWGgS4?autoplay=1') ?>">
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