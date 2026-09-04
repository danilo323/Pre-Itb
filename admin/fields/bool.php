<?php
// admin/fields/bool.php

function field_bool_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $checked = $value ? 'checked' : '';
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';
    
    return <<<HTML
<div class="field-group field-bool">
    <label>
        <input type="hidden" name="{$name_path}" value="0">
        <input type="checkbox" name="{$name_path}" value="1" {$checked}>
        {$label}
    </label>
    {$help}
</div>
HTML;
}

function field_bool_parse($raw, array $config) {
    // Retorna true/false limpio
    return (int)$raw === 1;
}
