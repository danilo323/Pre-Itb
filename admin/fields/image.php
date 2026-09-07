<?php
// admin/fields/image.php

function field_image_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';
    
    $has_image = !empty($val);
    
    // Corregir la ruta de la imagen para el panel de administración
    $img_src = $val;
    if ($has_image && strpos($img_src, 'http') !== 0 && strpos($img_src, '../') !== 0) {
        $img_src = '../' . ltrim($img_src, '/');
    }

    // Verificar si la imagen realmente existe en disco (si es local)
    $image_exists = true;
    if ($has_image && strpos($val, 'http') !== 0) {
        $physical_path = dirname(__DIR__, 2) . '/' . ltrim($val, '/');
        if (!file_exists($physical_path)) {
            $image_exists = false;
        }
    }

    $show_preview = $has_image && $image_exists;
    $preview_hidden_class = $show_preview ? '' : ' is-hidden';
    $placeholder_hidden_class = $show_preview ? ' is-hidden' : '';

    // Un ID único por si hay varios campos de imagen
    $inputId = 'file_' . md5($name_path . rand());

    // Texto del placeholder
    $placeholder_icon = 'bi-image';
    $placeholder_text = "Ninguna imagen seleccionada";

    return <<<HTML
<div class="form-group field-image">
    <label>{$label}</label>
    {$help}

    <div class="image-preview-wrapper">
        <!-- Preview si hay imagen -->
        <div class="image-preview{$preview_hidden_class}">
            <img src="{$img_src}" alt="Preview">
        </div>

        <!-- Placeholder si no hay imagen o si está rota -->
        <div class="image-placeholder{$placeholder_hidden_class}">
            <i class="bi {$placeholder_icon}"></i>
            <span>{$placeholder_text}</span>
        </div>

        <div class="image-actions">
            <label class="btn btn-outline" for="{$inputId}">
                <i class="bi bi-folder-fill"></i> Cambiar imagen
            </label>
            <button type="button" class="btn btn-danger btn-remove-image{$preview_hidden_class}" title="Quitar imagen">
                <i class="bi bi-x-circle"></i> Quitar
            </button>
            <input type="file" id="{$inputId}" name="{$name_path}[file]" accept="image/*" class="is-hidden">
            <input type="hidden" name="{$name_path}" value="{$val}">
        </div>
    </div>
</div>
HTML;
}

