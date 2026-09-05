<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('trayectoria')) return; ?>
<?php /* includes/trayectoria.php */
$stat_anios_val = content_get('trayectoria', 'stat_anios', '29+');
preg_match('/^([^\d]*)(\d+[\d,.]*)(.*)$/', trim($stat_anios_val), $m1);
$p1 = !empty($m1[1]) ? $m1[1] : '';
$n1 = !empty($m1[2]) ? str_replace([',', '.'], '', $m1[2]) : '29';
$s1 = !empty($m1[3]) ? $m1[3] : '+';

$stat_grad_val = content_get('trayectoria', 'stat_graduados', '+17,000');
preg_match('/^([^\d]*)(\d+[\d,.]*)(.*)$/', trim($stat_grad_val), $m2);
$p2 = !empty($m2[1]) ? $m2[1] : '+';
$n2 = !empty($m2[2]) ? str_replace([',', '.'], '', $m2[2]) : '17000';
$s2 = !empty($m2[3]) ? $m2[3] : '';

$stat_carr_val = content_get('trayectoria', 'stat_carreras', '+35');
preg_match('/^([^\d]*)(\d+[\d,.]*)(.*)$/', trim($stat_carr_val), $m3);
$p3 = !empty($m3[1]) ? $m3[1] : '+';
$n3 = !empty($m3[2]) ? str_replace([',', '.'], '', $m3[2]) : '35';
$s3 = !empty($m3[3]) ? $m3[3] : '';
?>
<!-- ============================================= -->
<!-- TRAYECTORIA Y COMPROMISO EDUCATIVO            -->
<!-- ============================================= -->
<section class="trayectoria" id="trayectoria">
    <div class="trayectoria__container">
        <!-- Lado izquierdo: Contenido de Texto -->
        <div class="trayectoria__content">
            <span class="section-tag"><?= content_get('trayectoria', 'etiqueta_superior', 'Trayectoria y Compromiso Educativo') ?></span>
            <h2 class="trayectoria__title">
                <?= str_replace('\n', '<br>', content_get('trayectoria', 'titulo', "Formando Líderes<br>Prácticos con Visión<br>del Futuro")) ?>
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

            <a href="<?= content_get('trayectoria', 'btn_historia_link', '#') ?>" class="btn btn--solid" id="btn-historia">
                <?= content_get('trayectoria', 'btn_historia', 'Nuestra Historia') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <!-- Centro: Imagen con Jarallax -->
        <div class="trayectoria__image" data-jarallax data-speed="0.5" data-img-position="top">
            <img src="<?= content_raw('trayectoria', 'imagen_campus', 'img/estudiantes1.png') ?>" alt="Estudiantes ITB en el campus" class="jarallax-img">
        </div>

        <!-- Lado derecho: Estadísticas -->
        <div class="trayectoria__stats">
            <div class="trayectoria__stat-card">
                <div class="trayectoria__stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="trayectoria__stat-text">
                    <span class="trayectoria__stat-number" data-count="<?= htmlspecialchars($n1) ?>" data-prefix="<?= htmlspecialchars($p1) ?>" data-suffix="<?= htmlspecialchars($s1) ?>"><?= htmlspecialchars($stat_anios_val) ?></span>
                    <span class="trayectoria__stat-label"><?= str_replace('\n', '<br>', content_get('trayectoria', 'stat_anios_label', "Años transformando vidas<br>y formando profesionales.")) ?></span>
                </div>
            </div>
            <div class="trayectoria__stat-card">
                <div class="trayectoria__stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="trayectoria__stat-text">
                    <span class="trayectoria__stat-number" data-count="<?= htmlspecialchars($n2) ?>" data-format="thousands" data-prefix="<?= htmlspecialchars($p2) ?>" data-suffix="<?= htmlspecialchars($s2) ?>"><?= htmlspecialchars($stat_grad_val) ?></span>
                    <span class="trayectoria__stat-label"><?= str_replace('\n', '<br>', content_get('trayectoria', 'stat_graduados_label', "Estudiantes formándose<br>con metodologías activas.")) ?></span>
                </div>
            </div>
            <div class="trayectoria__stat-card">
                <div class="trayectoria__stat-icon">
                    <i class="fas fa-laptop-code"></i>
                </div>
                <div class="trayectoria__stat-text">
                    <span class="trayectoria__stat-number" data-count="<?= htmlspecialchars($n3) ?>" data-prefix="<?= htmlspecialchars($p3) ?>" data-suffix="<?= htmlspecialchars($s3) ?>"><?= htmlspecialchars($stat_carr_val) ?></span>
                    <span class="trayectoria__stat-label"><?= str_replace('\n', '<br>', content_get('trayectoria', 'stat_carreras_label', "Carreras técnicas y<br>tecnológicas disponibles.")) ?></span>
                </div>
            </div>
        </div>
    </div>
</section>