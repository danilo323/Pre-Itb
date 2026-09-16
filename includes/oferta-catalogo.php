<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('oferta_catalogo')) return; ?>
<?php
// includes/oferta-catalogo.php
//
// Catálogo de la página Oferta Académica: el titular de entrada, la barra de
// búsqueda, la columna de filtros y la lista de programas con su paginación.
//
// Todo el filtrado ocurre en el navegador (js/oferta-catalogo.js): el PHP
// imprime TODAS las tarjetas una sola vez y cada una lleva en sus data-* los
// datos por los que se puede filtrar (modalidad, año, facultad, tipo). Así no
// hace falta recargar la página ni montar un endpoint para cada clic, y si el
// JS no llega a cargar se siguen viendo los programas, solo que sin filtros.
//
// Se edita en el panel en Páginas → Oferta Académica, sección 'oferta_catalogo'.

$catalogo_default = [
    [
        'imagen'    => 'img/enfermeria.jpg',
        'titulo'    => 'Tecnología Superior en enfermería',
        'facultad'  => 'Facultad de Salud / Servicios Sociales',
        'campo'     => 'FASSS',
        'etiqueta'  => 'Salud',
        'modalidad' => 'Presencial / Híbrida',
        'duracion'  => '2 Años (4 Semestres)',
        'campus'    => 'Campus Teresa Benites',
        'anio'      => '2026',
        'tipo'      => 'Cursos / Programas',
        'enlace'    => '#',
    ],
    [
        'imagen'    => 'img/Mecanica.jpg',
        'titulo'    => 'Tecnología Superior en Mecánica Automotriz',
        'facultad'  => 'Facultad de Transporte y Vialidad',
        'campo'     => 'FATV',
        'etiqueta'  => 'Automotriz',
        'modalidad' => 'Presencial / Híbrida',
        'duracion'  => '2 Años (4 Semestres)',
        'campus'    => 'Campus Teresa Benites',
        'anio'      => '2026',
        'tipo'      => 'Cursos / Programas',
        'enlace'    => '#',
    ],
    [
        'imagen'    => 'img/desarrollo_software.jpg',
        'titulo'    => 'Tecnología Superior en Desarrollo de Software',
        'facultad'  => 'Facultad de Ciencias Empresariales y Sistemas / Económicas y Empresariales',
        'campo'     => 'FACES',
        'etiqueta'  => 'Software',
        'modalidad' => 'Presencial / Híbrida',
        'duracion'  => '2 Años (4 Semestres)',
        'campus'    => 'Campus Teresa Benites',
        'anio'      => '2027',
        'tipo'      => 'Cursos / Programas',
        'enlace'    => '#',
    ],
    [
        'imagen'    => 'img/administracion.jpg',
        'titulo'    => 'Tecnología Superior en Administración',
        'facultad'  => 'Facultad de Ciencias Empresariales y Sistemas / Económicas y Empresariales',
        'campo'     => 'FACES',
        'etiqueta'  => 'Gestión',
        'modalidad' => 'Remoto',
        'duracion'  => '2 Años (4 Semestres)',
        'campus'    => 'Campus Teresa Benites',
        'anio'      => '2027',
        'tipo'      => 'Cursos / Programas',
        'enlace'    => '#',
    ],
];

$catalogo_list = content_raw('oferta_catalogo', 'lista_programas', $catalogo_default);
if (empty($catalogo_list) || !is_array($catalogo_list)) $catalogo_list = $catalogo_default;

// Un programa sin título es un item que quedó a medio llenar en el panel: se
// descarta para que no genere una tarjeta en blanco.
$catalogo_list = array_values(array_filter((array)$catalogo_list, function ($p) {
    return is_array($p) && trim($p['titulo'] ?? '') !== '';
}));

// Cuántas tarjetas se ven por página. El JS lee este mismo número del
// data-por-pagina, para que el contador ("Mostrando 1-4 de N") y la paginación
// no se puedan desincronizar con lo que realmente se está viendo.
$catalogo_por_pagina = max(1, (int) content_get('oferta_catalogo', 'por_pagina', '4'));
$catalogo_total      = count($catalogo_list);

