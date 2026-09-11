document.addEventListener('DOMContentLoaded', function () {
    if (typeof L === 'undefined') return;
    document.querySelectorAll('.js-map-picker').forEach(function (picker) {
        const mapElement = picker.querySelector('.field-map-picker__map');
        const input = picker.querySelector('.js-map-picker-value');
        const search = picker.querySelector('.js-map-picker-search');
        const searchButton = picker.querySelector('.js-map-picker-search-button');
        const status = picker.querySelector('.field-map-picker__status');
        const lat = Number(picker.dataset.lat), lng = Number(picker.dataset.lng);
        const hasPoint = picker.dataset.hasPoint === '1';
        const map = L.map(mapElement).setView([lat, lng], hasPoint ? 16 : 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:19, attribution:'&copy; OpenStreetMap'}).addTo(map);
        let marker = null;
        const setPoint = function (pointLat, pointLng, zoom) {
            pointLat = Number(pointLat); pointLng = Number(pointLng);
            if (marker) marker.setLatLng([pointLat, pointLng]);
            else { marker = L.marker([pointLat, pointLng], {draggable:true}).addTo(map); marker.on('dragend', function () { const point = marker.getLatLng(); setPoint(point.lat, point.lng); }); }
            input.value = JSON.stringify({lat:pointLat, lng:pointLng});
            status.textContent = 'Ubicación elegida: ' + pointLat.toFixed(6) + ', ' + pointLng.toFixed(6);
            if (zoom) map.setView([pointLat, pointLng], zoom);
        };
        if (hasPoint) setPoint(lat, lng);
        map.on('click', event => setPoint(event.latlng.lat, event.latlng.lng));
        searchButton.addEventListener('click', function () {
            const query = search.value.trim(); if (!query) return;
            status.textContent = 'Buscando ubicación…';
            fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=' + encodeURIComponent(query), {headers:{Accept:'application/json'}})
                .then(response => response.json()).then(results => { if (!results.length) { status.textContent = 'No se encontró esa ubicación. Señálala manualmente en el mapa.'; return; } setPoint(results[0].lat, results[0].lon, 17); })
                .catch(() => { status.textContent = 'No se pudo buscar. Señala la ubicación directamente en el mapa.'; });
        });
    });
});
