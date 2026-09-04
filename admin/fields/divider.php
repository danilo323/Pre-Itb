<?php
function field_divider_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? '', ENT_QUOTES, 'UTF-8');
    return "<div style='margin: 24px 0 12px 0; padding-bottom: 6px; border-bottom: 1px solid rgba(255,255,255,0.1); color: var(--panel-accent); font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;'>{$label}</div>";
}

function field_divider_parse($raw_value, array $config) {
    return null; // Un divisor es puramente visual, no guarda datos
}
