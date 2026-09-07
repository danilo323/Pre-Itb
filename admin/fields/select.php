<?php
// admin/fields/select.php

function field_select_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';
    $options = $config['options'] ?? [];
    
    $options_html = '<option value="">-- Seleccionar --</option>';
    foreach ($options as $opt_value => $opt_label) {
        $safe_val = htmlspecialchars($opt_value, ENT_QUOTES, 'UTF-8');
        $safe_label = htmlspecialchars($opt_label, ENT_QUOTES, 'UTF-8');
        $selected = ($value === $opt_value) ? 'selected' : '';
        $options_html .= "<option value=\"{$safe_val}\" {$selected}>{$safe_label}</option>";
    }
    
    return <<<HTML
<div class="field-group field-select">
    <label>{$label}</label>
    {$help}
    <select name="{$name_path}">
        {$options_html}
    </select>
</div>
HTML;
}

function field_select_parse($raw, array $config) {
    $allowed = array_keys($config['options'] ?? []);
    $val = trim((string)$raw);
    // Whitelist: solo aceptar valores que estén en las opciones definidas
    return in_array($val, $allowed, true) ? $val : '';
}
