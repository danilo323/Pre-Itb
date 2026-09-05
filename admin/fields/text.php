<?php
// admin/fields/text.php

function field_text_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';
    $readonly = !empty($config['readonly']) ? 'readonly' : '';
    
    return <<<HTML
<div class="field-group">
    <label>{$label}</label>
    {$help}
    <input type="text" name="{$name_path}" value="{$val}" class="form-control" {$readonly}>
</div>
HTML;
}

function field_text_parse($raw, array $config) {
    // Si es readonly, no aceptar cambios del POST (devolver el valor original)
    if (!empty($config['readonly'])) return $config['_original'] ?? '';
    return trim((string)$raw);
}
