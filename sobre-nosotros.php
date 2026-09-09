<?php
session_start();
require_once 'includes/content_helper.php';

// Página "Sobre Nosotros".
// Todo lo que se ve aquí sale de data/content.json:
//   - Menú, portada, alianzas y autoridades -> son las MISMAS de Inicio, así
//     que se reutilizan sus includes tal cual (se editan en Páginas → Inicio).
//   - Las 4 secciones propias -> Páginas → Sobre Nosotros, con sus claves
//     sobre_intro / sobre_mision / sobre_vision / sobre_valores.

// Devuelve una ruta de imagen que de verdad exista; si no, el placeholder,
// para no dejar el ícono de "imagen rota" cuando alguien borra el archivo
// directamente de la carpeta (mismo criterio que includes/autoridades.php).
$sobre_imagen = function (string $ruta, string $respaldo = 'img/placeholder_imagen.svg'): string {
    $ruta = trim($ruta);
    return content_image_exists($ruta) ? $ruta : $respaldo;
};

// Parte un texto en párrafos: el admin separa con una línea en blanco.
$sobre_parrafos = function (string $texto): array {
    $partes = preg_split('/\R\s*\R/u', trim($texto)) ?: [];
    return array_values(array_filter(array_map('trim', $partes), fn($p) => $p !== ''));
};
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= content_get('ajustes', 'description', 'Instituto Superior Tecnológico Bolivariano de Tecnología - Educación Superior de Excelencia.') ?>">
    <meta name="keywords" content="<?= content_get('ajustes', 'keywords', 'educación, instituto, guayaquil, carreras, tecnología, bolivariano, itb') ?>">
    <title>Sobre Nosotros — <?= content_get('ajustes', 'site_name', 'ITB - Instituto Superior Tecnológico Bolivariano de Tecnología') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,700;0,800;0,900;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jarallax/2.1.4/jarallax.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/sobre-nosotros.css">
</head>

