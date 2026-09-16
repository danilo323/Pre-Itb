<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('noticias')) return; ?>
<?php
// includes/noticias.php
//
// Dos carruseles independientes, los dos administrables desde el panel
// (Inicio → Noticias):
//   - EVENTOS: tarjeta grande de la izquierda. Pasan de izquierda a derecha.
//   - SECUNDARIAS: tarjeta de la derecha. Se ven dos y bajan de arriba a abajo.
//
// El PHP solo pinta TODOS los items uno detrás de otro; el desplazamiento y el
// bucle los hace js/main.js. Si el JS no llega a cargar, la sección se sigue
// viendo: queda el primer evento y las dos primeras noticias, sin movimiento.

$eventos = content_raw('noticias', 'eventos', []);

// Compatibilidad hacia atrás: antes la noticia principal eran campos sueltos
// (titulo, categoria, fecha, descripcion, imagen) en vez de un repeater. Si
// alguien abre el sitio con datos viejos todavía sin migrar, se arma con ellos
// un único evento en lugar de dejar la tarjeta vacía.
if (empty($eventos) || !is_array($eventos)) {
    $eventos = [[
        'categoria'   => content_raw('noticias', 'categoria', 'Evento'),
        'fecha'       => content_raw('noticias', 'fecha', ''),
        'titulo'      => content_raw('noticias', 'titulo', '¡METAMORFOSIS CREATIVA está por comenzar!'),
        'descripcion' => content_raw('noticias', 'descripcion', ''),
        'imagen'      => content_raw('noticias', 'imagen', 'img/noticia_1.png'),
    ]];
}

// Un evento sin título es un item que quedó a medio llenar en el panel: se
// descarta para que no genere una diapositiva en blanco dentro del carrusel.
$eventos = array_values(array_filter((array)$eventos, function ($e) {
    return is_array($e) && trim($e['titulo'] ?? '') !== '';
}));

$secundarias_default = [
    ['titulo' => 'Estudiantes de Diseño de Modas', 'fecha' => 'Agosto 20, 2026', 'imagen' => 'img/noticia_2.png'],
    ['titulo' => 'ITB promovió una movilidad',     'fecha' => 'Agosto 20, 2026', 'imagen' => 'img/noticia_3.png'],
];
$secundarias = content_raw('noticias', 'secundarias', $secundarias_default);
if (empty($secundarias) || !is_array($secundarias)) $secundarias = $secundarias_default;
$secundarias = array_values(array_filter((array)$secundarias, function ($s) {
    return is_array($s) && trim($s['titulo'] ?? '') !== '';
}));

