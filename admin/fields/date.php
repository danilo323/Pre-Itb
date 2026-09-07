<?php
// admin/fields/date.php

function field_date_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $val = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';
    
    return <<<HTML
<div class="field-group field-date">
    <label>{$label}</label>
    {$help}
    <input type="date" name="{$name_path}" value="{$val}">
</div>
HTML;
}

function field_date_parse($raw, array $config) {
    $val = trim((string)$raw);
    // Validar formato YYYY-MM-DD
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
        return $val;
    }
    return '';
}
