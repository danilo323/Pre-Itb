<?php
function field_divider_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? '', ENT_QUOTES, 'UTF-8');
    return "<div class='field-divider'>{$label}</div>";
}

function field_divider_parse($raw_value, array $config) {
    return null; // Un divisor es puramente visual, no guarda datos
}
