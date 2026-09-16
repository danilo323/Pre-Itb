<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('oferta_intro')) return; ?>
<?php
// includes/oferta-programas.php
//
// Buscador de Oferta Académica: barra de búsqueda + filtros + grid de
// tarjetas + paginación. Las tarjetas salen de la colección del panel
// "Programas Académicos" (Contenido → Programas Académicos), no de una
// sección de página — cada tarjeta es un item independiente, igual que
// Equipo o Noticias.
//
// El filtrado, la búsqueda, el orden y la paginación son enteramente del
// lado del navegador (js/oferta-programas.js) sobre las tarjetas que PHP ya
// pintó aquí: no hay recarga de página por cada clic.
//
// Reutiliza el interruptor "Visible" de la sección 'oferta_intro' de arriba:
// esconder la presentación esconde también el buscador, porque uno no tiene
// sentido sin el otro en esta página.

$programas_items = collection_items('programas_academicos');

// Mismo criterio de "publicado" que ya usa includes/autoridades.php: acepta
// tanto el booleano real que guarda el panel como el '1'/'0' de los datos
// de ejemplo.
$itb_campo_activo = function ($v) {
    return $v === null || $v === true || $v === '1' || $v === 1;
};
$programas_items = array_values(array_filter($programas_items, function ($p) use ($itb_campo_activo) {
    return $itb_campo_activo($p['publicado'] ?? true);
}));
?>
<!-- ============================================= -->
<!-- OFERTA ACADÉMICA — BUSCADOR                   -->
<!-- ============================================= -->
<section class="oferta-buscador" id="oferta-programas">
    <div class="oferta-buscador__searchbar">
        <div class="oferta-buscador__container oferta-buscador__searchbar-inner">
            <p class="oferta-buscador__searchbar-label">
                Explora nuestras Áreas Académicas<br>o busca un Programa específico
            </p>
            <div class="transparencia__search-group oferta-buscador__search-group">
                <input type="text" class="transparencia__search-input" id="oferta-buscador-input"
                       placeholder="Busca por curso, programa o facultad...">
                <button type="button" class="transparencia__search-btn" aria-label="Buscar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="10.5" cy="10.5" r="6"></circle>
                        <line x1="20" y1="20" x2="15" y2="15"></line>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div class="oferta-buscador__container">
        <div class="oferta-buscador__body">

            <div class="oferta-buscador__filtros-head">
                <span><i class="fas fa-sliders-h"></i> Filtros</span>
                <button type="button" class="oferta-buscador__borrar" id="oferta-buscador-borrar">Borrar</button>
            </div>

            <div class="oferta-buscador__resultados-head">
                <?php /* aria-live: al filtrar o cambiar de página, un lector de
                         pantalla lee el recuento nuevo sin que haya que ir a
                         buscarlo. "polite" espera a que termine de hablar. */ ?>
                <span class="oferta-buscador__contador" id="oferta-buscador-contador"
                      role="status" aria-live="polite"></span>
                <label class="oferta-buscador__orden">
                    Ordenar por:
                    <select id="oferta-buscador-orden">
                        <option value="relevancia">Relevancia</option>
                        <option value="az">Nombre A-Z</option>
                        <option value="za">Nombre Z-A</option>
                    </select>
                </label>
            </div>

            <aside class="oferta-buscador__filtros">
                <div class="oferta-buscador__grupo">
                    <h4>Mostrar resultados por</h4>
                    <label class="oferta-buscador__radio">
                        <input type="radio" name="oferta-tipo" value="Curso" data-filtro-tipo>
                        Cursos
                    </label>
                    <label class="oferta-buscador__radio">
                        <input type="radio" name="oferta-tipo" value="Programa" data-filtro-tipo checked>
                        Programas
                    </label>
                </div>

                <div class="oferta-buscador__grupo">
                    <h4>Modalidad <svg class="oferta-buscador__grupo-toggle" width="22" height="3" viewBox="0 0 22 3" shape-rendering="crispEdges" aria-hidden="true"><rect width="22" height="3" fill="currentColor"/></svg></h4>
                    <label class="oferta-buscador__check">
                        <input type="checkbox" value="Presencial" data-filtro="modalidad"> Presencial
                    </label>
                    <label class="oferta-buscador__check">
                        <input type="checkbox" value="Hibrida" data-filtro="modalidad"> Híbrida
                    </label>
                    <label class="oferta-buscador__check">
                        <input type="checkbox" value="Remoto" data-filtro="modalidad"> Remoto
                    </label>
                </div>

                <div class="oferta-buscador__grupo">
                    <h4>Año de Inicio <svg class="oferta-buscador__grupo-toggle" width="22" height="3" viewBox="0 0 22 3" shape-rendering="crispEdges" aria-hidden="true"><rect width="22" height="3" fill="currentColor"/></svg></h4>
                    <label class="oferta-buscador__check">
                        <input type="checkbox" value="2026" data-filtro="anio"> 2026
                    </label>
                    <label class="oferta-buscador__check">
                        <input type="checkbox" value="2027" data-filtro="anio"> 2027
                    </label>
                </div>

                <div class="oferta-buscador__grupo oferta-buscador__grupo--campo">
                    <h4>Campo de Estudio <svg class="oferta-buscador__grupo-toggle" width="22" height="3" viewBox="0 0 22 3" shape-rendering="crispEdges" aria-hidden="true"><rect width="22" height="3" fill="currentColor"/></svg></h4>
                    <label class="oferta-buscador__campo">
                        <input type="checkbox" value="FASSS" data-filtro="campo"> FASSS
                    </label>
                    <label class="oferta-buscador__campo">
                        <input type="checkbox" value="FATV" data-filtro="campo"> FATV
                    </label>
                    <label class="oferta-buscador__campo">
                        <input type="checkbox" value="FACES" data-filtro="campo"> FACES
                    </label>
                </div>
            </aside>

            <div class="oferta-buscador__resultados">
                <div class="oferta-buscador__grid" id="oferta-buscador-grid">
                    <?php 
                        $card_index = 0;
                        foreach ($programas_items as $p):
                        $nombre = trim($p['nombre'] ?? '');
                        if ($nombre === '') continue;
                        $tipo = $p['tipo'] ?? 'Programa';
                        $modalidad = $p['modalidad'] ?? '';
                        $modalidad_label = $modalidad === 'Hibrida' ? 'Híbrida' : $modalidad;
                        $anio = $p['anio_inicio'] ?? '';
                        $campo = $p['campo_estudio'] ?? '';
                        $etiqueta = trim($p['etiqueta'] ?? '');
                        $imagen = content_image_exists($p['imagen'] ?? '') ? $p['imagen'] : '';
                        $h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
                        $is_hidden = $card_index >= 4 ? 'hidden' : '';
                        $card_index++;
                    ?>
                    <article class="oferta-card" <?= $is_hidden ?>
                             data-nombre="<?= $h(strtolower($nombre . ' ' . ($p['facultad'] ?? ''))) ?>"
                             data-tipo="<?= $h($tipo) ?>"
                             data-modalidad="<?= $h($modalidad) ?>"
                             data-anio="<?= $h($anio) ?>"
                             data-campo="<?= $h($campo) ?>">
                        <?php 
                            if ($etiqueta === '') {
                                $etiqueta = (crc32($nombre) % 2 === 0) ? 'Nuevo' : 'Tendencia';
                            }
                        ?>
                        <?php if ($etiqueta !== ''): ?>
                            <?php 
                                $is_new = stripos($etiqueta, 'nuev') !== false;
                                $svg_file = $is_new ? 'estrella-insignia.svg' : 'estrella-5.svg';
                            ?>
                            <span class="oferta-card__badge<?= !$is_new ? ' oferta-card__badge--alt' : '' ?>">
                                <?= file_get_contents(__DIR__ . '/../svg/' . $svg_file) ?>
                                <span><?= $h($etiqueta) ?></span>
                            </span>
                        <?php endif; ?>
                        <div class="oferta-card__img"<?= $imagen ? " style=\"background-image:url('" . $h($imagen) . "')\"" : '' ?>></div>
                        <div class="oferta-card__body">
                            <h3 class="oferta-card__title"><?= $h($nombre) ?></h3>
                            <p class="oferta-card__facultad"><?= $h($p['facultad'] ?? '') ?></p>
                            <ul class="oferta-card__meta">
                                <li><?= file_get_contents(__DIR__ . '/../svg/icono-modalidad.svg') ?> Modalidad: <?= $h($modalidad_label) ?></li>
                                <li><?= file_get_contents(__DIR__ . '/../svg/icono-duracion.svg') ?> Duración: <?= $h($p['duracion'] ?? '') ?></li>
                                <li><?= file_get_contents(__DIR__ . '/../svg/icono-campus.svg') ?> <?= $h($p['campus'] ?? '') ?></li>
                            </ul>
                        </div>
                        <a href="#" class="btn--outline-card oferta-card__cta">
                            Ver programa <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                        </a>
                    </article>
                    <?php endforeach; ?>
                </div>

                <p class="oferta-buscador__vacio" id="oferta-buscador-vacio" hidden>
                    No encontramos programas con esos filtros. Prueba quitando alguno.
                </p>

                <nav class="oferta-buscador__paginacion" id="oferta-buscador-paginacion" aria-label="Paginación de resultados"></nav>
            </div>

        </div>
    </div>
</section>