function field_image_parse($raw, array $config) {
    // El motor genérico inyecta 'name_path' y '_old_value'
    $name_path = $config['name_path'] ?? '';
    $old_val = $config['_old_value'] ?? '';
    
    // Si no tenemos nombre del campo, devolvemos el valor por defecto/viejo
    if (!$name_path) return $old_val;
    
    $file_key = $name_path . '[file]';
    
    // 1. Si hay una subida válida, la procesamos con validación estricta (Anti-RCE)
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$file_key]['tmp_name'];
        $original_name = basename($_FILES[$file_key]['name']);
        $file_size = (int)($_FILES[$file_key]['size'] ?? 0);

        // A. Whitelist estricta de extensiones
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_exts, true)) {
            $_SESSION['flash_message'] = "Extensión de archivo '.{$ext}' no permitida. Solo imágenes (jpg, png, webp, gif, svg).";
            $_SESSION['flash_type'] = 'error';
            return $old_val;
        }

        // B. Verificación del tipo MIME real con finfo (no confiar en la extensión)
        $allowed_mimes = [
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'image/svg+xml',
            'text/xml',
            'image/svg'
        ];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $tmp_name) : false;
        if ($finfo) finfo_close($finfo);

        if (!$mime || !in_array($mime, $allowed_mimes, true)) {
            $_SESSION['flash_message'] = "Tipo de archivo inválido ({$mime}). El archivo no parece ser una imagen válida.";
            $_SESSION['flash_type'] = 'error';
            return $old_val;
        }

        // C. Validación de tamaño máximo en PHP (32MB para aceptar fotos de cámara antes de comprimir)
        $config_sys = file_exists(__DIR__ . '/../config.php') ? (require __DIR__ . '/../config.php') : [];
        $max_mb = (int)($config_sys['max_upload_mb'] ?? 32);
        $max_bytes = $max_mb * 1024 * 1024;
        if ($file_size > $max_bytes) {
            $_SESSION['flash_message'] = "La imagen supera el tamaño máximo permitido de {$max_mb} MB.";
            $_SESSION['flash_type'] = 'error';
            return $old_val;
        }

        // D. Renombrado a hash criptográfico aleatorio (nunca el nombre original)
        $upload_dir = dirname(__DIR__, 2) . '/img/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        // E. Compresión automática con GD (excepto SVG)
        $compressible = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($ext, $compressible, true) && extension_loaded('gd')) {
            // Mover temporalmente para poder procesarla
            $tmp_dest = $upload_dir . 'tmp_' . bin2hex(random_bytes(8)) . '.' . $ext;
            if (!move_uploaded_file($tmp_name, $tmp_dest)) {
                return $old_val;
            }

            // Crear recurso GD desde el archivo original
            $src = null;
            switch ($mime) {
                case 'image/jpeg':
                case 'image/pjpeg':
                    $src = @imagecreatefromjpeg($tmp_dest);
                    break;
                case 'image/png':
                    $src = @imagecreatefrompng($tmp_dest);
                    break;
                case 'image/webp':
                    $src = @imagecreatefromwebp($tmp_dest);
                    break;
                case 'image/gif':
                    $src = @imagecreatefromgif($tmp_dest);
                    break;
            }

            if (!$src) {
                // GD no pudo leer la imagen; guardar sin comprimir
                $new_name = bin2hex(random_bytes(16)) . '.' . $ext;
                $dest = $upload_dir . $new_name;
                @rename($tmp_dest, $dest);
                return 'img/' . $new_name;
            }

            // Redimensionar si supera 1920px de ancho
            $orig_w = imagesx($src);
            $orig_h = imagesy($src);
            $max_width = 1920;

            if ($orig_w > $max_width) {
                $ratio = $max_width / $orig_w;
                $new_w = $max_width;
                $new_h = (int)round($orig_h * $ratio);

                $resized = imagecreatetruecolor($new_w, $new_h);
                // Preservar transparencia para PNG
                if ($ext === 'png') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
                    imagefill($resized, 0, 0, $transparent);
                }
                imagecopyresampled($resized, $src, 0, 0, 0, 0, $new_w, $new_h, $orig_w, $orig_h);
                $src = $resized;
            }

            // Guardar comprimida como JPEG (máxima compresión) o PNG si tiene transparencia
            if ($ext === 'png') {
                $new_name = bin2hex(random_bytes(16)) . '.png';
                $dest = $upload_dir . $new_name;
                imagesavealpha($src, true);
                imagepng($src, $dest, 8); // Compresión PNG nivel 8 (0-9)
            } else {
                // Convertir todo lo demás a JPEG para máxima compresión
                $new_name = bin2hex(random_bytes(16)) . '.jpg';
                $dest = $upload_dir . $new_name;
                imagejpeg($src, $dest, 82); // Calidad 82% — buen balance tamaño/calidad
            }

            @unlink($tmp_dest); // Eliminar archivo temporal

            return 'img/' . $new_name;

        } else {
            // SVG u otros: guardar sin comprimir
            $new_name = bin2hex(random_bytes(16)) . '.' . $ext;
            $dest = $upload_dir . $new_name;

            if (move_uploaded_file($tmp_name, $dest)) {
                return 'img/' . $new_name;
            }
        }
    }
    
    // 2. Si el usuario presionó "Quitar", el JS vacía el campo oculto
    if (is_string($raw) && $raw === '') {
        return '';
    }
    
    // 3. Si el campo oculto trae un valor (la imagen actual), confiamos en él.
    // Esto es crucial para los repeaters, porque al eliminar items los índices cambian
    // y $old_val se desincroniza, pero el hidden input (que viaja con el HTML del item) siempre es correcto.
    if (is_string($raw) && !empty($raw)) {
        return $raw;
    }
    
    // Fallback de seguridad
    return $old_val;
}
