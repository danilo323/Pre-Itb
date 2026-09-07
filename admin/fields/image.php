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
    
    // 1. Si hay una subida válida, la procesamos
    if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$file_key]['tmp_name'];
        $name     = basename($_FILES[$file_key]['name']);
        
        $upload_dir = dirname(__DIR__, 2) . '/img/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        
        $new_name = time() . '_' . preg_replace('/[^a-zA-Z0-9.\-_]/', '', $name);
        $dest     = $upload_dir . $new_name;
        
        if (move_uploaded_file($tmp_name, $dest)) {
            return 'img/' . $new_name;
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
