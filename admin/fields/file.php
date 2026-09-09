<?php
// admin/fields/file.php
// Campo de subida de archivos (PDF). Mismo patrón de seguridad que image.php:
// whitelist de extensión + verificación de MIME real + límite de tamaño +
// nombre aleatorio al guardar (nunca el nombre original del archivo).

function field_file_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';

    $has_file = !empty($val);
    $file_name = $has_file ? basename($val) : '';

    $physical_exists = false;
    if ($has_file) {
        $physical_path = dirname(__DIR__, 2) . '/' . ltrim($val, '/');
        $physical_exists = file_exists($physical_path);
    }
    $show_current = $has_file && $physical_exists;

    $inputId = 'file_' . md5($name_path . rand());
    $btn_label = $show_current ? 'Cambiar PDF' : 'Subir PDF';

    $current_html = $show_current
        ? '<a href="../' . $val . '" target="_blank" class="file-current-link"><i class="bi bi-file-earmark-pdf-fill"></i> ' . htmlspecialchars($file_name, ENT_QUOTES, 'UTF-8') . '</a>'
        : '<span class="file-placeholder"><i class="bi bi-file-earmark"></i> Ningún archivo seleccionado</span>';

    return <<<HTML
<div class="form-group field-file">
    <label>{$label}</label>
    {$help}

    <div class="file-preview-wrapper">
        <div class="file-current">{$current_html}</div>

        <div class="file-actions">
            <label class="btn btn-outline" for="{$inputId}">
                <i class="bi bi-folder-fill"></i> {$btn_label}
            </label>
            <button type="button" class="btn btn-danger btn-remove-file">
                <i class="bi bi-x-circle"></i> Quitar
            </button>
            <input type="file" id="{$inputId}" name="{$name_path}[file]" accept="application/pdf" class="is-hidden">
            <input type="hidden" name="{$name_path}" value="{$val}">
        </div>
    </div>
</div>
HTML;
}

function field_file_parse($raw, array $config) {
    $name_path = $config['name_path'] ?? '';
    $old_val = $config['_old_value'] ?? '';

    if (!$name_path) return $old_val;

    $file_key = $name_path . '[file]';

    // 1. Si hay una subida válida, la procesamos con validación estricta
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$file_key]['tmp_name'];
        $original_name = basename($_FILES[$file_key]['name']);
        $file_size = (int)($_FILES[$file_key]['size'] ?? 0);

        // A. Whitelist estricta de extensión
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $_SESSION['flash_message'] = "Extensión de archivo '.{$ext}' no permitida. Solo se aceptan PDF.";
            $_SESSION['flash_type'] = 'error';
            return $old_val;
        }

        // B. Verificación del tipo MIME real con finfo (no confiar en la extensión)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $tmp_name) : false;
        if ($finfo) finfo_close($finfo);

        if ($mime !== 'application/pdf') {
            $_SESSION['flash_message'] = "Tipo de archivo inválido ({$mime}). El archivo no parece ser un PDF válido.";
            $_SESSION['flash_type'] = 'error';
            return $old_val;
        }

        // C. Límite de tamaño (mismo tope configurado para imágenes)
        $config_sys = file_exists(__DIR__ . '/../config.php') ? (require __DIR__ . '/../config.php') : [];
        $max_mb = (int)($config_sys['max_upload_mb'] ?? 32);
        $max_bytes = $max_mb * 1024 * 1024;
        if ($file_size > $max_bytes) {
            $_SESSION['flash_message'] = "El archivo supera el tamaño máximo permitido de {$max_mb} MB.";
            $_SESSION['flash_type'] = 'error';
            return $old_val;
        }

        // D. Guardar en docs/ con nombre aleatorio (nunca el nombre original)
        $upload_dir = dirname(__DIR__, 2) . '/docs/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $new_name = bin2hex(random_bytes(16)) . '.pdf';
        $dest = $upload_dir . $new_name;

        if (move_uploaded_file($tmp_name, $dest)) {
            return 'docs/' . $new_name;
        }
    }

    // 2. Si el usuario presionó "Quitar", el JS vacía el campo oculto
    if (is_string($raw) && $raw === '') {
        return '';
    }

    // 3. Si el campo oculto trae un valor (el archivo actual), confiamos en él
    if (is_string($raw) && !empty($raw)) {
        return $raw;
    }

    // Fallback de seguridad
    return $old_val;
}
