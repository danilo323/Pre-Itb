<?php
// admin/fields/image.php

function field_image_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';
    
    // Mostrar preview si ya hay una imagen guardada
    $preview = '';
    if (!empty($val)) {
        $preview = "<div class='image-preview'><img src='{$val}' alt='Preview' style='max-width:200px;'><br><small>Actual: {$val}</small></div>";
    }
    
    return <<<HTML
<div class="field-group field-image">
    <label>{$label}</label>
    {$help}
    {$preview}
    <input type="file" name="{$name_path}_file" accept="image/*">
    <input type="hidden" name="{$name_path}" value="{$val}">
</div>
HTML;
}

function field_image_parse($raw, array $config) {
    // El valor que llega es la ruta de la imagen actual (del hidden)
    // La subida real del archivo la maneja upload.php (Persona 3)
    // Aquí solo sanitizamos la ruta
    return trim(htmlspecialchars((string)$raw, ENT_QUOTES, 'UTF-8'));
}
