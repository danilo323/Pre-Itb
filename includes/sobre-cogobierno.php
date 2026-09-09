<?php
if (!function_exists('is_visible')) require_once 'content_helper.php';
if (!is_visible('sobre_cogobierno')) return;

if (!function_exists('sobre_imagen')) {
    function sobre_imagen(string $ruta, string $respaldo = 'img/placeholder_imagen.svg'): string {
        $ruta = trim($ruta);
        return content_image_exists($ruta) ? $ruta : $respaldo;
    }
}

$cogob_miembros = (array) content_raw('sobre_cogobierno', 'miembros', [
    ['foto' => 'img/placeholder_autoridad.svg', 'nombre' => 'Obst. Lenny Mariscal San Martín', 'cargo' => 'Representante de Docentes'],
    ['foto' => 'img/placeholder_autoridad.svg', 'nombre' => 'Ing. Marcos Escaleras Gómez', 'cargo' => 'Representante de Trabajadores'],
]);
$cogob_fondo = sobre_imagen((string) content_raw('sobre_cogobierno', 'card_imagen', 'img/trayectoria.png'));
$cogob_icono = trim((string) content_raw('sobre_cogobierno', 'card_icono', ''));
$cogob_url   = trim((string) content_raw('sobre_cogobierno', 'card_boton_url', '#'));
if ($cogob_url === '') $cogob_url = '#';
?>
<!-- ============================================= -->
<!-- 5. CO GOBIERNO                                -->
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
                    $miembro_foto = sobre_imagen((string)($miembro['foto'] ?? ''), 'img/placeholder_autoridad.svg');
                    $miembro_cargo = trim((string)($miembro['cargo'] ?? ''));
                    ?>
                    <article class="sobre-cogob__card">
                        <div class="sobre-cogob__photo">
                            <img src="<?= htmlspecialchars($miembro_foto, ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($miembro_nombre, ENT_QUOTES, 'UTF-8') ?>">
                            <button type="button" class="sobre-cogob__plus"
                                aria-label="Ver perfil de <?= htmlspecialchars($miembro_nombre, ENT_QUOTES, 'UTF-8') ?>"
                                data-nombre="<?= htmlspecialchars($miembro_nombre, ENT_QUOTES, 'UTF-8') ?>"
                                data-cargo="<?= htmlspecialchars($miembro_cargo, ENT_QUOTES, 'UTF-8') ?>"
                                data-foto="<?= htmlspecialchars($miembro_foto, ENT_QUOTES, 'UTF-8') ?>">
                                <i class="fas fa-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                        <h3 class="sobre-cogob__name"><?= htmlspecialchars($miembro_nombre, ENT_QUOTES, 'UTF-8') ?></h3>
                        <span class="sobre-cogob__role"><?= htmlspecialchars($miembro_cargo, ENT_QUOTES, 'UTF-8') ?></span>
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
