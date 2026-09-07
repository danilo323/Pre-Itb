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

    return "
    <div class='field-alert field-alert-{$alert_type}'>
        <i class='bi {$icon}'></i>
        <div class='field-alert-body'>
            {$label}
        </div>
    </div>";
}

function field_alert_parse($raw_value, array $config) {
    return null; // El alert es puramente visual, no guarda datos
}
