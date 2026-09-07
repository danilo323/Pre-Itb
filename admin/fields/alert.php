<?php
// admin/fields/alert.php

function field_alert_render(string $name_path, $value, array $config): string {
    $label = $config['label'] ?? '';
    // Permite HTML en el label para que podamos usar <strong>, etc., pero sin romper el HTML.
    $alert_type = $config['alert_type'] ?? 'info'; // info, warning, danger, success
    
    // Icono dependiendo del tipo de alerta (usando Bootstrap Icons que ya tiene el panel)
    $icon = 'bi-info-circle-fill';
    if ($alert_type === 'warning') $icon = 'bi-exclamation-triangle-fill';
    if ($alert_type === 'danger') $icon = 'bi-x-octagon-fill';
    if ($alert_type === 'success') $icon = 'bi-check-circle-fill';

    // Colores por defecto (info)
    $bg = '#e0f2fe';
    $border = '#bae6fd';
    $color = '#0369a1';
    
    if ($alert_type === 'warning') { $bg = '#fef3c7'; $border = '#fde68a'; $color = '#b45309'; }
    if ($alert_type === 'danger') { $bg = '#fee2e2'; $border = '#fecaca'; $color = '#b91c1c'; }
    if ($alert_type === 'success') { $bg = '#dcfce7'; $border = '#bbf7d0'; $color = '#15803d'; }

    return "
    <div style='background-color: {$bg}; border: 1px solid {$border}; color: {$color}; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 0.9rem; display: flex; align-items: flex-start; gap: 12px;'>
        <i class='bi {$icon}' style='font-size: 1.25rem; line-height: 1;'></i>
        <div style='line-height: 1.5;'>
            {$label}
        </div>
    </div>";
}

function field_alert_parse($raw_value, array $config) {
    return null; // El alert es puramente visual, no guarda datos
}
