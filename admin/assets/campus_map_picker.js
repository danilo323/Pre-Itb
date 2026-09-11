/**
 * campus_map_picker.js
 * Mapa unificado de campus: un solo mapa Leaflet con selector integrado
 * en el repeater para editar ubicaciones individualmente.
 *
 * Flujo:
 *  1. Inyecta botón "Seleccionar" junto al nombre de cada campus en el repeater.
 *  2. Lee ubicaciones actuales del repeater y coloca marcadores.
 *  3. Al hacer clic en "Seleccionar", ese campus se activa para editar.
 *  4. Al hacer clic en el mapa se coloca / mueve el pin del campus activo.
 *     Los pines no se arrastran: el clic actualiza solo el campus elegido.
 *  5. Al enviar el formulario, sincroniza las ubicaciones a los hidden inputs
 *     del repeater (lista_campus[N][ubicacion]) para que se guarden normalmente.
 */
(function () {
    function init() {
        if (typeof L === 'undefined') return;

        document.querySelectorAll('.js-campus-map-picker').forEach(function (container) {
        var mapEl       = container.querySelector('.js-campus-map');
        // La ubicación se asigna exclusivamente con un clic sobre el mapa.
        var searchInput = null;
        var searchBtn   = null;
        var statusEl    = container.querySelector('.js-campus-status');
        var hiddenInput = container.querySelector('.js-campus-locations-value');
        var activeBox   = container.querySelector('.js-campus-active');
        var activeName  = container.querySelector('.js-campus-active-name');
        var deselectBtn = container.querySelector('.js-campus-deselect');

        /* ── 0. Detectar el name_path real del repeater ───────────────── */
        // El repeater puede tener data-name-path="lista_campus" o
        // "footer__lista_campus" dependiendo de si es singleton/page.
        // Buscamos el repeater que contenga "lista_campus" en su data-name-path.
        var repeaterGroup = document.querySelector(
            '.repeater-group[data-name-path$="lista_campus"]'
        );
        if (!repeaterGroup && document.querySelector('.repeater-group[data-name-path*="lista_campus"]')) {
            repeaterGroup = document.querySelector('.repeater-group[data-name-path*="lista_campus"]');
        }
        var namePath = repeaterGroup ? repeaterGroup.getAttribute('data-name-path') : 'lista_campus';

        /* ── 1. Leer campus del repeater ────────────────────────────────── */
        function readCampusesFromRepeater() {
            var items = repeaterGroup
                ? repeaterGroup.querySelectorAll('.repeater-item')
                : [];
            var campuses = [];
            items.forEach(function (item, idx) {
                var nameInput  = item.querySelector('input[name="' + namePath + '[' + idx + '][nombre]"]');
                var ubicInput  = item.querySelector('input[name="' + namePath + '[' + idx + '][ubicacion]"]');
                var nombre = nameInput ? nameInput.value.trim() : ('Campus ' + (idx + 1));
                var ubicacion = null;
                if (ubicInput && ubicInput.value) {
                    try { ubicacion = JSON.parse(ubicInput.value); } catch (e) { ubicacion = null; }
                }
                campuses.push({ index: idx, nombre: nombre, ubicacion: ubicacion, element: item });
            });
            return campuses;
        }

        /* ── 2. Inyectar botones "Seleccionar" en el repeater ───────────── */
        function injectSelectButtons() {
            campuses.forEach(function (c) {
                var header = c.element.querySelector('.repeater-item-header');
                if (!header) return;
                var title = header.querySelector('.repeater-item-title');
                if (!title) return;

                var btn = c.element.querySelector('.js-campus-select-btn');
                if (!btn) {
                    btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-sm js-campus-select-btn';
                    title.appendChild(btn);
                }
                btn.dataset.index = c.index;
                btn.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Seleccionar';

                // Indicador de ubicación guardada
                var dot = c.element.querySelector('.cmp-repeater-dot');
                if (!dot) {
                    dot = document.createElement('span');
                    dot.className = 'cmp-repeater-dot';
                    title.insertBefore(dot, btn);
                }
                dot.classList.toggle('has-location', !!locations[c.index]);
                dot.title = locations[c.index] ? 'Ubicación definida' : 'Sin ubicación';

                // Se usa una propiedad del elemento y no data-* porque los
                // repeaters se clonan: el atributo se copiaría, pero el evento
                // no, dejando el botón del campus nuevo sin funcionar.
                if (!btn._campusMapBound) {
                    btn.addEventListener('click', function () {
                        selectCampus(Number(btn.dataset.index));
                    });
                    btn._campusMapBound = true;
                }
            });
        }

        /* ── 3. Estado ──────────────────────────────────────────────────── */
        var campuses    = [];
        var locations   = {};          // {0: {lat, lng}, 1: {lat, lng}, ...}
        var markers     = {};          // {0: L.marker, ...}
        var selectedIndex = -1;        // campus activo para editar
        var Ecuador     = [-2.170998, -79.922359];
        var defaultZoom = 13;
        var editZoom    = 16;

        // Cargar ubicaciones previas desde el hidden input del campo
        if (hiddenInput && hiddenInput.value) {
            try {
                var prev = JSON.parse(hiddenInput.value);
                if (prev && typeof prev === 'object') locations = prev;
            } catch (e) { /* ignorar */ }
        }

        campuses = readCampusesFromRepeater();

        // Complementar con ubicaciones del repeater (fuente primaria)
        campuses.forEach(function (c) {
            if (c.ubicacion && c.ubicacion.lat && c.ubicacion.lng && !locations[c.index]) {
                locations[c.index] = { lat: c.ubicacion.lat, lng: c.ubicacion.lng };
            }
        });

        injectSelectButtons();

        /* ── 4. Mapa ────────────────────────────────────────────────────── */
        var map = L.map(mapEl).setView(Ecuador, defaultZoom);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        function updateMarkerLabels() {
            Object.keys(markers).forEach(function (idx) {
                var marker = markers[idx];
                var campus = campuses[Number(idx)];
                marker.unbindTooltip();

                // Confirma visualmente cuál campus está siendo editado.
                if (Number(idx) === selectedIndex && campus) {
                    marker.bindTooltip(campus.nombre, {
                        permanent: true,
                        direction: 'top',
                        offset: [0, -32],
                        className: 'cmp-campus-marker-label'
                    }).openTooltip();
                }
            });
        }

        // El mapa siempre representa un único campus: el que se está editando.
        function clearMarkers() {
            Object.keys(markers).forEach(function (idx) {
                map.removeLayer(markers[idx]);
            });
            markers = {};
        }

        function createMarker(idx, lat, lng) {
            Object.keys(markers).forEach(function (markerIdx) {
                if (Number(markerIdx) !== idx) {
                    map.removeLayer(markers[markerIdx]);
                    delete markers[markerIdx];
                }
            });
            if (markers[idx]) markers[idx].setLatLng([lat, lng]);
            else {
                markers[idx] = L.marker([lat, lng], { draggable: false }).addTo(map);
            }
            updateMarkerLabels();
        }

        /* ── 5. Selección de campus ─────────────────────────────────────── */
        function selectCampus(idx) {
            selectedIndex = idx;
            var c = campuses[idx];

            // Actualizar UI activa
            activeBox.hidden = false;
            activeName.textContent = c.nombre;

            // Actualizar botones del repeater
            repeaterGroup.querySelectorAll('.js-campus-select-btn').forEach(function (b) {
                b.classList.toggle('is-active', Number(b.dataset.index) === idx);
                if (Number(b.dataset.index) === idx) {
                    b.innerHTML = '<i class="bi bi-check-lg"></i> Editando';
                } else {
                    b.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Seleccionar';
                }
            });

            // Mover mapa
            var loc = locations[c.index];
            if (loc) {
                map.setView([loc.lat, loc.lng], editZoom);
                createMarker(c.index, loc.lat, loc.lng);
            } else {
                clearMarkers();
            }
            updateMarkerLabels();
            updateStatus();
        }

        function deselectCampus() {
            selectedIndex = -1;
            activeBox.hidden = true;
            clearMarkers();
            repeaterGroup.querySelectorAll('.js-campus-select-btn').forEach(function (b) {
                b.classList.remove('is-active');
                b.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Seleccionar';
            });
            updateMarkerLabels();
            statusEl.textContent = 'Haz clic en "Seleccionar" junto al nombre de un campus para editar su ubicación en el mapa.';
        }

        deselectBtn.addEventListener('click', deselectCampus);

        function updateStatus() {
            if (selectedIndex < 0) return;
            var c = campuses[selectedIndex];
            var loc = locations[c.index];
            if (loc) {
                statusEl.textContent = c.nombre + ': ' + loc.lat.toFixed(6) + ', ' + loc.lng.toFixed(6) + ' — haz clic en el mapa para cambiar su ubicación.';
            } else {
                statusEl.textContent = 'Haz clic en el mapa para fijar la ubicación de "' + c.nombre + '".';
            }
        }

        function updateDots() {
            campuses.forEach(function (c) {
                var dot = c.element ? c.element.querySelector('.cmp-repeater-dot') : null;
                if (dot) {
                    dot.classList.toggle('has-location', !!locations[c.index]);
                    dot.title = locations[c.index] ? 'Ubicación definida' : 'Sin ubicación';
                }
            });
        }

        /* ── 6. Click en mapa → asignar ubicación al campus activo ──────── */
        map.on('click', function (e) {
            if (selectedIndex < 0) {
                statusEl.textContent = 'Primero selecciona un campus de la lista.';
                return;
            }
            var c = campuses[selectedIndex];
            locations[c.index] = { lat: e.latlng.lat, lng: e.latlng.lng };
            createMarker(c.index, e.latlng.lat, e.latlng.lng);
            map.setView([e.latlng.lat, e.latlng.lng], editZoom);
            syncHiddenInput();
            updateStatus();
            updateDots();
        });

        /* ── 7. Búsqueda ────────────────────────────────────────────────── */
        if (searchBtn && searchInput) searchBtn.addEventListener('click', function () {
            var query = searchInput.value.trim();
            if (!query) return;
            statusEl.textContent = 'Buscando ubicación…';
            fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&q=' + encodeURIComponent(query),
                { headers: { Accept: 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (results) {
                    if (!results.length) { statusEl.textContent = 'No se encontró esa ubicación.'; return; }
                    var lat = Number(results[0].lat), lng = Number(results[0].lon);
                    if (selectedIndex >= 0) {
                        var c = campuses[selectedIndex];
                        locations[c.index] = { lat: lat, lng: lng };
                        createMarker(c.index, lat, lng);
                        syncHiddenInput();
                        updateStatus();
                        updateDots();
                    }
                    map.setView([lat, lng], editZoom);
                })
                .catch(function () { statusEl.textContent = 'No se pudo buscar.'; });
        });
        if (searchBtn && searchInput) searchInput.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); searchBtn.click(); } });

        /* ── 8. Sincronizar al enviar el formulario ─────────────────────── */
        function syncHiddenInput() {
            hiddenInput.value = JSON.stringify(locations);
        }

        var form = container.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                syncHiddenInput();
                campuses.forEach(function (c) {
                    var ubicInput = repeaterGroup && repeaterGroup.querySelector(
                        'input[name="' + namePath + '[' + c.index + '][ubicacion]"]'
                    );
                    if (ubicInput && locations[c.index]) {
                        ubicInput.value = JSON.stringify(locations[c.index]);
                    } else if (ubicInput && !locations[c.index]) {
                        ubicInput.value = '';
                    }
                });
            });
        }

        /* ── 9. Re-leer campus si cambia el repeater ────────────────────── */
        if (repeaterGroup) {
            var observer = new MutationObserver(function (mutations) {
                // Los botones y sus iconos también cambian el DOM. Solo se
                // vuelve a leer el repeater cuando realmente se añadió,
                // eliminó o reordenó un campus; de otro modo la selección se
                // perdería justo al pulsar "Seleccionar".
                var changed = mutations.some(function (mutation) {
                    var nodes = Array.prototype.slice.call(mutation.addedNodes)
                        .concat(Array.prototype.slice.call(mutation.removedNodes));
                    return nodes.some(function (node) {
                        return node.nodeType === 1 && (
                            node.classList.contains('repeater-item') ||
                            node.querySelector('.repeater-item')
                        );
                    });
                });
                if (!changed) return;
                campuses = readCampusesFromRepeater();
                locations = {};
                campuses.forEach(function (c) {
                    if (c.ubicacion) locations[c.index] = c.ubicacion;
                });
                clearMarkers();
                injectSelectButtons();
                updateDots();
                selectedIndex = -1;
                activeBox.hidden = true;
                updateMarkerLabels();
                statusEl.textContent = 'Haz clic en "Seleccionar" junto al nombre de un campus para editar su ubicación en el mapa.';
            });
            observer.observe(repeaterGroup, { childList: true, subtree: true });
        }
    });
    }

    // Ejecutar: si el DOM ya está listo, init() directo; si no, esperar a DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
