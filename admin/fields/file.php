<?php
// admin/fields/file.php
// Campo genérico para documentos descargables (PDF, Word). Guarda en /docs.

function field_file_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';
    $accept = htmlspecialchars($config['accept'] ?? '.pdf,.doc,.docx', ENT_QUOTES, 'UTF-8');

    $config_sys = file_exists(__DIR__ . '/../config.php') ? (require __DIR__ . '/../config.php') : [];
    $max_mb = (int)($config_sys['max_upload_mb'] ?? 32);

    $has_file = !empty($value);
    $physical_path = $has_file ? dirname(__DIR__, 2) . '/' . ltrim($value, '/') : '';
    $file_exists = $has_file && (strpos($value, 'http') === 0 || file_exists($physical_path));

    if ($file_exists) {
        $ext = strtoupper(pathinfo($value, PATHINFO_EXTENSION));
        $size_mb = (strpos($value, 'http') !== 0 && file_exists($physical_path))
            ? round(filesize($physical_path) / 1048576, 1)
            : null;
        $name_text = htmlspecialchars(basename($value), ENT_QUOTES, 'UTF-8');
        $meta_text = $size_mb !== null ? "{$ext} · {$size_mb} MB" : $ext;
        $preview_link = '<a href="' . $val . '" target="_blank" rel="noopener" class="file-preview-link">Ver archivo actual</a>';
        $select_label = 'Reemplazar PDF';
        $remove_disabled = '';
    } else {
        $name_text = 'Ningún PDF seleccionado';
        $meta_text = "PDF · hasta {$max_mb} MB";
        $preview_link = '';
        $select_label = 'Seleccionar PDF';
        $remove_disabled = 'disabled';
    }

    $inputId = 'file_' . md5($name_path . rand());

    return <<<HTML
<div class="form-group field-file">
    <label>{$label}</label>
    {$help}

    <div class="file-upload-card">
        <div class="file-upload-icon"><i class="bi bi-file-earmark-pdf-fill"></i></div>
        <div class="file-summary">
            <span class="file-summary-name">{$name_text}</span>
            <span class="file-summary-meta">{$meta_text}</span>
            {$preview_link}
        </div>
        <div class="file-actions">
            <label class="btn btn-outline file-select-label" for="{$inputId}">{$select_label}</label>
            <button type="button" class="btn btn-danger btn-remove-file" {$remove_disabled}>
                <i class="bi bi-x-circle"></i> Quitar
            </button>
            <input type="file" id="{$inputId}" name="{$name_path}[file]" accept="{$accept}" class="is-hidden">
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

    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$file_key]['tmp_name'];
        $original_name = basename($_FILES[$file_key]['name']);
        $file_size = (int)($_FILES[$file_key]['size'] ?? 0);

        // A. Whitelist estricta de extensiones (configurable por campo, PDF/Word por defecto)
        $allowed_exts = $config['allowed_exts'] ?? ['pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_exts, true)) {
            $lista = implode(', ', $allowed_exts);
            $_SESSION['flash_message'] = "Extensión de archivo '.{$ext}' no permitida. Solo: {$lista}.";
            $_SESSION['flash_type'] = 'error';
            return $old_val;
        }

        // B. Verificación del tipo MIME real con finfo (no confiar en la extensión)
        $allowed_mimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];
        $mime = false;
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = $finfo ? finfo_file($finfo, $tmp_name) : false;
            if ($finfo) finfo_close($finfo);
        } elseif (function_exists('mime_content_type')) {
            $mime = @mime_content_type($tmp_name);
        }

        if ($mime !== false && !in_array($mime, $allowed_mimes, true)) {
            $_SESSION['flash_message'] = "Tipo de archivo inválido ({$mime}). El archivo no parece ser un documento válido.";
            $_SESSION['flash_type'] = 'error';
            return $old_val;
        }

        // C. Validación de tamaño máximo en PHP
        $config_sys = file_exists(__DIR__ . '/../config.php') ? (require __DIR__ . '/../config.php') : [];
        $max_mb = (int)($config_sys['max_upload_mb'] ?? 32);
        $max_bytes = $max_mb * 1024 * 1024;
        if ($file_size > $max_bytes) {
            $_SESSION['flash_message'] = "El archivo supera el tamaño máximo permitido de {$max_mb} MB.";
            $_SESSION['flash_type'] = 'error';
            return $old_val;
        }

        // D. Renombrado a hash criptográfico aleatorio (nunca el nombre original)
        $upload_dir = dirname(__DIR__, 2) . '/docs/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $new_name = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = $upload_dir . $new_name;

        if (move_uploaded_file($tmp_name, $dest)) {
            return 'docs/' . $new_name;
        }
    }

    // 2. Si el usuario presionó "Quitar", el JS vacía el campo oculto
    if (is_string($raw) && $raw === '') {
        return '';
    }

    // 3. Si el campo oculto trae un valor (el archivo actual), confiamos en él.
    if (is_string($raw) && !empty($raw)) {
        return $raw;
    }

    return $old_val;
}
