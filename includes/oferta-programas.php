<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('oferta_programas')) return; ?>
<?php
// includes/oferta-programas.php
//
// Buscador de Oferta Académica: barra de búsqueda + filtros + grid de
// tarjetas + paginación.
//
// El filtrado, la búsqueda, el orden y la paginación son enteramente del
// lado del navegador (js/oferta-programas.js) sobre las tarjetas que PHP ya
// pintó aquí: no hay recarga de página por cada clic.
//
// De dónde salen las tarjetas, en este orden:
//   1. Páginas → Oferta Académica → SECCIÓN 2: PROGRAMAS DEL BUSCADOR. Es el
//      sitio donde se editan: está junto al resto de la página a la que
//      pertenecen y trae su propio interruptor "Visible".
//   2. La colección Contenido → Programas Académicos, que es donde vivían
//      antes. Se sigue leyendo para no perder lo que ya estuviera cargado
//      ahí, pero si la sección 2 tiene programas, mandan los de la sección.
//   3. Programas Destacados de Inicio. Último recurso para que el buscador
//      nunca salga vacío en una instalación sin nada cargado todavía.

$programas_items = content_raw('oferta_programas', 'lista_programas', []);
if (!is_array($programas_items)) $programas_items = [];

$btn_solicitar_texto_def = content_get('oferta_programas', 'btn_solicitar_texto', 'Solicitar Información');
$btn_solicitar_enlace_def = content_get('oferta_programas', 'btn_solicitar_enlace', '#');
$btn_matricular_texto_def = content_get('oferta_programas', 'btn_matricular_texto', 'Matricúlame');
$btn_matricular_enlace_def = content_get('oferta_programas', 'btn_matricular_enlace', '#');

if (empty($programas_items)) {
    $programas_items = collection_items('programas_academicos');
}

// Mismo criterio de "publicado" que ya usa includes/autoridades.php: acepta
// tanto el booleano real que guarda el panel como el '1'/'0' de los datos
// de ejemplo. Los programas de la sección 2 no llevan ese campo — quitarlos
// de la lista ES borrarlos — así que se dan por publicados.
$itb_campo_activo = function ($v) {
    return $v === null || $v === true || $v === '1' || $v === 1;
};
$programas_items = array_values(array_filter($programas_items, function ($p) use ($itb_campo_activo) {
    return is_array($p) && $itb_campo_activo($p['publicado'] ?? true);
}));

