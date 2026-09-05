<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('trayectoria')) return; ?>
<?php /* includes/trayectoria.php */ ?>
<!-- ============================================= -->
<!-- TRAYECTORIA Y COMPROMISO EDUCATIVO            -->
<!-- ============================================= -->
<section class="trayectoria" id="trayectoria">
    <div class="trayectoria__container">
        <!-- Lado izquierdo -->
        <div class="trayectoria__content">
            <span class="section-tag"><?= content_get('trayectoria', 'etiqueta_superior', 'NUESTRA TRAYECTORIA') ?></span>
            <h2 class="trayectoria__title">
                <?= content_get('trayectoria', 'titulo', 'Trayectoria y Compromiso con la Educación') ?>
            </h2>
            <p class="trayectoria__description">
                <?= content_get('trayectoria', 'descripcion', 'Desde 1995, el Instituto Superior Tecnológico Bolivariano de Tecnología ha formado profesionales con una educación integral basada en valores, innovación y excelencia académica. Nuestro compromiso es transformar vidas a través del conocimiento.') ?>
            </p>

            <!-- Perfil del Canciller -->
            <div class="trayectoria__profile">
                <div class="trayectoria__profile-img">
                    <img src="<?= content_raw('trayectoria', 'canciller_foto', 'img/canciller.jpg') ?>" alt="<?= content_get('trayectoria', 'canciller_nombre', 'PhD. Roberto Tolozano Benites') ?>">
                </div>
                <div class="trayectoria__profile-info">
                    <span class="trayectoria__profile-name"><?= content_get('trayectoria', 'canciller_nombre', 'PhD. Roberto Tolozano Benites') ?></span>
                    <span class="trayectoria__profile-role"><?= content_get('trayectoria', 'canciller_cargo', 'Canciller del ITB') ?></span>
                </div>
            </div>

            <a href="#" class="btn btn--outline-dark" id="btn-historia">
                <?= content_get('trayectoria', 'btn_historia', 'Nuestra Historia') ?> <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <!-- Lado derecho: Estadísticas -->
        <div class="trayectoria__stats">
            <div class="trayectoria__stat-card">
                <div class="trayectoria__stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <span class="trayectoria__stat-number"><?= content_get('trayectoria', 'stat_anios', '29+') ?></span>
                <span class="trayectoria__stat-label"><?= str_replace('\n', '<br>', content_get('trayectoria', 'stat_anios_label', 'Años de<br>Experiencia')) ?></span>
            </div>
            <div class="trayectoria__stat-card">
                <div class="trayectoria__stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <span class="trayectoria__stat-number"><?= content_get('trayectoria', 'stat_graduados', '+17,000') ?></span>
                <span class="trayectoria__stat-label"><?= str_replace('\n', '<br>', content_get('trayectoria', 'stat_graduados_label', 'Estudiantes<br>Graduados')) ?></span>
            </div>
            <div class="trayectoria__stat-card">
                <div class="trayectoria__stat-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <span class="trayectoria__stat-number"><?= content_get('trayectoria', 'stat_carreras', '+35') ?></span>
                <span class="trayectoria__stat-label"><?= str_replace('\n', '<br>', content_get('trayectoria', 'stat_carreras_label', 'Carreras<br>Disponibles')) ?></span>
            </div>
        </div>
    </div>
</section>
