<?php
// admin/fields/campus_map_picker.php
// Mapa unificado de campus: un solo mapa con selector para editar ubicaciones individualmente.

function field_campus_map_picker_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? 'Ubicación de Campus en el Mapa', ENT_QUOTES, 'UTF-8');
    $help  = htmlspecialchars($config['help'] ?? 'Selecciona un campus y haz clic en el mapa para fijar su ubicación.', ENT_QUOTES, 'UTF-8');
    $json  = is_array($value) ? json_encode($value) : '{}';
    $safe  = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');

    return <<<HTML
<div class="form-group field-campus-map js-campus-map-picker">
    <label>{$label}</label><small>{$help}</small>
    <div class="cmp-active js-campus-active" hidden>
        <span class="cmp-active__label">Editando:</span>
        <span class="cmp-active__name js-campus-active-name"></span>
        <button type="button" class="btn btn-sm btn-outline js-campus-deselect">Cancelar selección</button>
    </div>
    <div class="cmp-search">
        <input type="text" class="form-control js-campus-search" placeholder="Buscar dirección o lugar">
        <button type="button" class="btn btn-outline js-campus-search-btn">Buscar</button>
    </div>
    <div class="cmp-map js-campus-map"></div>
    <small class="cmp-status js-campus-status">Selecciona un campus y haz clic en el mapa para fijar su pin. El pin no se arrastra.</small>
    <input class="js-campus-locations-value" type="hidden" name="{$name_path}" value="{$safe}">
</div>
HTML;
}

function field_campus_map_picker_parse($raw, array $config) {
    if (!is_string($raw) || trim($raw) === '') return [];
    $data = json_decode($raw, true);
    if (!is_array($data)) return [];
    $clean = [];
    foreach ($data as $idx => $loc) {
        if (is_array($loc) && isset($loc['lat'], $loc['lng'])
            && is_numeric($loc['lat']) && is_numeric($loc['lng'])) {
            $lat = (float)$loc['lat'];
            $lng = (float)$loc['lng'];
            if ($lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                $clean[(string)$idx] = ['lat' => $lat, 'lng' => $lng];
            }
        }
    }
    return $clean;
}
