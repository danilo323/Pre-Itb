<?php /* includes/hero.php */ ?>
<!-- ============================================= -->
<!-- HERO SECTION                                  -->
<!-- ============================================= -->
<section class="hero" id="hero">
    <div class="hero__bg">
        <img src="<?= content_raw('hero', 'imagen_bg', 'img/hero-bg.jpg') ?>" alt="Estudiantes ITB" class="hero__bg-img">
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
                    <svg viewBox="0 0 300 300" class="hero__circular-svg">
                        <defs>
                            <path id="circlePath" d="M 150, 150 m -120, 0 a 120,120 0 1,1 240,0 a 120,120 0 1,1 -240,0" />
                        </defs>
                        <text>
                            <textPath href="#circlePath" class="hero__circular-text-path">
                                <?= content_get('hero', 'circular_text', 'ITB INSTITUTO UNIVERSITARIO • EST. 1995 • ITB INSTITUTO UNIVERSITARIO • EST. 1995 •') ?>
                            </textPath>
                        </text>
                    </svg>
                </div>
                <div class="hero__video-card">
                    <img src="<?= content_raw('hero', 'imagen_video_thumb', 'img/hero-video-thumb.jpg') ?>" alt="Video institucional ITB" class="hero__video-thumb">
                    <a href="<?= content_raw('hero', 'video_url', 'https://youtu.be/eTgzLxWGgS4?si=itTyNJd-Es1E4f-B') ?>" target="_blank" class="hero__play-btn" id="hero-play-btn" aria-label="Reproducir video institucional">
                        <i class="fas fa-play"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
