<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('trayectoria')) return; ?>
<?php /* includes/trayectoria.php */ ?>
<!-- ============================================= -->
<!-- TRAYECTORIA Y COMPROMISO EDUCATIVO            -->
<!-- ============================================= -->
<section class="trayectoria" id="trayectoria">
    <div class="trayectoria__container">
        <!-- Lado izquierdo: Contenido de Texto -->
        <div class="trayectoria__content">
            <span class="section-tag"><?= content_get('trayectoria', 'etiqueta_superior', 'Trayectoria y Compromiso Educativo') ?></span>
            <h2 class="trayectoria__title">
                <?= str_replace('\n', '<br>', content_get('trayectoria', 'titulo', "Formando Líderes\nPrácticos con Visión\ndel Futuro")) ?>
            </h2>
            <p class="trayectoria__description">
                <?= content_get('trayectoria', 'descripcion', 'Con casi 3 décadas de trayectoria, impulsamos una educación superior práctica, accesible e innovadora para formar profesionales listos para el mercado laboral.') ?>
            </p>

            <!-- Perfil del Canciller -->
            <div class="trayectoria__profile">
                <div class="trayectoria__profile-img">
                    <img src="<?= content_raw('trayectoria', 'canciller_foto', 'img/PHD_Roberto.jpg') ?>" alt="<?= content_get('trayectoria', 'canciller_nombre', 'PhD. Roberto Tolozano Benites') ?>">
                </div>
                <div class="trayectoria__profile-info">
                    <span class="trayectoria__profile-role"><?= content_get('trayectoria', 'canciller_cargo', 'Canciller') ?></span>
                    <span class="trayectoria__profile-name"><?= content_get('trayectoria', 'canciller_nombre', 'PhD. Roberto Tolozano Benites') ?></span>
                </div>
            </div>

            <a href="#" class="btn btn--solid" id="btn-historia">
                <?= content_get('trayectoria', 'btn_historia', 'Nuestra Historia') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <!-- Centro: Imagen con Jarallax -->
        <div class="trayectoria__image" data-jarallax data-speed="0.5" data-img-position="top">
            <img src="img/estudiantes1.png" alt="Estudiantes ITB en el campus" class="jarallax-img">
        </div>

        <!-- Lado derecho: Estadísticas -->
        <div class="trayectoria__stats">
            <div class="trayectoria__stat-card">
                <div class="trayectoria__stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="trayectoria__stat-text">
                    <span class="trayectoria__stat-number" data-count="<?= content_get('trayectoria', 'stat_anios', '29') ?>" data-suffix="+"><?= content_get('trayectoria', 'stat_anios', '29') ?>+</span>
                    <span class="trayectoria__stat-label"><?= str_replace('\n', '<br>', content_get('trayectoria', 'stat_anios_label', "Años transformando vidas\ny formando profesionales.")) ?></span>
                </div>
            </div>
            <div class="trayectoria__stat-card">
                <div class="trayectoria__stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="trayectoria__stat-text">
                    <span class="trayectoria__stat-number" data-count="<?= content_get('trayectoria', 'stat_graduados', '17000') ?>" data-format="thousands" data-prefix="+" data-suffix="">+<?= number_format((int)content_get('trayectoria', 'stat_graduados', '17000')) ?></span>
                    <span class="trayectoria__stat-label"><?= str_replace('\n', '<br>', content_get('trayectoria', 'stat_graduados_label', "Estudiantes formándose\ncon metodologías activas.")) ?></span>
                </div>
            </div>
            <div class="trayectoria__stat-card">
                <div class="trayectoria__stat-icon">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <div class="trayectoria__stat-text">
                    <span class="trayectoria__stat-number" data-count="<?= content_get('trayectoria', 'stat_carreras', '35') ?>" data-prefix="+" data-suffix="">+<?= content_get('trayectoria', 'stat_carreras', '35') ?></span>
                    <span class="trayectoria__stat-label"><?= str_replace('\n', '<br>', content_get('trayectoria', 'stat_carreras_label', "Carreras técnicas y\ntecnológicas disponibles.")) ?></span>
                </div>
            </div>
        </div>
    </div>
</section>