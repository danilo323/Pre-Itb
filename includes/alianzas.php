<?php if (!function_exists('is_visible')) require_once 'content_helper.php'; if (!is_visible('alianzas')) return; ?>
<?php
// Logos administrables desde el panel (Inicio → Alianzas).
$alianzas_logos = content_raw('alianzas', 'lista_logos', [
    ['imagen' => 'img/alianza_1.png', 'nombre' => 'Aliado 1'],
    ['imagen' => 'img/alianza_2.jpg', 'nombre' => 'Aliado 2'],
    ['imagen' => 'img/alianza_3.png', 'nombre' => 'Aliado 3'],
    ['imagen' => 'img/alianza_4.png', 'nombre' => 'Aliado 4'],
    ['imagen' => 'img/alianza_5.png', 'nombre' => 'Aliado 5'],
]);

// Descartar los que quedaron sin imagen, o cuyo archivo ya no existe en disco
// (si no, el logo borrado deja un hueco de "imagen rota" en el carrusel).
$alianzas_logos = array_values(array_filter((array)$alianzas_logos, function ($l) {
    return content_image_exists($l['imagen'] ?? '');
}));

// Segunda fila: fotos de actividades, mismo carrusel en bucle que los logos.
$alianzas_fotos = content_raw('alianzas', 'lista_fotos', [
    ['imagen' => 'img/alianza_sub1.png', 'nombre' => 'Actividad ITB 1'],
    ['imagen' => 'img/alianza_sub2.png', 'nombre' => 'Actividad ITB 2'],
    ['imagen' => 'img/alianza_sub3.png', 'nombre' => 'Actividad ITB 3'],
    ['imagen' => 'img/alianza_sub4.png', 'nombre' => 'Actividad ITB 4'],
    ['imagen' => 'img/alianza_sub5.png', 'nombre' => 'Actividad ITB 5'],
    ['imagen' => 'img/alianza_sub6.png', 'nombre' => 'Actividad ITB 6'],
]);
$alianzas_fotos = array_values(array_filter((array)$alianzas_fotos, function ($f) {
    return content_image_exists($f['imagen'] ?? '');
}));
?>



<section class="alianzas" id="alianzas">
    <div class="alianzas__container">

        <div class="alianzas__header">
            <h2 class="alianzas__title">
                <?= content_title('alianzas', 'titulo', 'Alianzas del ITB') ?>
            </h2>
            <a href="#" class="btn-alianzas-todas" id="btn-todas-alianzas">
                <?= content_get('alianzas', 'boton_todas', 'Ver Alianzas y Convenios') ?>
                <span class="btn__icon-right"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>

        <?php if (!empty($alianzas_logos)): ?>
        <div class="alianzas__track">
            <div class="alianzas__logos">
                <?php
                // La lista se pinta dos veces seguidas: la animación desplaza el
                // carril un -50%, así que la segunda copia entra justo cuando la
                // primera sale y el bucle se ve continuo, sin saltos.
                for ($vuelta = 0; $vuelta < 2; $vuelta++):
                    foreach ($alianzas_logos as $logo):
                        $src = htmlspecialchars(trim($logo['imagen']), ENT_QUOTES, 'UTF-8');
                        $alt = htmlspecialchars(trim($logo['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
                ?>
                    <img src="<?= $src ?>" alt="<?= $alt ?>" <?= $vuelta === 1 ? 'aria-hidden="true"' : '' ?>>
                <?php
                    endforeach;
                endfor;
                ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($alianzas_fotos)): ?>
        <div class="alianzas__track alianzas__track--fotos">
            <div class="alianzas__fotos">
                <?php
                // Mismo truco del carril doble que la fila de logos de arriba.
                for ($vuelta = 0; $vuelta < 2; $vuelta++):
                    foreach ($alianzas_fotos as $foto):
                        $src = htmlspecialchars(trim($foto['imagen']), ENT_QUOTES, 'UTF-8');
                        $alt = htmlspecialchars(trim($foto['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
                ?>
                    <img src="<?= $src ?>" alt="<?= $alt ?>" <?= $vuelta === 1 ? 'aria-hidden="true"' : '' ?>>
                <?php
                    endforeach;
                endfor;
                ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>
