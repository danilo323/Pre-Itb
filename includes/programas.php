<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('programas')) return; ?>
<?php
// includes/programas.php
//
// La sección muestra SIEMPRE cuatro tarjetas, pase lo que pase:
//   - Las tres primeras son fijas, un programa cada una.
//   - La CUARTA es un carrusel: se queda con el cuarto programa y con todos
//     los que se agreguen después, y los va pasando de arriba hacia abajo.
//
// Antes, agregar un quinto programa desde el panel abría una segunda fila en
// la rejilla y descuadraba la sección. Ahora la rejilla no crece: crece la
// cuarta tarjeta por dentro.
//
// El PHP solo pinta todos los programas rotativos uno tras otro; el
// desplazamiento y el bucle los hace js/main.js. Si el JS no llega a cargar,
// se sigue viendo el cuarto programa, sin movimiento.

$programas_default = [
    ['imagen' => 'img/enfermeria.jpg',          'modalidad' => 'Presencial / Híbrida', 'titulo' => 'Tecnología Superior en Enfermería',              'duracion' => '2 Años (4 Semestres)'],
    ['imagen' => 'img/Mecanica.jpg',            'modalidad' => 'Presencial / Híbrida', 'titulo' => 'Tecnología Superior en Mecánica Automotriz',     'duracion' => '2 Años (4 Semestres)'],
    ['imagen' => 'img/desarrollo_software.jpg', 'modalidad' => 'Online / Presencial',  'titulo' => 'Tecnología Superior en Desarrollo de Software',  'duracion' => '2 Años (4 Semestres)'],
    ['imagen' => 'img/administracion.jpg',      'modalidad' => 'Online / Presencial',  'titulo' => 'Tecnología Superior en Administración',          'duracion' => '2 Años (4 Semestres)'],
];

$programas_list = content_raw('programas', 'lista_programas', $programas_default);
if (empty($programas_list) || !is_array($programas_list)) $programas_list = $programas_default;

// Un programa sin título es un item que quedó a medio llenar en el panel: se
// descarta para que no genere una tarjeta en blanco.
$programas_list = array_values(array_filter((array)$programas_list, function ($p) {
    return is_array($p) && trim($p['titulo'] ?? '') !== '';
}));

// Cuántas tarjetas fijas hay antes de la que rota. Son 3 porque la rejilla es
// de 4 columnas (ver .programas__grid en css/programas.css).
$programas_fijos = 3;

$fijos     = array_slice($programas_list, 0, $programas_fijos);
$rotativos = array_slice($programas_list, $programas_fijos);

// Pinta el interior de una tarjeta (imagen + cuerpo). Se usa igual en las
// tarjetas fijas y en cada diapositiva del carrusel, para que no haya dos
// copias del mismo HTML que se puedan desincronizar.
$pintar_programa = function (array $prog) {
    $img_src = trim($prog['imagen'] ?? '');
    if ($img_src !== '' && !content_image_exists($img_src)) $img_src = 'img/placeholder_imagen.svg';
    $titulo = htmlspecialchars(trim($prog['titulo'] ?? 'Título del programa'), ENT_QUOTES, 'UTF-8');
    ?>
    <div class="programas__card-img">
        <?php if ($img_src !== ''): ?>
            <img src="<?= htmlspecialchars($img_src, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $titulo ?>">
        <?php endif; ?>
    </div>
    <div class="programas__card-body">
        <h3 class="programas__card-title"><?= nl2br($titulo) ?></h3>
        <div class="programas__card-details">
            <p>Modalidad: <?= htmlspecialchars(trim($prog['modalidad'] ?? 'Presencial'), ENT_QUOTES, 'UTF-8') ?></p>
            <p>Duración: <?= htmlspecialchars(trim($prog['duracion'] ?? '2 Años'), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <a href="#" class="btn--outline-card">Ver programa <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span></a>
    </div>
    <?php
};
?>
<!-- ============================================= -->
<!-- PROGRAMAS DESTACADOS                          -->
<!-- ============================================= -->
<section class="programas" id="programas">
    <div class="programas__container">
        <div class="programas__header">
            <span class="programas__tag"><?= content_get('programas', 'etiqueta_superior', 'Formación Práctica e Innovadora') ?></span>
            <h2 class="programas__title">
                <?= htmlspecialchars(content_get('programas', 'titulo', 'Programas Destacados'), ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <p class="programas__subtitle">
                <?= content_get('programas', 'descripcion', 'Descubre nuestros programas tecnológicos de mayor demanda laboral, diseñados para insertarte rápidamente en el mercado de trabajo.') ?>
            </p>
        </div>

        <div class="programas__grid">

            <?php foreach ($fijos as $prog): ?>
                <div class="programas__card">
                    <?php $pintar_programa($prog); ?>
                </div>
            <?php endforeach; ?>

            <?php if (!empty($rotativos)): ?>
                <!-- Cuarta tarjeta: va pasando los programas 4, 5, 6... -->
                <div class="programas__card programas__card--carrusel js-carrusel-programas">
                    <?php if (count($rotativos) > 1): ?>
                        <div class="programas__contador carrusel-contador carrusel-contador--sobre-foto js-contador"
                             data-total="<?= count($rotativos) ?>"
                             aria-live="polite" aria-atomic="true">
                            <span class="carrusel-contador__actual">1</span>/<span><?= count($rotativos) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="programas__card-viewport">
                        <div class="programas__card-track">
                            <?php foreach ($rotativos as $i => $prog): ?>
                                <div class="programas__card-slide" <?= $i > 0 ? 'aria-hidden="true"' : '' ?>>
                                    <?php $pintar_programa($prog); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <div class="programas__footer">
            <a href="#" class="btn--solid">
                <?= content_get('programas', 'btn_ver_todos', 'Ver todos los programas') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>
    </div>
</section>
