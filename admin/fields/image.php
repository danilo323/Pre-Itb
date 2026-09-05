<?php
// admin/fields/image.php

function field_image_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';
    
    $has_image = !empty($val);
    $preview_style = $has_image ? '' : 'display: none;';
    $placeholder_style = $has_image ? 'display: none;' : '';
    
    // Corregir la ruta de la imagen para que siempre cargue desde la raíz (/) si es relativa
    $img_src = $val;
    if ($has_image && strpos($img_src, 'http') !== 0 && strpos($img_src, '/') !== 0) {
        $img_src = '/' . $img_src;
    }
    
    // Un ID único por si hay varios campos de imagen
    $inputId = 'file_' . md5($name_path . rand());
    
    return <<<HTML
<div class="form-group field-image">
    <label>{$label}</label>
    {$help}
    
    <div class="image-preview-wrapper">
        <!-- Preview si hay imagen -->
        <div class="image-preview" style="{$preview_style}">
            <img src="{$img_src}" alt="Preview">
            <div style="font-size: 12px; color: #64748b; margin-top: 8px;">Ruta actual: {$val}</div>
        </div>
        
        <!-- Placeholder si no hay imagen -->
        <div class="image-placeholder" style="{$placeholder_style}; text-align: center; color: var(--text-muted); padding: 20px;">
            <i class="bi bi-image" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
            <span>Ninguna imagen seleccionada</span>
        </div>

        <div class="image-actions">
            <label class="btn btn-outline" for="{$inputId}" style="cursor: pointer;">
                <i class="bi bi-folder-fill"></i> Seleccionar archivo
            </label>
            <button type="button" class="btn btn-danger btn-remove-image" style="{$preview_style}" title="Eliminar imagen">
                <i class="bi bi-trash-fill"></i> Eliminar
            </button>
            <input type="file" id="{$inputId}" name="{$name_path}_file" accept="image/*" style="display: none;">
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
    
    $file_key = $name_path . '_file';
    
    // Si hay una subida válida
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
    
    // Si no subieron nada, mantenemos la imagen anterior
    return $old_val;
}
