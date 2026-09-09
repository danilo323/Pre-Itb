<?php
// admin/fields/audio.php

function field_audio_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';

    $has_audio = !empty($val);

    // Corregir la ruta del audio para el panel de administración
    $audio_src = $val;
    if ($has_audio && strpos($audio_src, 'http') !== 0 && strpos($audio_src, '../') !== 0) {
        $audio_src = '../' . ltrim($audio_src, '/');
    }

    // Verificar si el archivo realmente existe en disco (si es local)
    $audio_exists = true;
    if ($has_audio && strpos($val, 'http') !== 0) {
        $physical_path = dirname(__DIR__, 2) . '/' . ltrim($val, '/');
        if (!file_exists($physical_path)) {
            $audio_exists = false;
        }
    }

    $show_preview = $has_audio && $audio_exists;
    $preview_hidden_class = $show_preview ? '' : ' is-hidden';
    $placeholder_hidden_class = $show_preview ? ' is-hidden' : '';

    $inputId = 'file_' . md5($name_path . rand());

    return <<<HTML
<div class="form-group field-audio">
    <label>{$label}</label>
    {$help}

    <div class="audio-preview-wrapper">
        <div class="audio-preview{$preview_hidden_class}">
            <audio controls src="{$audio_src}"></audio>
        </div>

        <div class="audio-placeholder{$placeholder_hidden_class}">
            <i class="bi bi-music-note-beamed"></i>
            <span>Ningún audio seleccionado</span>
        </div>

        <div class="audio-actions">
            <label class="btn btn-outline" for="{$inputId}">
                <i class="bi bi-folder-fill"></i> Cambiar audio
            </label>
            <button type="button" class="btn btn-danger btn-remove-audio{$preview_hidden_class}" title="Quitar audio">
                <i class="bi bi-x-circle"></i> Quitar
            </button>
            <input type="file" id="{$inputId}" name="{$name_path}[file]" accept="audio/*" class="is-hidden">
            <input type="hidden" name="{$name_path}" value="{$val}">
        </div>
    </div>
</div>
HTML;
}

function field_audio_parse($raw, array $config) {
    // El motor genérico inyecta 'name_path' y '_old_value'
    $name_path = $config['name_path'] ?? '';
    $old_val = $config['_old_value'] ?? '';

    if (!$name_path) return $old_val;

    $file_key = $name_path . '[file]';

    // 1. Si hay una subida válida, la procesamos con validación estricta (Anti-RCE)
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$file_key]['tmp_name'];
        $original_name = basename($_FILES[$file_key]['name']);
        $file_size = (int)($_FILES[$file_key]['size'] ?? 0);

        // A. Whitelist estricta de extensiones
        $allowed_exts = ['mp3', 'ogg', 'wav', 'm4a'];
        $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_exts, true)) {
            $_SESSION['flash_message'] = "Extensión de archivo '.{$ext}' no permitida. Solo audio (mp3, ogg, wav, m4a).";
            $_SESSION['flash_type'] = 'error';
            return $old_val;
        }

        // B. Verificación del tipo MIME real con finfo (no confiar en la extensión)
        $allowed_mimes = [
            'audio/mpeg',
            'audio/mp3',
            'audio/ogg',
            'audio/wav',
            'audio/x-wav',
            'audio/vnd.wave',
            'audio/mp4',
            'audio/x-m4a',
        ];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $tmp_name) : false;
        if ($finfo) finfo_close($finfo);

        if (!$mime || !in_array($mime, $allowed_mimes, true)) {
            $_SESSION['flash_message'] = "Tipo de archivo inválido ({$mime}). El archivo no parece ser un audio válido.";
            $_SESSION['flash_type'] = 'error';
            return $old_val;
        }

        // C. Validación de tamaño máximo en PHP
        $config_sys = file_exists(__DIR__ . '/../config.php') ? (require __DIR__ . '/../config.php') : [];
        $max_mb = (int)($config_sys['max_upload_mb'] ?? 32);
        $max_bytes = $max_mb * 1024 * 1024;
        if ($file_size > $max_bytes) {
            $_SESSION['flash_message'] = "El audio supera el tamaño máximo permitido de {$max_mb} MB.";
            $_SESSION['flash_type'] = 'error';
            return $old_val;
        }

        // D. Renombrado a hash criptográfico aleatorio (nunca el nombre original)
        $upload_dir = dirname(__DIR__, 2) . '/audio/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $new_name = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = $upload_dir . $new_name;

        if (move_uploaded_file($tmp_name, $dest)) {
            return 'audio/' . $new_name;
        }
    }

    // 2. Si el usuario presionó "Quitar", el JS vacía el campo oculto
    if (is_string($raw) && $raw === '') {
        return '';
    }

    // 3. Si el campo oculto trae un valor (el audio actual), confiamos en él.
    if (is_string($raw) && !empty($raw)) {
        return $raw;
    }

    // Fallback de seguridad
    return $old_val;
}
