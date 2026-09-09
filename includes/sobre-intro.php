<?php
if (!function_exists('is_visible')) require_once 'content_helper.php';
if (!is_visible('sobre_intro')) return;

if (!function_exists('sobre_imagen')) {
    function sobre_imagen(string $ruta, string $respaldo = 'img/placeholder_imagen.svg'): string {
        $ruta = trim($ruta);
        return content_image_exists($ruta) ? $ruta : $respaldo;
    }
}

if (!function_exists('sobre_parrafos')) {
    function sobre_parrafos(string $texto): array {
        $partes = preg_split('/\R\s*\R/u', trim($texto)) ?: [];
        return array_values(array_filter(array_map('trim', $partes), fn($p) => $p !== ''));
    }
}

$intro_titulo = content_get('sobre_intro', 'titulo', 'Formando Líderes Prácticos con Visión de Futuro');
$intro_texto  = (string) content_raw('sobre_intro', 'descripcion', "En ITB priorizamos una educación de alta calidad enfocada en formar pensadores innovadores. Te brindamos un entorno práctico para potenciar tus talentos más allá del salón de clases, conectándote con la comunidad y el sector laboral.\n\nCon casi 3 décadas de trayectoria desde nuestra fundación en 1996, nos hemos consolidado como un referente de educación superior en Guayaquil, abriendo las puertas para que transformes tu pasión en tu profesión.");
$intro_imagen = sobre_imagen((string) content_raw('sobre_intro', 'imagen', 'img/trayectoria.png'));
$autor_cita   = content_get('sobre_intro', 'cita_autor', 'PhD. Elena Tolozano Benites, Rectora');
if ($autor_cita && !str_starts_with(trim($autor_cita), '—') && !str_starts_with(trim($autor_cita), '-')) {
    $autor_cita = '— ' . $autor_cita;
}
?>
<!-- ============================================= -->
<!-- 1. PRESENTACIÓN                               -->
<!-- ============================================= -->
<section class="sobre-intro" id="sobre-nosotros">
    <div class="sobre-intro__container">

        <!-- Lado izquierdo: textos + tarjeta de cita -->
        <div class="sobre-intro__content">
            <span class="section-tag"><?= content_get('sobre_intro', 'etiqueta_superior', 'Trayectoria y Compromiso Educativo') ?></span>

            <h1 class="sobre-intro__title"><?= nl2br($intro_titulo) ?></h1>

            <div class="sobre-intro__text">
                <?php foreach (sobre_parrafos($intro_texto) as $parrafo): ?>
                    <p><?= nl2br(htmlspecialchars($parrafo, ENT_QUOTES, 'UTF-8')) ?></p>
                <?php endforeach; ?>
            </div>

            <!-- Tarjeta de cita (va dos espacios debajo del texto) -->
            <div class="tarjeta-cita">
                <p class="tarjeta-cita__texto"><?= nl2br(content_get('sobre_intro', 'cita_texto', 'Impulsamos una educación práctica, accesible e innovadora para formarte como el profesional que la industria necesita.')) ?></p>
                <span class="tarjeta-cita__autor"><?= htmlspecialchars($autor_cita, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>

        <!-- Lado derecho: imagen con tarjeta flotante de trayectoria -->
        <div class="sobre-intro__image-wrapper">
            <div class="sobre-intro__image">
                <img src="<?= htmlspecialchars($intro_imagen, ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= content_get('sobre_intro', 'etiqueta_superior', 'Trayectoria y Compromiso Educativo') ?> - ITB">
            </div>
            <div class="sobre-intro__badge">
                <span class="sobre-intro__badge-tag">DESDE 1996</span>
                <div class="sobre-intro__badge-title">29+ Años Impulsando tu Futuro</div>
                <div class="sobre-intro__badge-rating">
                    <div class="sobre-intro__badge-stars">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="sobre-intro__badge-score">4.9</span>
                </div>
            </div>
        </div>

    </div>
</section>
