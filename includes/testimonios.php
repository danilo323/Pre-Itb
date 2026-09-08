<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('testimonios')) return; ?>
<?php
// includes/testimonios.php
//
// Carrusel horizontal de historias de éxito: se ve una a la vez y van pasando
// de izquierda a derecha.
//
// Son DOS carriles que se mueven a la vez, no uno: la foto vive en la columna
// izquierda de la rejilla y la cita en la derecha, y entre las dos queda el
// título de la sección, que NO se mueve. Si todo fuera un solo carril, el
// título viajaría con cada diapositiva y se vería dar un salto en cada paso.
// js/main.js los mantiene sincronizados en el mismo índice.

$testimonios_default = [
    [
        'imagen'  => 'img/MariaFernanda.png',
        'cita'    => '"El ITB me brindó las herramientas y el conocimiento necesario para destacarme en el campo laboral."',
        'rol'     => 'Graduada',
        'nombre'  => 'María Fernanda López',
        'carrera' => 'Graduada en Enfermería - Promoción 2022',
    ],
];

$testimonios = content_raw('testimonios', 'lista_testimonios', $testimonios_default);

// Compatibilidad hacia atrás: antes esto eran campos sueltos (un único
// testimonio). Si alguien abre el sitio con datos todavía sin migrar, se arma
// con ellos una sola historia en lugar de dejar la sección vacía.
if (empty($testimonios) || !is_array($testimonios)) {
    $testimonios = [[
        'imagen'  => content_raw('testimonios', 'imagen', 'img/MariaFernanda.png'),
        'cita'    => content_raw('testimonios', 'cita', ''),
        'rol'     => content_raw('testimonios', 'rol', 'Graduada'),
        'nombre'  => content_raw('testimonios', 'nombre', ''),
        'carrera' => content_raw('testimonios', 'carrera', ''),
    ]];
}

// Una historia sin cita ni nombre es un item a medio llenar en el panel.
$testimonios = array_values(array_filter((array)$testimonios, function ($t) {
    return is_array($t) && (trim($t['cita'] ?? '') !== '' || trim($t['nombre'] ?? '') !== '');
}));
if (empty($testimonios)) $testimonios = $testimonios_default;

$hay_varios = count($testimonios) > 1;
?>
<!-- ============================================= -->
<!-- TESTIMONIOS - HISTORIAS DE ÉXITO              -->
<!-- ============================================= -->
<section class="testimonios" id="testimonios">
    <div class="testimonios__container js-carrusel-testimonios">

        <!-- Columna izquierda: carril de fotos -->
        <div class="testimonios__photo">
            <div class="testimonios__photo-track">
                <?php foreach ($testimonios as $i => $t):
                    $foto = trim($t['imagen'] ?? '');
                    if (!content_image_exists($foto)) $foto = 'img/placeholder_autoridad.svg';
                    $nombre_alt = htmlspecialchars(trim($t['nombre'] ?? 'Graduado del ITB'), ENT_QUOTES, 'UTF-8');
                ?>
                    <!-- data-jarallax en CADA diapositiva, no en el marco: jarallax
                         reemplaza la <img> por una capa propia, asi que necesita un
                         contenedor por foto. La seccion 8 de js/main.js recoge todo
                         lo que lleve [data-jarallax], no hay que tocar el JS. -->
                    <div class="testimonios__photo-slide" data-jarallax data-speed="0.5" data-img-position="top" <?= $i > 0 ? 'aria-hidden="true"' : '' ?>>
                        <img src="<?= htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $nombre_alt ?>" class="jarallax-img">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Columna derecha: encabezado fijo + carril de citas -->
        <div class="testimonios__content">
            <span class="testimonios__tag"><?= content_get('testimonios', 'etiqueta_superior', 'Historias de Éxito') ?></span>
            <h2 class="testimonios__title"><?= nl2br(htmlspecialchars(content_raw('testimonios', 'titulo', 'Lo que dicen nuestros Graduados'), ENT_QUOTES, 'UTF-8')) ?></h2>

            <div class="testimonios__quote-viewport">
                <div class="testimonios__quote-track">
                    <?php foreach ($testimonios as $i => $t): ?>
                        <div class="testimonios__quote-slide" <?= $i > 0 ? 'aria-hidden="true"' : '' ?>>
                            <div class="testimonios__quote-block">
                                <span class="testimonios__quote-icon">&ldquo;</span>
                                <p class="testimonios__quote-text"><?= htmlspecialchars(trim($t['cita'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <div class="testimonios__author">
                                <p class="testimonios__author-role"><?= htmlspecialchars(trim($t['rol'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="testimonios__author-name"><?= htmlspecialchars(trim($t['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                                <p class="testimonios__author-program"><?= htmlspecialchars(trim($t['carrera'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if ($hay_varios): ?>
                <div class="testimonios__nav">
                    <div class="testimonios__dots" role="tablist" aria-label="Historias de éxito">
                        <?php foreach ($testimonios as $i => $t): ?>
                            <button type="button"
                                    class="testimonios__dot<?= $i === 0 ? ' is-active' : '' ?>"
                                    data-indice="<?= $i ?>"
                                    role="tab"
                                    aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                                    aria-label="Historia <?= $i + 1 ?> de <?= count($testimonios) ?>"></button>
                        <?php endforeach; ?>
                    </div>
                    <div class="testimonios__contador carrusel-contador js-contador"
                         data-total="<?= count($testimonios) ?>"
                         aria-live="polite" aria-atomic="true">
                        <span class="carrusel-contador__actual">1</span>/<span><?= count($testimonios) ?></span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>