<body class="page-sobre-nosotros">

    <?php include 'includes/header.php'; ?>

    <main>
        <?php // Misma portada de Inicio (Páginas → Inicio → HERO). ?>
        <?php include 'includes/hero.php'; ?>

        <?php if (is_visible('sobre_intro')): ?>
            <?php
            $intro_titulo = content_get('sobre_intro', 'titulo', 'Formando Líderes Prácticos con Visión de Futuro');
            $intro_texto  = (string) content_raw('sobre_intro', 'descripcion', "En ITB priorizamos una educación de alta calidad enfocada en formar pensadores innovadores. Te brindamos un entorno práctico para potenciar tus talentos más allá del salón de clases, conectándote con la comunidad y el sector laboral.\n\nCon casi 3 décadas de trayectoria desde nuestra fundación en 1996, nos hemos consolidado como un referente de educación superior en Guayaquil, abriendo las puertas para que transformes tu pasión en tu profesión.");
            $intro_imagen = $sobre_imagen((string) content_raw('sobre_intro', 'imagen', 'img/trayectoria.png'));
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
                            <?php foreach ($sobre_parrafos($intro_texto) as $parrafo): ?>
                                <p><?= nl2br(htmlspecialchars($parrafo, ENT_QUOTES, 'UTF-8')) ?></p>
                            <?php endforeach; ?>
                        </div>

                        <!-- Tarjeta de cita (va dos espacios debajo del texto) -->
                        <div class="tarjeta-cita">
                            <p class="tarjeta-cita__texto"><?= nl2br(content_get('sobre_intro', 'cita_texto', 'Impulsamos una educación práctica, accesible e innovadora para formarte como el profesional que la industria necesita.')) ?></p>
                            <?php
                            $autor_cita = content_get('sobre_intro', 'cita_autor', 'PhD. Elena Tolozano Benites, Rectora');
                            if ($autor_cita && !str_starts_with(trim($autor_cita), '—') && !str_starts_with(trim($autor_cita), '-')) {
                                $autor_cita = '— ' . $autor_cita;
                            }
                            ?>
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
        <?php endif; ?>

        <?php
        // ---------------------------------------------------------------
        // 2 y 3. MISIÓN Y VISIÓN
        // Son la misma tarjeta: cambia el contenido y de qué lado queda la
        // imagen, así que se pintan con el mismo bloque para no duplicar.
        // ---------------------------------------------------------------
        $sobre_mv = [
            [
                'clave'    => 'sobre_mision',
                'ancla'    => 'nuestra-mision',
                'invertida' => false,
                'titulo'   => 'Nuestra misión',
                'texto'    => 'Somos una Institución de Educación Superior comprometida con la excelencia académica y la formación de profesionales tecnológicos innovadores.',
                'imagen'   => 'img/experiencia.png',
                'puntos'   => [
                    ['texto' => 'Autonomía de pensamiento y liderazgo.'],
                    ['texto' => 'Aliado estratégico de empresas e instituciones.'],
                    ['texto' => 'Formación práctica orientada al desarrollo económico y social.'],
                ],
            ],
            [
                'clave'    => 'sobre_vision',
                'ancla'    => 'nuestra-vision',
                'invertida' => true, // imagen a la derecha
                'titulo'   => 'Nuestra visión',
                'texto'    => 'Ser una institución de futuro, acreditada internacionalmente, que lidere la formación tecnológica en el Ecuador.',
                'imagen'   => 'img/bienestar_estudiantil_1.png',
                'puntos'   => [
                    ['texto' => 'Liderazgo en educación tecnológica universitaria.'],
                    ['texto' => 'Alianzas e integración con instituciones nacionales e internacionales.'],
                    ['texto' => 'Transferencia tecnológica e innovación para el aprendizaje continuo.'],
                ],
            ],
        ];
        ?>

        <!-- ============================================= -->
        <!-- 2 y 3. MISIÓN Y VISIÓN                        -->
        <!-- ============================================= -->
        <section class="sobre-mv-section" id="mision-y-vision">
            <div class="sobre-mv__container">
                <?php
                foreach ($sobre_mv as $bloque):
                    if (!is_visible($bloque['clave'])) continue;

                    $mv_imagen = $sobre_imagen((string) content_raw($bloque['clave'], 'imagen', $bloque['imagen']));
                    $mv_puntos = (array) content_raw($bloque['clave'], 'puntos', $bloque['puntos']);
                ?>
                    <article class="sobre-mv__card<?= $bloque['invertida'] ? ' sobre-mv__card--invertida' : '' ?>" id="<?= $bloque['ancla'] ?>">

                        <div class="sobre-mv__image">
                            <img src="<?= htmlspecialchars($mv_imagen, ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= content_get($bloque['clave'], 'titulo', $bloque['titulo']) ?> - ITB">
                        </div>

                        <div class="sobre-mv__body">
                            <h2 class="sobre-mv__title"><?= content_get($bloque['clave'], 'titulo', $bloque['titulo']) ?></h2>

                            <!-- La bolita + línea continua + bolita que acompaña al texto -->
                            <div class="sobre-mv__text">
                                <span class="sobre-mv__linea" aria-hidden="true"></span>

                                <p class="sobre-mv__desc"><?= nl2br(htmlspecialchars(content_get($bloque['clave'], 'descripcion', $bloque['texto']), ENT_QUOTES, 'UTF-8')) ?></p>

                                <?php if (!empty($mv_puntos)): ?>
                                    <ul class="sobre-mv__list">
                                        <?php foreach ($mv_puntos as $punto): ?>
                                            <?php $punto_texto = trim((string)($punto['texto'] ?? '')); ?>
                                            <?php if ($punto_texto === '') continue; ?>
                                            <li>
                                                <span class="sobre-mv__check">✓</span>
                                                <span class="sobre-mv__item-text"><?= htmlspecialchars($punto_texto, ENT_QUOTES, 'UTF-8') ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>

                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if (is_visible('sobre_valores')): ?>
            <?php
            $valores = (array) content_raw('sobre_valores', 'items', [
                ['icono' => 'img/placeholder_imagen.svg', 'titulo' => 'Cogobierno',   'descripcion' => 'Participación y gestión democrática'],
                ['icono' => 'img/placeholder_imagen.svg', 'titulo' => 'Igualdad',     'descripcion' => 'Oportunidades sin discriminación'],
                ['icono' => 'img/placeholder_imagen.svg', 'titulo' => 'Calidad',      'descripcion' => 'Excelencia en educación superior'],
                ['icono' => 'img/placeholder_imagen.svg', 'titulo' => 'Pertinencia',  'descripcion' => 'Programas alineados a la sociedad'],
                ['icono' => 'img/placeholder_imagen.svg', 'titulo' => 'Integralidad', 'descripcion' => 'Formación académica y humana'],
            ]);
            ?>
            <!-- ============================================= -->
            <!-- 4. PRINCIPIOS Y VALORES                       -->
            <!-- ============================================= -->
            <section class="sobre-valores" id="principios-y-valores">
                <div class="sobre-valores__container">

                    <div class="sobre-valores__header">
                        <span class="section-tag"><?= content_get('sobre_valores', 'etiqueta_superior', 'NUESTROS PRINCIPIOS Y VALORES') ?></span>
                        <h2 class="sobre-valores__title"><?= nl2br(content_get('sobre_valores', 'titulo', 'Los pilares que guían la excelencia académica y humana en el ITB.')) ?></h2>
                    </div>

                    <div class="sobre-valores__grid">
                        <?php foreach ($valores as $valor): ?>
                            <?php
                            $valor_titulo = trim((string)($valor['titulo'] ?? ''));
                            if ($valor_titulo === '') continue;
                            $valor_icono = $sobre_imagen((string)($valor['icono'] ?? ''));
                            ?>
                            <div class="sobre-valores__item">
                                <div class="sobre-valores__icon">
                                    <img src="<?= htmlspecialchars($valor_icono, ENT_QUOTES, 'UTF-8') ?>"
                                        alt="<?= htmlspecialchars($valor_titulo, ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                                <h3 class="sobre-valores__item-title"><?= htmlspecialchars($valor_titulo, ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="sobre-valores__item-text"><?= htmlspecialchars(trim((string)($valor['descripcion'] ?? '')), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </section>
        <?php endif; ?>

        <?php // 5. Mismas autoridades de Inicio (colección Equipo). ?>
        <?php include 'includes/autoridades.php'; ?>


        <?php if (is_visible('sobre_cogobierno')): ?>
            <?php
            // CO GOBIERNO (se edita en Páginas → Sobre Nosotros → SECCIÓN 5).
            $cogob_miembros = (array) content_raw('sobre_cogobierno', 'miembros', [
                ['foto' => 'img/placeholder_autoridad.svg', 'nombre' => 'Obst. Lenny Mariscal San Martín', 'cargo' => 'Representante de Docentes'],
                ['foto' => 'img/placeholder_autoridad.svg', 'nombre' => 'Ing. Marcos Escaleras Gómez', 'cargo' => 'Representante de Trabajadores'],
            ]);
            $cogob_fondo = $sobre_imagen((string) content_raw('sobre_cogobierno', 'card_imagen', 'img/trayectoria.png'));
            // El ícono es opcional: si no cargaron ninguno, se pinta el de
            // organigrama de Font Awesome (que ya viene cargado en la página).
            $cogob_icono = trim((string) content_raw('sobre_cogobierno', 'card_icono', ''));
            $cogob_url = trim((string) content_raw('sobre_cogobierno', 'card_boton_url', '#'));
            if ($cogob_url === '') $cogob_url = '#';
            ?>
            <!-- ============================================= -->
            <!-- CO GOBIERNO                                   -->
            <!-- ============================================= -->
            <section class="sobre-cogob" id="co-gobierno">
                <div class="sobre-cogob__container">

                    <!-- Lado izquierdo: título, texto y representantes -->
                    <div class="sobre-cogob__content">
                        <h2 class="sobre-cogob__title"><?= content_get('sobre_cogobierno', 'titulo', 'Co Gobierno') ?></h2>
                        <p class="sobre-cogob__desc"><?= nl2br(content_get('sobre_cogobierno', 'descripcion', 'Representantes de la comunidad académica que participan activamente en la toma de decisiones e institucionalidad del ITB')) ?></p>

                        <div class="sobre-cogob__grid">
                            <?php foreach ($cogob_miembros as $miembro): ?>
                                <?php
                                $miembro_nombre = trim((string)($miembro['nombre'] ?? ''));
                                if ($miembro_nombre === '') continue;
                                $miembro_foto = $sobre_imagen((string)($miembro['foto'] ?? ''), 'img/placeholder_autoridad.svg');
                                ?>
                                <article class="sobre-cogob__card">
                                    <div class="sobre-cogob__photo">
                                        <img src="<?= htmlspecialchars($miembro_foto, ENT_QUOTES, 'UTF-8') ?>"
                                            alt="<?= htmlspecialchars($miembro_nombre, ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="button" class="sobre-cogob__plus"
                                            aria-label="Ver perfil de <?= htmlspecialchars($miembro_nombre, ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="fas fa-plus" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <h3 class="sobre-cogob__name"><?= htmlspecialchars($miembro_nombre, ENT_QUOTES, 'UTF-8') ?></h3>
                                    <span class="sobre-cogob__role"><?= htmlspecialchars(trim((string)($miembro['cargo'] ?? '')), ENT_QUOTES, 'UTF-8') ?></span>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Lado derecho: tarjeta de la estructura orgánica -->
                    <aside class="sobre-cogob__banner"
                        style="background-image: url('<?= htmlspecialchars($cogob_fondo, ENT_QUOTES, 'UTF-8') ?>')">
                        <div class="sobre-cogob__banner-content">
                            <div class="sobre-cogob__banner-icon">
                                <?php if ($cogob_icono !== '' && content_image_exists($cogob_icono)): ?>
                                    <img src="<?= htmlspecialchars($cogob_icono, ENT_QUOTES, 'UTF-8') ?>" alt="">
                                <?php else: ?>
                                    <i class="fas fa-sitemap" aria-hidden="true"></i>
                                <?php endif; ?>
                            </div>
                            <h3 class="sobre-cogob__banner-title"><?= content_get('sobre_cogobierno', 'card_titulo', 'Estructura Orgánica') ?></h3>
                            <p class="sobre-cogob__banner-text"><?= nl2br(content_get('sobre_cogobierno', 'card_descripcion', 'Conoce la jerarquía, áreas académicas y departamentos administrativos que conforman el ITB.')) ?></p>
                            <a href="<?= htmlspecialchars($cogob_url, ENT_QUOTES, 'UTF-8') ?>" class="btn btn--solid">
                                <?= content_get('sobre_cogobierno', 'card_boton', 'Ver Organigrama') ?>
                                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
                            </a>
                        </div>
                    </aside>

                </div>
            </section>
        <?php endif; ?>

        <?php
        // Mismas alianzas de Inicio, pero solo el carrusel de imágenes: esta
        // bandera le dice al include que no pinte el título ni el botón.
        $alianzas_solo_carrusel = true;
        include 'includes/alianzas.php';
        ?>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jarallax/2.1.4/jarallax.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/countup.js@2.8.0/dist/countUp.umd.js"></script>
    <script src="js/main.js"></script>

</body>

</html>