// Las listas de casillas se sacan de los propios programas: al agregar uno
// nuevo desde el panel, su modalidad / año / facultad aparecen solos en la
// columna de filtros sin tener que tocar este archivo.
$catalogo_valores = function (array $lista, string $campo, bool $separar = false) {
    $vistos = [];
    foreach ($lista as $item) {
        $bruto = trim($item[$campo] ?? '');
        if ($bruto === '') continue;
        // "Presencial / Híbrida" son DOS modalidades, no una: se parten para
        // que cada casilla filtre por una sola.
        $partes = $separar ? preg_split('#\s*/\s*#u', $bruto) : [$bruto];
        foreach ($partes as $parte) {
            $parte = trim($parte);
            if ($parte !== '' && !in_array($parte, $vistos, true)) $vistos[] = $parte;
        }
    }
    return $vistos;
};

$catalogo_modalidades = $catalogo_valores($catalogo_list, 'modalidad', true);
$catalogo_anios       = $catalogo_valores($catalogo_list, 'anio');
$catalogo_campos      = $catalogo_valores($catalogo_list, 'campo');
sort($catalogo_anios);

// Grupo plegable de casillas. Los tres bloques (modalidad, año, campo) son
// iguales salvo el título y la lista, así que se pintan con la misma función.
$catalogo_grupo = function (string $titulo, string $nombre, array $opciones) {
    if (empty($opciones)) return;
    $id = 'filtro-' . $nombre;
    ?>
    <div class="catalogo-filtros__grupo js-filtro-grupo">
        <button type="button" class="catalogo-filtros__grupo-toggle js-filtro-toggle"
                aria-expanded="true" aria-controls="<?= $id ?>">
            <span><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></span>
            <i class="fas fa-minus" aria-hidden="true"></i>
        </button>
        <div class="catalogo-filtros__opciones" id="<?= $id ?>">
            <?php foreach ($opciones as $i => $opcion): ?>
                <?php $input_id = $id . '-' . $i; ?>
                <label class="catalogo-filtros__opcion" for="<?= $input_id ?>">
                    <input type="checkbox" id="<?= $input_id ?>"
                           class="js-filtro-check"
                           data-filtro="<?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>"
                           value="<?= htmlspecialchars($opcion, ENT_QUOTES, 'UTF-8') ?>">
                    <span><?= htmlspecialchars($opcion, ENT_QUOTES, 'UTF-8') ?></span>
                </label>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
};
?>
<!-- ============================================= -->
<!-- OFERTA ACADÉMICA — CATÁLOGO DE PROGRAMAS      -->
<!-- ============================================= -->
<section class="catalogo" id="catalogo">

    <!-- 1. Titular de entrada -->
    <div class="catalogo__intro">
        <div class="catalogo__intro-inner">
            <h2 class="catalogo__intro-title">
                <?= nl2br(htmlspecialchars(content_get('oferta_catalogo', 'titulo', "Cientos de programas.\nUn título diseñado a tu medida."), ENT_QUOTES, 'UTF-8')) ?>
            </h2>
            <p class="catalogo__intro-text">
                <?= nl2br(htmlspecialchars(content_get('oferta_catalogo', 'descripcion', 'Elegir tu especialidad es solo el punto de partida. Diseña tu propia ruta académica combinando las materias y áreas que realmente te apasionan. Estudia con total flexibilidad y llega tan lejos como te propongas.'), ENT_QUOTES, 'UTF-8')) ?>
            </p>
        </div>
    </div>

    <!-- 2. Franja de búsqueda -->
    <div class="catalogo__buscador">
        <div class="catalogo__buscador-inner">
            <p class="catalogo__buscador-label">
                <?= nl2br(htmlspecialchars(content_get('oferta_catalogo', 'buscador_label', "Explora nuestras Áreas Académicas\no busca un Programa específico"), ENT_QUOTES, 'UTF-8')) ?>
            </p>
            <div class="catalogo__buscador-campo">
                <label class="catalogo__buscador-oculto" for="catalogo-busqueda">
                    <?= htmlspecialchars(content_get('oferta_catalogo', 'buscador_placeholder', 'Busca por curso, programa o facultad...'), ENT_QUOTES, 'UTF-8') ?>
                </label>
                <input type="search" id="catalogo-busqueda" class="catalogo__buscador-input"
                       placeholder="<?= htmlspecialchars(content_get('oferta_catalogo', 'buscador_placeholder', 'Busca por curso, programa o facultad...'), ENT_QUOTES, 'UTF-8') ?>"
                       autocomplete="off">
                <button type="button" class="catalogo__buscador-btn js-catalogo-buscar" aria-label="Buscar">
                    <i class="fas fa-search" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Filtros + resultados -->
    <div class="catalogo__cuerpo js-catalogo" data-por-pagina="<?= $catalogo_por_pagina ?>">
        <div class="catalogo__layout">

            <!-- 3a. Columna de filtros -->
            <aside class="catalogo-filtros" aria-label="Filtros de programas">
                <div class="catalogo-filtros__cabecera">
                    <span class="catalogo-filtros__titulo">
                        <i class="fas fa-sliders-h" aria-hidden="true"></i>
                        <?= htmlspecialchars(content_get('oferta_catalogo', 'filtros_titulo', 'Filtros'), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <button type="button" class="catalogo-filtros__borrar js-filtros-borrar">
                        <?= htmlspecialchars(content_get('oferta_catalogo', 'filtros_borrar', 'Borrar'), ENT_QUOTES, 'UTF-8') ?>
                    </button>
                </div>

                <!-- Cursos / Programas: es una sola elección, por eso van botones
                     de radio y no casillas. -->
                <div class="catalogo-filtros__grupo">
                    <p class="catalogo-filtros__grupo-titulo">
                        <?= htmlspecialchars(content_get('oferta_catalogo', 'filtro_tipo_titulo', 'Mostrar resultados por'), ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <div class="catalogo-filtros__opciones">
                        <label class="catalogo-filtros__opcion" for="filtro-tipo-cursos">
                            <input type="radio" name="catalogo-tipo" id="filtro-tipo-cursos"
                                   class="js-filtro-tipo" value="cursos" checked>
                            <span>Cursos</span>
                        </label>
                        <label class="catalogo-filtros__opcion" for="filtro-tipo-programas">
                            <input type="radio" name="catalogo-tipo" id="filtro-tipo-programas"
                                   class="js-filtro-tipo" value="programas">
                            <span>Programas</span>
                        </label>
                    </div>
                </div>

                <?php
                $catalogo_grupo(content_get('oferta_catalogo', 'filtro_modalidad_titulo', 'Modalidad'), 'modalidad', $catalogo_modalidades);
                $catalogo_grupo(content_get('oferta_catalogo', 'filtro_anio_titulo', 'Año de Inicio'), 'anio', $catalogo_anios);
                $catalogo_grupo(content_get('oferta_catalogo', 'filtro_campo_titulo', 'Campo de Estudio'), 'campo', $catalogo_campos);
                ?>
            </aside>

            <!-- 3b. Resultados -->
            <div class="catalogo__resultados">
                <div class="catalogo__barra">
                    <p class="catalogo__conteo js-catalogo-conteo"
                       data-plantilla="<?= htmlspecialchars(content_get('oferta_catalogo', 'conteo_texto', 'Mostrando {desde}-{hasta} de {total} resultados'), ENT_QUOTES, 'UTF-8') ?>"
                       aria-live="polite">
                        Mostrando <?= $catalogo_total ? 1 : 0 ?>-<?= min($catalogo_por_pagina, $catalogo_total) ?> de <?= $catalogo_total ?> resultados
                    </p>
                    <div class="catalogo__orden">
                        <label for="catalogo-orden">
                            <?= htmlspecialchars(content_get('oferta_catalogo', 'orden_label', 'Ordenar por:'), ENT_QUOTES, 'UTF-8') ?>
                        </label>
                        <select id="catalogo-orden" class="catalogo__orden-select js-catalogo-orden">
                            <option value="relevancia">Relevancia</option>
                            <option value="az">Nombre (A-Z)</option>
                            <option value="za">Nombre (Z-A)</option>
                            <option value="anio">Año de inicio</option>
                        </select>
                    </div>
                </div>

                <div class="catalogo__lista js-catalogo-lista">
                    <?php foreach ($catalogo_list as $indice => $prog): ?>
                        <?php
                        $img_src = trim($prog['imagen'] ?? '');
                        if ($img_src !== '' && !content_image_exists($img_src)) $img_src = 'img/placeholder_imagen.svg';
                        $titulo   = trim($prog['titulo'] ?? '');
                        $facultad = trim($prog['facultad'] ?? '');
                        $etiqueta = trim($prog['etiqueta'] ?? '');
                        $enlace   = trim($prog['enlace'] ?? '') !== '' ? trim($prog['enlace']) : '#';
                        // Un programa recien agregado en el panel puede venir sin
                        // el selector 'Aparece en' tocado. Sin este respaldo su
                        // tipo quedaria vacio y la tarjeta no saldria con NINGUNO
                        // de los dos botones (Cursos / Programas): invisible.
                        $tipo     = trim($prog['tipo'] ?? '') !== '' ? trim($prog['tipo']) : 'Cursos / Programas';
                        // El buscador mira este texto: título, facultad, campo y
                        // etiqueta juntos, para que "enfermería", "FASSS" o
                        // "Salud" encuentren la misma tarjeta.
                        $busqueda = $titulo . ' ' . $facultad . ' ' . trim($prog['campo'] ?? '') . ' ' . $etiqueta;
                        ?>
                        <article class="catalogo-card js-catalogo-card"
                                 data-orden="<?= $indice ?>"
                                 data-titulo="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>"
                                 data-busqueda="<?= htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8') ?>"
                                 data-tipo="<?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?>"
                                 data-modalidad="<?= htmlspecialchars(trim($prog['modalidad'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                 data-anio="<?= htmlspecialchars(trim($prog['anio'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                 data-campo="<?= htmlspecialchars(trim($prog['campo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">

                            <div class="catalogo-card__img">
                                <?php if ($img_src !== ''): ?>
                                    <img src="<?= htmlspecialchars($img_src, ENT_QUOTES, 'UTF-8') ?>"
                                         alt="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                                <?php endif; ?>
                            </div>

                            <div class="catalogo-card__body">
                                <h3 class="catalogo-card__title">
                                    <a href="<?= htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?></a>
                                </h3>

                                <?php if ($facultad !== ''): ?>
                                    <p class="catalogo-card__facultad"><?= htmlspecialchars($facultad, ENT_QUOTES, 'UTF-8') ?></p>
                                <?php endif; ?>

                                <ul class="catalogo-card__datos">
                                    <li><i class="fas fa-graduation-cap" aria-hidden="true"></i><span>Modalidad: <?= htmlspecialchars(trim($prog['modalidad'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></li>
                                    <li><i class="fas fa-clock" aria-hidden="true"></i><span>Duración: <?= htmlspecialchars(trim($prog['duracion'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></li>
                                    <li><i class="fas fa-map-marker-alt" aria-hidden="true"></i><span><?= htmlspecialchars(trim($prog['campus'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span></li>
                                </ul>
                            </div>

                            <div class="catalogo-card__accion">
                                <a href="<?= htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8') ?>" class="btn--outline-card">
                                    <?= htmlspecialchars(content_get('oferta_catalogo', 'btn_card', 'Ver programa'), ENT_QUOTES, 'UTF-8') ?>
                                    <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                                </a>
                            </div>

                            <?php if ($etiqueta !== ''): ?>
                                <span class="catalogo-card__tag"><?= htmlspecialchars($etiqueta, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>

                <!-- Sale cuando los filtros dejan la lista sin ninguna tarjeta -->
                <p class="catalogo__vacio js-catalogo-vacio" hidden>
                    <?= htmlspecialchars(content_get('oferta_catalogo', 'sin_resultados', 'No encontramos programas con esos filtros. Prueba quitando alguno.'), ENT_QUOTES, 'UTF-8') ?>
                </p>

                <!-- La paginación la arma el JS con los resultados que queden -->
                <nav class="catalogo__paginacion js-catalogo-paginacion" aria-label="Paginación de programas"></nav>
            </div>

        </div>
    </div>
</section>
