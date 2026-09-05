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
            <img src="<?= content_raw('trayectoria', 'imagen_central', 'img/estudiantes1.png') ?>" alt="Estudiantes ITB en el campus" class="jarallax-img">
        </div>

        <!-- Lado derecho: Estadísticas -->
        <?php
        // Íconos por posición (0, 1, 2) — se mantienen fijos del diseño original
        $stat_icons = ['fa-user-graduate', 'fa-users', 'fa-laptop-code'];
        $stats = content_raw('trayectoria', 'estadisticas', [
            ['numero' => '29+', 'texto' => 'Años de Experiencia'],
            [' numero' => '+17,000', 'texto' => 'Estudiantes Graduados'],
            ['numero' => '+35', 'texto' => 'Carreras Disponibles'],
        ]);
        ?>
        <div class="trayectoria__stats">
            <?php foreach ((array)$stats as $i => $stat): ?>
            <div class="trayectoria__stat-card">
                <div class="trayectoria__stat-icon">
                    <i class="fas <?= $stat_icons[$i] ?? 'fa-star' ?>"></i>
                </div>
                <div class="trayectoria__stat-text">
                    <span class="trayectoria__stat-number"><?= htmlspecialchars($stat['numero'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="trayectoria__stat-label"><?= htmlspecialchars($stat['texto'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>