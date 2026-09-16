<?php
// Selector visual de coordenadas para los campus del pie de página.
function field_map_picker_render(string $name_path, $value, array $config): string {
    $label = htmlspecialchars($config['label'] ?? 'Ubicación en el mapa', ENT_QUOTES, 'UTF-8');
    $help = htmlspecialchars($config['help'] ?? 'Busca una dirección o haz clic en el mapa para colocar el pin.', ENT_QUOTES, 'UTF-8');
    $lat = is_array($value) && isset($value['lat']) && is_numeric($value['lat']) ? (float)$value['lat'] : -2.170998;
    $lng = is_array($value) && isset($value['lng']) && is_numeric($value['lng']) ? (float)$value['lng'] : -79.922359;
    $has_point = is_array($value) && isset($value['lat'], $value['lng']) && is_numeric($value['lat']) && is_numeric($value['lng']);
    $id = 'map_picker_' . md5($name_path . random_int(1, PHP_INT_MAX));
    $input_id = $id . '_value';
    $search_id = $id . '_search';
    $json = $has_point ? json_encode(['lat' => $lat, 'lng' => $lng]) : '';
    $safe_json = htmlspecialchars($json ?: '', ENT_QUOTES, 'UTF-8');
    $point_state = $has_point ? '1' : '0';

    return <<<HTML
<div class="form-group field-map-picker js-map-picker" data-lat="{$lat}" data-lng="{$lng}" data-has-point="{$point_state}">
    <label>{$label}</label><small>{$help}</small>
    <div class="field-map-picker__search">
        <input id="{$search_id}" type="text" class="form-control js-map-picker-search" placeholder="Buscar dirección o lugar">
        <button type="button" class="btn btn-outline js-map-picker-search-button">Buscar</button>
    </div>
    <div id="{$id}" class="field-map-picker__map"></div>
    <small id="{$id}_status" class="field-map-picker__status">Haz clic en el mapa para fijar la ubicación.</small>
    <input id="{$input_id}" class="js-map-picker-value" type="hidden" name="{$name_path}" value="{$safe_json}">
</div>
HTML;
}

function field_map_picker_parse($raw, array $config) {
    if (!is_string($raw) || trim($raw) === '') return [];
    $point = json_decode($raw, true);
    if (!is_array($point) || !isset($point['lat'], $point['lng']) || !is_numeric($point['lat']) || !is_numeric($point['lng'])) return [];
    $lat = (float)$point['lat']; $lng = (float)$point['lng'];
    if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) return [];
    return ['lat' => $lat, 'lng' => $lng];
}
