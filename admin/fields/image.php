<?php
// admin/fields/image.php
//
// Campo de imagen de cualquier sección, portada, pie de página o item de
// colección. YA NO SUBE ARCHIVOS: las imágenes entran por Globales →
// Biblioteca y aquí solo se ELIGE una de las que ya están ahí.
//
// Por qué: antes cada campo era una puerta de subida independiente, así que la
// misma foto acababa duplicada media docena de veces con nombres distintos y
// no había forma de ver qué había cargado en el sitio. Con una sola puerta, la
// biblioteca es la lista completa y real de las imágenes disponibles.
//
// Lo que viaja en el formulario es solo el input oculto con la ruta
// ('img/foto.jpg'). El botón "Elegir de la biblioteca" abre el selector que
// monta admin/assets/biblioteca.js y este escribe ahí la ruta elegida.

require_once __DIR__ . '/../biblioteca_lib.php';

function field_image_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val   = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help  = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';

    $has_image = !empty($val);

    // El panel vive en /admin, así que una ruta del sitio ('img/x.jpg') hay que
    // subirla un nivel para que el <img> del panel la encuentre.
    $img_src = $val;
    if ($has_image && strpos($img_src, 'http') !== 0 && strpos($img_src, '../') !== 0) {
        $img_src = '../' . ltrim($img_src, '/');
    }

    // ¿El archivo sigue estando? Si alguien lo borró por FTP, se enseña el
    // hueco en vez de un icono de imagen rota.
    $image_exists = true;
    if ($has_image && strpos($val, 'http') !== 0) {
        $image_exists = file_exists(dirname(__DIR__, 2) . '/' . ltrim($val, '/'));
    }

    $show_preview = $has_image && $image_exists;
    $preview_hidden_class     = $show_preview ? '' : ' is-hidden';
    $placeholder_hidden_class = $show_preview ? ' is-hidden' : '';

    // Si hay ruta pero el archivo no está, el aviso tiene que decirlo: "no hay
    // imagen" y "la imagen ya no existe" se arreglan de formas distintas.
    $placeholder_icon = 'bi-image';
    $placeholder_text = 'Ninguna imagen seleccionada';
    if ($has_image && !$image_exists) {
        $placeholder_icon = 'bi-exclamation-triangle-fill';
        $placeholder_text = 'La imagen ya no está en la biblioteca';
    }

    $texto_boton = $has_image ? 'Cambiar imagen' : 'Elegir de la biblioteca';

    return <<<HTML
<div class="form-group field-image">
    <label>{$label}</label>
    {$help}

    <div class="image-preview-wrapper">
        <div class="image-preview{$preview_hidden_class}">
            <img src="{$img_src}" alt="Vista previa">
        </div>

        <div class="image-placeholder{$placeholder_hidden_class}">
            <i class="bi {$placeholder_icon}"></i>
            <span>{$placeholder_text}</span>
        </div>

        <div class="image-actions">
            <button type="button" class="btn btn-outline js-abrir-biblioteca">
                <i class="bi bi-images"></i> <span class="js-texto-elegir">{$texto_boton}</span>
            </button>
            <button type="button" class="btn btn-danger btn-remove-image{$preview_hidden_class}" title="Quitar imagen">
                <i class="bi bi-x-circle"></i> Quitar
            </button>
            <input type="hidden" name="{$name_path}" value="{$val}">
        </div>
    </div>
</div>
HTML;
}

/**
 * Ahora solo valida la ruta que mandó el selector: ya no hay subida que
 * procesar (eso vive en biblioteca_guardar_subida()).
 */
function field_image_parse($raw, array $config) {
    $old_val = $config['_old_value'] ?? '';

    // El input oculto siempre viaja con el HTML del campo, también dentro de un
    // repeater donde los índices bailan al eliminar items. Por eso manda él y
    // no $old_val, que ahí se desincroniza.
    if (!is_string($raw)) return $old_val;

    // "Quitar imagen": el JS vacía el oculto.
    if ($raw === '') return '';

    // Enlaces externos: se dejan pasar tal cual, no son de la biblioteca.
    if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) return $raw;

    // Cualquier otra cosa tiene que ser una imagen de la biblioteca. Esto corta
    // que alguien edite el HTML y meta una ruta arbitraria en el contenido.
    if (biblioteca_ruta_valida($raw)) {
        return biblioteca_ruta(biblioteca_nombre_de_ruta($raw));
    }

    return $old_val;
}
