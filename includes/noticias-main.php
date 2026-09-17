<?php
// includes/noticias-main.php
if (!function_exists('is_visible')) require_once 'content_helper.php';

// Heredar las noticias de la página de inicio (CMS)
$eventos = content_raw('noticias', 'eventos', []);
$secundarias = content_raw('noticias', 'secundarias', []);

if (empty($eventos) || !is_array($eventos)) {
    $eventos = [[
        'categoria'   => content_raw('noticias', 'categoria', 'Evento'),
        'fecha'       => content_raw('noticias', 'fecha', ''),
        'titulo'      => content_raw('noticias', 'titulo', '¡METAMORFOSIS CREATIVA está por comenzar!'),
        'descripcion' => content_raw('noticias', 'descripcion', ''),
        'imagen'      => content_raw('noticias', 'imagen', 'img/noticia_1.png'),
    ]];
}

if (empty($secundarias) || !is_array($secundarias)) {
    $secundarias = [
        ['titulo' => 'Estudiantes de Diseño de Modas', 'fecha' => 'Agosto 20, 2026', 'imagen' => 'img/noticia_2.png'],
        ['titulo' => 'ITB promovió una movilidad',     'fecha' => 'Agosto 20, 2026', 'imagen' => 'img/noticia_3.png'],
    ];
}

$todas_las_noticias = array_merge($eventos, $secundarias);
$todas_las_noticias = array_values(array_filter((array)$todas_las_noticias, function ($item) {
    return is_array($item) && trim($item['titulo'] ?? '') !== '';
}));
?>
<!-- ============================================= -->
<!-- NOTICIAS MAIN CONTENT                       -->
<!-- ============================================= -->
<section class="noticias-page">
    <div class="noticias-page__header">
        <h2 class="noticias-page__title">Conoce todas las noticias de la semana ITB.</h2>
        <p class="noticias-page__subtitle">Entérate de los últimos acontecimientos, convenios y logros de nuestra comunidad académica. Mantente informado sobre todo lo que sucede en el Instituto Superior Universitario Bolivariano de Tecnología.</p>
    </div>

    <div class="noticias-page__container">
        
        <!-- ================= SIDEBAR (Filtros) ================= -->
        <aside class="noticias-page__sidebar">
            <div class="noticias-page__sidebar-header">
                <span class="noticias-page__sidebar-title"><i class="fas fa-sliders-h"></i> Filtros</span>
                <a href="#" class="noticias-page__sidebar-clear">Borrar</a>
            </div>

            <div class="noticias-page__filter-group">
                <h4 class="noticias-page__filter-title">Categorías</h4>
                <label class="noticias-page__filter-label">
                    <input type="checkbox" name="cat" value="salud"> Salud
                </label>
                <label class="noticias-page__filter-label">
                    <input type="checkbox" name="cat" value="tecnologia"> Tecnología
                </label>
                <label class="noticias-page__filter-label">
                    <input type="checkbox" name="cat" value="eventos"> Eventos
                </label>
                <label class="noticias-page__filter-label">
                    <input type="checkbox" name="cat" value="convenios"> Convenios
                </label>
            </div>

            <div class="noticias-page__filter-group">
                <h4 class="noticias-page__filter-title">Año de publicación</h4>
                <label class="noticias-page__filter-label">
                    <input type="checkbox" name="anio" value="2024"> 2024
                </label>
                <label class="noticias-page__filter-label">
                    <input type="checkbox" name="anio" value="2023"> 2023
                </label>
                <label class="noticias-page__filter-label">
                    <input type="checkbox" name="anio" value="2022"> 2022
                </label>
            </div>
        </aside>

        <!-- ================= MAIN CONTENT (Cards) ================= -->
        <div class="noticias-page__content">
            
            <!-- Buscador interno -->
            <div class="noticias-search-bar" style="margin-bottom: 20px; border-radius: 8px;">
                <div class="noticias-search-bar__container" style="padding: 0 20px;">
                    <div class="noticias-search-bar__text">
                        Explora nuestras noticias o busca un evento específico
                    </div>
                    <div class="noticias-search-bar__input-group">
                        <input type="text" placeholder="Busca por título, categoría o fecha..." class="noticias-search-bar__input">
                        <button class="noticias-search-bar__btn" aria-label="Buscar"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>

            <div class="noticias-page__toolbar">
                <span class="noticias-page__results-count">Mostrando 1-<?= min(4, count($todas_las_noticias)) ?> de <?= count($todas_las_noticias) ?> resultados</span>
                <div class="noticias-page__sort">
                    <label for="sort-select">Ordenar por:</label>
                    <select id="sort-select" class="noticias-page__sort-select">
                        <option value="recientes">Más recientes</option>
                        <option value="relevancia">Relevancia</option>
                    </select>
                </div>
            </div>

            <div class="noticias-page__list">
                <?php 
                // Limitar a 4 noticias por página
                $noticias_pagina = array_slice($todas_las_noticias, 0, 4);
                foreach ($noticias_pagina as $i => $item): 
                    $img = trim($item['imagen'] ?? '');
                    if (!content_image_exists($img)) $img = 'img/placeholder_imagen.svg';
                    $cat = htmlspecialchars(trim($item['categoria'] ?? 'NOTICIA'), ENT_QUOTES, 'UTF-8');
                    $fecha = htmlspecialchars(trim($item['fecha'] ?? ''), ENT_QUOTES, 'UTF-8');
                    $titulo = htmlspecialchars(trim($item['titulo'] ?? ''), ENT_QUOTES, 'UTF-8');
                ?>
                <article class="noticias-page__card">
                    <div class="noticias-page__card-img">
                        <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $titulo ?>">
                        <?php if ($i === 0): ?>
                            <span class="noticias-page__card-badge"><i class="fas fa-star"></i> Nuevo</span>
                        <?php endif; ?>
                    </div>
                    <div class="noticias-page__card-body">
                        <h3 class="noticias-page__card-title"><?= $titulo ?></h3>
                        <p class="noticias-page__card-category"><?= $cat ?></p>
                        <div class="noticias-page__card-meta">
                            <?php if ($fecha): ?>
                                <span><i class="far fa-calendar-alt"></i> Fecha: <?= $fecha ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="noticias-page__card-action">
                            <a href="#" class="btn--solid">
                                Leer noticia
                                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?php /* Paginación: por ahora es maqueta, los números todavía no
                     llevan a ninguna parte (falta el JS que filtre y pagine,
                     como el de js/oferta-programas.js). Se deja con sus marcas
                     de accesibilidad puestas para que al cablearla solo haya
                     que mover la clase y el aria-current. */ ?>
            <nav class="noticias-page__pagination" aria-label="Paginación de noticias">
                <a href="#" class="noticias-page__page active" aria-current="page" aria-label="Página 1, página actual">1</a>
                <a href="#" class="noticias-page__page" aria-label="Ir a la página 2">2</a>
                <a href="#" class="noticias-page__page" aria-label="Ir a la página 3">3</a>
                <a href="#" class="noticias-page__page" aria-label="Ir a la página 4">4</a>
                <span class="noticias-page__page-dots" aria-hidden="true">...</span>
                <a href="#" class="noticias-page__page" aria-label="Ir a la página 8">8</a>
                <a href="#" class="noticias-page__page-next" aria-label="Página siguiente"><i class="fas fa-chevron-right" aria-hidden="true"></i></a>
            </nav>

        </div>
    </div>
</section>
