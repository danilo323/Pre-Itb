<?php
// admin/fields/image.php

function field_image_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';
    
    $has_image = !empty($val);
    $preview_style = $has_image ? '' : 'display: none;';
    $placeholder_style = $has_image ? 'display: none;' : '';
    
    // Un ID único por si hay varios campos de imagen
    $inputId = 'file_' . md5($name_path . rand());
    
    return <<<HTML
<div class="field-group field-image">
    <label>{$label}</label>
    {$help}
    
    <div class="image-upload-box">
        <!-- Preview si hay imagen -->
        <div class="image-preview" style="{$preview_style}">
            <img src="{$val}" alt="Preview">
            <span class="image-current-path">Actual: {$val}</span>
        </div>
        
        <!-- Placeholder si no hay imagen -->
        <div class="image-placeholder" style="{$placeholder_style}">
            <i>🖼️</i>
            <span>Ninguna imagen seleccionada</span>
        </div>

        <div class="image-upload-actions">
            <label class="btn-upload" for="{$inputId}">
                Seleccionar archivo
            </label>
            <button type="button" class="btn-remove-image" style="{$preview_style}" title="Eliminar imagen" aria-label="Eliminar imagen">
                <i class="fas fa-trash"></i>
            </button>
            <input type="file" id="{$inputId}" name="{$name_path}_file" accept="image/*" class="file-input-hidden">
            <input type="hidden" name="{$name_path}" value="{$val}">
        </div>
    </div>
</div>
HTML;
}

function field_image_parse($raw, array $config) {
    // El valor que llega es la ruta de la imagen actual (del hidden)
    // La subida real del archivo la maneja upload.php (Persona 3)
    // Aquí solo sanitizamos la ruta
    return trim(htmlspecialchars((string)$raw, ENT_QUOTES, 'UTF-8'));
}
