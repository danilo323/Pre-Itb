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
    // Se conservan para compatibilidad con el bloque legado comentado abajo.
    $lat_js = json_encode($lat);
    $lng_js = json_encode($lng);
    $has_point_js = $has_point ? 'true' : 'false';

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
<!-- JavaScript moved to admin/assets/map_picker.js.
<script>
(function () {
    var map = L.map('{$id}').setView([{$lat_js}, {$lng_js}], {$has_point_js} ? 16 : 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);
    var marker = null, input = document.getElementById('{$input_id}'), status = document.getElementById('{$id}_status');
    function setPoint(lat, lng, zoom) {
        lat = Number(lat); lng = Number(lng);
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], {draggable:true}).addTo(map);
            marker.on('dragend', function () { var p = marker.getLatLng(); setPoint(p.lat, p.lng, false); });
        }
        input.value = JSON.stringify({lat: lat, lng: lng});
        status.textContent = 'Ubicación elegida: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
        if (zoom) map.setView([lat, lng], zoom);
    }
    if ({$has_point_js}) setPoint({$lat_js}, {$lng_js}, false);
    map.on('click', function (event) { setPoint(event.latlng.lat, event.latlng.lng, false); });
    document.querySelector('[data-map-search="{$id}"]').addEventListener('click', function () {
        var query = document.getElementById('{$search_id}').value.trim();
        if (!query) return;
        status.textContent = 'Buscando ubicación…';
        fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=' + encodeURIComponent(query), {headers:{'Accept':'application/json'}})
            .then(function (r) { return r.json(); }).then(function (results) {
                if (!results.length) { status.textContent = 'No se encontró esa ubicación. Señálala manualmente en el mapa.'; return; }
                setPoint(results[0].lat, results[0].lon, 17);
            }).catch(function () { status.textContent = 'No se pudo buscar. Señala la ubicación directamente en el mapa.'; });
    });
}());
</script>
-->
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
