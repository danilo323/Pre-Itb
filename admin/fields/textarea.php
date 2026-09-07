<?php
// admin/fields/textarea.php

function field_textarea_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';
    $readonly = !empty($config['readonly']) ? 'readonly' : '';
    $required = !empty($config['required']) ? 'required' : '';
    
    return <<<HTML
<div class="field-group">
    <label>{$label}</label>
    {$help}
    <textarea name="{$name_path}" rows="4" {$readonly} {$required}>{$val}</textarea>
</div>
HTML;
}

function field_textarea_parse($raw, array $config) {
    // Si es readonly, no aceptar cambios del POST (devolver el valor original)
    if (!empty($config['readonly'])) return $config['_original'] ?? '';
    return trim((string)$raw);
}