// Respaldo (3): ni la seccion 2 del panel ni la coleccion tienen nada. Pasa en
// una instalacion recien montada, donde la base de datos todavia no guarda
// esas claves: el grid salia en blanco y la pagina entera se leia como rota,
// con "No hay resultados" aunque el sitio si tiene oferta cargada.
//
// Se toman entonces los programas de "Programas Destacados" (seccion
// 'programas' de Inicio, includes/programas.php): son los mismos estudios
// guardados con otros nombres de campo, asi que solo hay que traducirlos a lo
// que esperan la tarjeta y los filtros de aqui. En cuanto se guarde la
// seccion 2 del panel, esto deja de usarse.
if (empty($programas_items)) {
    // Area del panel -> facultad que se imprime bajo el titulo + siglas del
    // "Campo de Estudio" por el que filtra la columna de la izquierda.
    $oferta_facultades = [
        'salud'                  => ['Facultad de Salud y Servicios Sociales', 'FASSS'],
        'transporte'             => ['Facultad de Transporte y Vialidad', 'FATV'],
        'ciencias empresariales' => ['Facultad de Ciencias Empresariales y Sistemas', 'FACES'],
        'tecnología'             => ['Facultad de Ciencias Empresariales y Sistemas', 'FACES'],
    ];

    // Los destacados guardan la modalidad como texto libre ("Hibrido",
    // "Online / Presencial"); las casillas del filtro solo entienden
    // Presencial / Hibrida / Remoto, asi que hay que encajarla en una de las
    // tres o la tarjeta desapareceria al marcar cualquier modalidad.
    // mbstring no siempre esta instalada; sin este respaldo la pagina entera
    // se caia justo en el caso que este bloque venia a cubrir (base recien
    // montada). Mismo patron que includes/transparencia.php.
    $oferta_a_minusculas = fn(string $s): string => function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);

    $oferta_modalidad = function (string $bruto) use ($oferta_a_minusculas): string {
        $m = $oferta_a_minusculas(trim($bruto));
        $remoto = str_contains($m, 'remoto') || str_contains($m, 'online') || str_contains($m, 'virtual');
        if (str_contains($m, 'brid')) return 'Hibrida';                  // hibrido / hibrida
        if ($remoto && str_contains($m, 'presencial')) return 'Hibrida'; // "Online / Presencial"
        if ($remoto) return 'Remoto';
        return 'Presencial';
    };

    $programas_items = [];
    foreach ((array) content_raw('programas', 'lista_programas', []) as $prog) {
        if (!is_array($prog) || trim($prog['titulo'] ?? '') === '') continue;

        $area = $oferta_a_minusculas(trim($prog['area'] ?? ''));
        // Sin area (o con una que no esta en el mapa) se usa la facultad mas
        // amplia, la que agrupa administracion, contabilidad, diseno y
        // sistemas: asi la tarjeta nunca sale sin facultad ni fuera de todos
        // los filtros de campo.
        [$facultad, $campo] = $oferta_facultades[$area] ?? $oferta_facultades['ciencias empresariales'];

        $sede = trim($prog['sede'] ?? '');

        $programas_items[] = [
            'tipo'          => 'Programa',
            'nombre'        => trim($prog['titulo']),
            'facultad'      => $facultad,
            'campo_estudio' => $campo,
            'modalidad'     => $oferta_modalidad((string) ($prog['modalidad'] ?? '')),
            'duracion'      => trim($prog['duracion'] ?? ''),
            'campus'        => $sede !== '' ? $sede : 'Campus Teresa Benites',
            // Los destacados no guardan ano de inicio y el filtro solo ofrece
            // 2026 y 2027: se reparte de forma fija (el mismo programa cae
            // siempre en el mismo ano) para que ninguna de las dos casillas
            // quede sin resultados. Es relleno: el ano real se pone en el panel.
            'anio_inicio'   => (crc32($prog['titulo']) % 2 === 0) ? '2026' : '2027',
            'etiqueta'      => trim($prog['etiqueta'] ?? ''),
            'imagen'        => trim($prog['imagen'] ?? ''),
            'publicado'     => true,
        ];
    }
}
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
                        $perfil = trim($p['perfil'] ?? '');
                        $competencias_administrativa = trim($p['competencias_administrativa'] ?? '');
                        $competencias_asistencial = trim($p['competencias_asistencial'] ?? '');
                        $documentos_admision = trim($p['documentos_admision'] ?? '');
                        $inversion = trim($p['inversion'] ?? '');
                        $horarios = trim($p['horarios'] ?? '');
                        $documentos = is_array($p['documentos'] ?? null) ? $p['documentos'] : [];
                        $detalle_id = 'oferta-card-detalle-' . $card_index;
                        $btn_solicitar_texto = trim($p['btn_solicitar_texto'] ?? '') !== '' ? $p['btn_solicitar_texto'] : $btn_solicitar_texto_def;
                        $btn_solicitar_enlace = trim($p['btn_solicitar_enlace'] ?? '') !== '' ? $p['btn_solicitar_enlace'] : $btn_solicitar_enlace_def;
                        $btn_matricular_texto = trim($p['btn_matricular_texto'] ?? '') !== '' ? $p['btn_matricular_texto'] : $btn_matricular_texto_def;
                        $btn_matricular_enlace = trim($p['btn_matricular_enlace'] ?? '') !== '' ? $p['btn_matricular_enlace'] : $btn_matricular_enlace_def;

                        // Valores iniciales para que los programas ya creados conserven
                        // una ficha completa. Al guardar desde el panel, cada valor pasa
                        // a ser independiente por programa.
                        if ($perfil === '') $perfil = 'El Técnico Superior podrá desempeñarse, bajo supervisión profesional, en establecimientos de salud, brindando atención administrativa y asistencial de calidad.';
                        if ($competencias_administrativa === '') $competencias_administrativa = "Participar en el ingreso, egreso y traslado de pacientes en establecimientos de salud.\nParticipar en el manejo de carpetas, informes, archivos y registros de atención de salud.\nOrientar, informar y ayudar a pacientes, familiares, usuarios y público en general.";
                        if ($competencias_asistencial === '') $competencias_asistencial = "Participar en el ingreso, egreso y traslado de pacientes en establecimientos de salud.\nParticipar en el manejo de carpetas, informes, archivos y registros de atención de salud.\nOrientar, informar y ayudar a pacientes, familiares, usuarios y público en general.";
                        if ($documentos_admision === '') $documentos_admision = "Copia de cédula de identidad a color.\nCopia de papeleta de votación a color (actualizada).\n6 fotos tamaño carnet (formales y tomadas en estudio fotográfico).\nCopia de título de bachiller, legalizado por el ministerio de educación.";
                        if ($inversion === '') $inversion = 'Consulta con un asesor para conocer la inversión y las facilidades de pago disponibles.';
                        if ($horarios === '') $horarios = 'Consulta los horarios disponibles para este programa.';
                        if (!$documentos) $documentos = [
                            ['titulo' => 'Malla'], ['titulo' => 'Acuerdo'], ['titulo' => 'Justificación'], ['titulo' => 'Tríptico'],
                        ];
                        $lista_competencias = function ($texto) use ($h) {
                            $items = array_filter(array_map('trim', preg_split('/\R/', $texto)));
                            $html = '<ul>';
                            foreach ($items as $item) $html .= '<li>' . $h($item) . '</li>';
                            return $html . '</ul>';
                        };
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
                                $etiqueta = (crc32($nombre) % 2 === 0) ? 'Nueva' : 'Destacada';
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
                        <div class="oferta-card__resumen">
                            <div class="oferta-card__img"<?= $imagen ? " style=\"background-image:url('" . $h($imagen) . "')\"" : '' ?>></div>
                            <div class="oferta-card__body">
                                <h3 class="oferta-card__title"><?= $h($nombre) ?></h3>
                                <p class="oferta-card__facultad"><?= $h($p['facultad'] ?? '') ?></p>
                                <ul class="oferta-card__meta">
                                    <li><i class="fas fa-user-graduate" aria-hidden="true"></i> Modalidad: <?= $h($modalidad_label) ?></li>
                                    <li><i class="far fa-calendar-alt" aria-hidden="true"></i> Duración: <?= $h($p['duracion'] ?? '') ?></li>
                                    <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i> <?= $h($p['campus'] ?? '') ?></li>
                                </ul>
                            </div>
                            <button class="oferta-card__toggle" type="button" aria-expanded="false" aria-controls="<?= $detalle_id ?>" aria-label="Ver información de <?= $h($nombre) ?>">
                                <i class="fas fa-chevron-down" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="oferta-card__detalle" id="<?= $detalle_id ?>" hidden>
                            <section class="oferta-card__informacion">
                                <div class="oferta-card__info-tabs" role="tablist" aria-label="Información del programa">
                                    <button type="button" role="tab" aria-selected="true" class="is-active">Documentos de admisión <i class="fas fa-chevron-right" aria-hidden="true"></i></button>
                                    <button type="button" role="tab" aria-selected="false">Inversión <i class="fas fa-chevron-right" aria-hidden="true"></i></button>
                                    <button type="button" role="tab" aria-selected="false">Horarios <i class="fas fa-chevron-right" aria-hidden="true"></i></button>
                                </div>
                                <div class="oferta-card__info-panel" role="tabpanel"><?= $lista_competencias($documentos_admision) ?></div>
                                <div class="oferta-card__info-panel" role="tabpanel" hidden><?= $lista_competencias($inversion) ?></div>
                                <div class="oferta-card__info-panel" role="tabpanel" hidden><?= $lista_competencias($horarios) ?></div>
                            </section>
                            <section class="oferta-card__perfil">
                                <h4>Perfil</h4>
                                <?= nl2br($h($perfil)) ?>
                            </section>
                            <section class="oferta-card__competencias">
                                <div class="oferta-card__tabs" role="tablist" aria-label="Competencias de <?= $h($nombre) ?>">
                                    <span class="oferta-card__competencias-titulo">Competencias</span>
                                    <button type="button" role="tab" aria-selected="true" class="is-active">Área Administrativa</button>
                                    <button type="button" role="tab" aria-selected="false">Área Asistencial</button>
                                </div>
                                <div class="oferta-card__tab-panel" role="tabpanel"><?= $lista_competencias($competencias_administrativa) ?></div>
                                <div class="oferta-card__tab-panel" role="tabpanel" hidden><?= $lista_competencias($competencias_asistencial) ?></div>
                            </section>
                            <section class="oferta-card__documentos">
                                <h4>Documentos de Carrera</h4>
                                <div class="oferta-card__documentos-pie">
                                    <div class="oferta-card__pdfs">
                                        <?php foreach ($documentos as $documento): if (!is_array($documento) || trim($documento['titulo'] ?? '') === '') continue; $archivo = trim($documento['archivo'] ?? ($documento['url'] ?? '')); ?>
                                            <?php if ($archivo !== ''): ?>
                                                <a href="<?= $h($archivo) ?>" target="_blank" rel="noopener"><span class="oferta-card__pdf-icon" aria-hidden="true">PDF</span><span><?= $h($documento['titulo']) ?></span></a>
                                            <?php else: ?>
                                                <span class="oferta-card__pdf-pendiente"><span class="oferta-card__pdf-icon" aria-hidden="true">PDF</span><span><?= $h($documento['titulo']) ?></span></span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="oferta-card__acciones">
                                        <a href="<?= $h($btn_solicitar_enlace) ?>" class="navbar__btn navbar__btn--outline"><?= $h($btn_solicitar_texto) ?></a>
                                        <a href="<?= $h($btn_matricular_enlace) ?>" class="navbar__btn btn btn--solid"><?= $h($btn_matricular_texto) ?><span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span></a>
                                    </div>
                                </div>
                            </section>
                        </div>
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