// Cuántas noticias secundarias se ven a la vez en la tarjeta de la derecha.
// El JS lo lee del data-attribute para saber cuánto medir y cuántas clonar.
$secundarias_visibles = 2;
?>
<!-- ============================================= -->
<!-- NOTICIAS, EVENTOS Y ALIANZAS                  -->
<!-- ============================================= -->
<section class="noticias" id="noticias">
    <div class="noticias__container">
        <div class="noticias__header">
            <div>
                <span class="section-tag"><?= content_get('noticias', 'etiqueta_superior', 'Vida Universitaria y Actualidad') ?></span>
                <h2 class="noticias__title">
                    <?= content_title('noticias', 'titulo_seccion', 'Noticias y Eventos del ITB') ?>
                </h2>
            </div>
            <a href="noticias.php" class="btn--solid" id="btn-todas-noticias">
                <?= content_get('noticias', 'boton_todas', 'Ver más Noticias y Eventos') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <div class="noticias__grid">

            <!-- ---- EVENTOS: carrusel horizontal ---- -->
            <div class="noticias__main js-carrusel-eventos">
                <div class="noticias__main-viewport">
                    <div class="noticias__main-track">
                        <?php foreach ($eventos as $i => $ev):
                            $ev_img = trim($ev['imagen'] ?? '');
                            if (!content_image_exists($ev_img)) $ev_img = 'img/placeholder_imagen.svg';
                            $ev_cat    = htmlspecialchars(trim($ev['categoria'] ?? 'Evento'), ENT_QUOTES, 'UTF-8');
                            $ev_fecha  = htmlspecialchars(trim($ev['fecha'] ?? ''), ENT_QUOTES, 'UTF-8');
                            $ev_titulo = htmlspecialchars(trim($ev['titulo'] ?? ''), ENT_QUOTES, 'UTF-8');
                            $ev_desc   = htmlspecialchars(trim($ev['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8');
                        ?>
                        <article class="noticias__evento" <?= $i > 0 ? 'aria-hidden="true"' : '' ?>>
                            <div class="noticias__main-img">
                                <img src="<?= htmlspecialchars($ev_img, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $ev_titulo ?>">
                            </div>
                            <div class="noticias__main-body">
                                <div class="noticias__meta">
                                    <span class="meta-cat"><?= $ev_cat ?></span>
                                    <?php if ($ev_fecha !== ''): ?>
                                        <span class="meta-div">—</span>
                                        <span class="meta-date"><?= $ev_fecha ?></span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="noticias__main-title"><?= $ev_titulo ?></h3>
                                <p class="noticias__main-desc"><?= $ev_desc ?></p>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (count($eventos) > 1): ?>
                <!-- Puntos: además de indicar en qué evento vas, permiten saltar
                     a uno concreto sin esperar a que el carrusel dé la vuelta. -->
                <div class="noticias__contador carrusel-contador js-contador"
                     data-total="<?= count($eventos) ?>"
                     aria-live="polite" aria-atomic="true">
                    <span class="carrusel-contador__actual">1</span>/<span><?= count($eventos) ?></span>
                </div>
                <div class="noticias__dots" role="tablist" aria-label="Eventos">
                    <?php foreach ($eventos as $i => $ev): ?>
                        <button type="button"
                                class="noticias__dot<?= $i === 0 ? ' is-active' : '' ?>"
                                data-indice="<?= $i ?>"
                                role="tab"
                                aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
                                aria-label="Evento <?= $i + 1 ?> de <?= count($eventos) ?>"></button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- ---- NOTICIAS SECUNDARIAS: carrusel vertical ---- -->
            <div class="noticias__list js-carrusel-noticias" data-visibles="<?= $secundarias_visibles ?>">
                <?php if (count($secundarias) > $secundarias_visibles): ?>
                    <!-- Arranca en "2/5" porque se ven DOS noticias a la vez: el
                         numero de la izquierda es el de la ultima visible. -->
                    <div class="noticias__contador noticias__contador--lista carrusel-contador js-contador"
                         data-total="<?= count($secundarias) ?>"
                         aria-live="polite" aria-atomic="true">
                        <span class="carrusel-contador__actual"><?= min($secundarias_visibles, count($secundarias)) ?></span>/<span><?= count($secundarias) ?></span>
                    </div>
                <?php endif; ?>
                <div class="noticias__list-viewport">
                    <div class="noticias__list-track">
                        <?php foreach ($secundarias as $i => $sec):
                            $sec_img = trim($sec['imagen'] ?? '');
                            if (!content_image_exists($sec_img)) $sec_img = 'img/placeholder_imagen.svg';
                            $sec_titulo = htmlspecialchars(trim($sec['titulo'] ?? ''), ENT_QUOTES, 'UTF-8');
                            $sec_fecha  = htmlspecialchars(trim($sec['fecha'] ?? ''), ENT_QUOTES, 'UTF-8');
                        ?>
                        <div class="noticias__item" <?= $i >= $secundarias_visibles ? 'aria-hidden="true"' : '' ?>>
                            <div class="noticias__item-img">
                                <img src="<?= htmlspecialchars($sec_img, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $sec_titulo ?>">
                            </div>
                            <div class="noticias__item-body">
                                <div class="noticias__item-text">
                                    <div class="noticias__meta">
                                        <span class="meta-cat">NOTICIA</span>
                                        <?php if ($sec_fecha !== ''): ?>
                                            <span class="meta-div">—</span>
                                            <span class="meta-date"><?= $sec_fecha ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="noticias__item-title"><?= $sec_titulo ?></h4>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
