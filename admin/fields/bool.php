<?php
// admin/fields/bool.php

function field_bool_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? $name_path, ENT_QUOTES, 'UTF-8');
    $checked = $value ? 'checked' : '';
    $help = isset($config['help']) ? '<small>' . htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') . '</small>' : '';

    // GRUPO EXCLUSIVO (opcional).
    // Varios interruptores pueden declarar en el schema que pertenecen al mismo
    // grupo: entonces se comportan como una sola elección, exactamente uno
    // encendido. Al encender uno, los demás del grupo se apagan solos; si se
    // apaga el último que quedaba, se enciende el marcado como 'exclusive_default'.
    // Quién forma cada grupo lo decide el SCHEMA, no este archivo: aquí solo se
    // vuelcan los datos al HTML y admin/assets/admin.js los hace cumplir.
    $exclusivo = '';
    if (!empty($config['exclusive_group'])) {
        $grupo = htmlspecialchars((string)$config['exclusive_group'], ENT_QUOTES, 'UTF-8');
        $exclusivo = ' data-exclusivo="' . $grupo . '"';
        if (!empty($config['exclusive_default'])) {
            $exclusivo .= ' data-exclusivo-defecto="1"';
        }
    }

    return <<<HTML
<div class="field-group field-bool">
    <label class="bool-toggle-label">
        <input type="hidden" name="{$name_path}" value="0">
        <input type="checkbox" name="{$name_path}" value="1" {$checked} class="bool-toggle-input"{$exclusivo}>
        <span class="bool-toggle-switch"></span>
        <span class="bool-toggle-text">{$label}</span>
    </label>
    {$help}
</div>
HTML;
}

function field_bool_parse($raw, array $config) {
    // Retorna true/false limpio
    return (int)$raw === 1;
}
