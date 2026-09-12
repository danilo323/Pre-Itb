document.addEventListener('DOMContentLoaded', function () {
    const name = document.getElementById('input_nombre');
    const slug = document.getElementById('input_slug');
    if (name && slug) {
        name.addEventListener('input', function () {
            if (!slug.dataset.manual) slug.value = this.value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9\s\-]/g, '').trim().replace(/\s+/g, '-');
        });
        slug.addEventListener('input', function () { this.dataset.manual = '1'; });
    }
    const select = document.getElementById('menu_pos_select'), panel = document.getElementById('page-menu-placement');
    if (select && panel) {
        // Cada hijo se coloca dentro de su <details> padre: el desplegado es
        // nativo de HTML y no depende de clases ni de estados ocultos.
        panel.querySelectorAll('.page-menu-placement__existing-child').forEach(child => {
            const parent = Array.from(panel.querySelectorAll('.page-menu-placement__parent-row')).find(row => row.dataset.menuPos === 'hijo:' + child.dataset.parent);
            const container = parent && parent.querySelector('.page-menu-placement__children');
            if (container) container.appendChild(child);
        });
        const nombreOriginal = (name && name.value.trim()) || '';
        if (nombreOriginal) {
            panel.querySelectorAll('.page-menu-placement__existing-child').forEach(child => {
                if ((child.dataset.child || '') === nombreOriginal) child.remove();
            });
        }
        const before = document.getElementById('menu_before');
        const preview = document.getElementById('menu-new-page-preview');
        let children = [], position = 0;
        const updateOrder = function () {
            const parent = select.value.indexOf('hijo:') === 0 ? select.value.slice(5) : '';
            panel.querySelectorAll('.page-menu-placement__parent-row').forEach(row => row.classList.toggle('is-selected', row.dataset.menuPos === select.value));
            if (select.value === 'padre') {
                preview.hidden = false;
                preview.classList.add('is-main-menu-item');
                panel.querySelector('.page-menu-placement__parents').appendChild(preview);
                before.value = '';
                return;
            }
            preview.classList.remove('is-main-menu-item');
            if (!parent) { preview.hidden = true; return; }
            const parentRow = Array.from(panel.querySelectorAll('.page-menu-placement__parent-row')).find(row => row.dataset.menuPos === select.value);
            children = parentRow ? Array.from(parentRow.querySelectorAll('.page-menu-placement__existing-child')) : [];
            const encontrado = before.value ? children.findIndex(child => child.dataset.child === before.value) : -1;
            position = encontrado >= 0 ? encontrado : children.length;
            drawPreview();
        };
        const drawPreview = function () {
            if (!preview || !children.length && position !== 0) return;
            preview.hidden = false;
            if (position < children.length) children[position].before(preview); else if (children.length) children[children.length - 1].after(preview); else { const row = Array.from(panel.querySelectorAll('.page-menu-placement__parent-row')).find(item => item.dataset.menuPos === select.value); if (row) row.querySelector('.page-menu-placement__children').appendChild(preview); }
            before.value = position < children.length ? children[position].dataset.child : '';
        };
        const mark = () => panel.querySelectorAll('[data-menu-pos]').forEach(button => button.classList.toggle('is-selected', button.dataset.menuPos === select.value));
        panel.querySelectorAll('.page-menu-placement__choice[data-menu-pos]').forEach(button => button.addEventListener('click', function () {
            select.value = this.dataset.menuPos;
            before.value = '';
            mark();
            updateOrder();
        }));
        panel.querySelectorAll('.page-menu-placement__parent').forEach(button => button.addEventListener('click', function () { const row = this.closest('.page-menu-placement__parent-row'); select.value = row.dataset.menuPos; before.value = ''; mark(); updateOrder(); }));
        panel.querySelectorAll('.js-menu-position').forEach(button => button.addEventListener('click', function (event) {
            event.stopPropagation();
            const row = this.closest('.page-menu-placement__existing-child');
            position = children.indexOf(row) + (this.dataset.direction === 'after' ? 1 : 0);
            drawPreview();
        }));
        mark(); updateOrder();
        const updatePreviewName = function () { const label = preview.querySelector('span'); if (label) label.textContent = (name && name.value.trim()) || 'Nueva página'; };
        updatePreviewName(); if (name) name.addEventListener('input', updatePreviewName);
        // ---- Ventana (modal) para elegir la posicion en el menu -------------
        // La lista ya no cuelga suelta del formulario: se abre desde un boton
        // que resume lo elegido. "Cancelar" devuelve el valor que habia al
        // abrirla, asi que se puede trastear sin miedo a dejarlo a medias.
        const modal = document.getElementById('menu-pos-modal');
        const trigger = document.getElementById('menu-pos-trigger');
        if (modal && trigger) {
            let previo = { pos: select.value, before: before.value };
            const textoResumen = function () {
                const valor = select.value;
                if (valor === 'padre')
                    return ['Como opci\u00f3n principal del men\u00fa', 'Quedar\u00e1 al nivel de Instituto, Oferta Acad\u00e9mica y Admisiones.'];
                if (valor.indexOf('hijo:') === 0) {
                    const padre = valor.slice(5);
                    return ['Dentro de \u00ab' + padre + '\u00bb',
                        before.value ? 'Justo antes de \u00ab' + before.value + '\u00bb.' : 'Al final de las opciones de ese men\u00fa.'];
                }
                return ['No se mostrar\u00e1 en el men\u00fa', 'Solo estar\u00e1 disponible mediante su URL.'];
            };
            const pintarResumen = function () {
                const textos = textoResumen();
                document.querySelectorAll('[data-menu-resumen]').forEach(function (el) { el.textContent = textos[0]; });
                document.querySelectorAll('[data-menu-detalle]').forEach(function (el) { el.textContent = textos[1]; });
            };
            const abrir = function () {
                previo = { pos: select.value, before: before.value };
                modal.hidden = false;
                document.body.classList.add('menu-pos-modal-open');
                // Si la pagina ya esta dentro de un menu padre, ese menu se abre
                // desplegado para ver de una donde queda.
                const fila = modal.querySelector('.page-menu-placement__parent-row.is-selected');
                if (fila) fila.open = true;
                const foco = modal.querySelector('.menu-pos-modal__close');
                if (foco) foco.focus();
            };
            const cerrar = function () {
                modal.hidden = true;
                document.body.classList.remove('menu-pos-modal-open');
                trigger.focus();
            };
            trigger.addEventListener('click', abrir);
            modal.querySelectorAll('[data-menu-cerrar]').forEach(function (el) { el.addEventListener('click', cerrar); });
            modal.querySelectorAll('[data-menu-cancelar]').forEach(function (el) {
                el.addEventListener('click', function () {
                    select.value = previo.pos;
                    before.value = previo.before;
                    mark(); updateOrder(); pintarResumen(); cerrar();
                });
            });
            document.addEventListener('keydown', function (evento) {
                if (evento.key === 'Escape' && !modal.hidden) cerrar();
            });
            // Cualquier clic de dentro puede cambiar la eleccion; el resumen se
            // repinta al terminar de correr el manejador que lo provoco.
            modal.addEventListener('click', function () { setTimeout(pintarResumen, 0); });
            pintarResumen();
        }
    }
    const grid = document.getElementById('secciones-grid'), list = document.getElementById('orden-lista'), summary = document.getElementById('orden-resumen');
    const updateOrder = () => {
        if (!grid || !list || !summary) return;
        list.innerHTML = ''; let position = 1;
        grid.querySelectorAll('.sec-checkbox').forEach(cb => {
            const label = cb.closest('.sec-label'), badge = label.querySelector('.sec-badge');
            if (cb.checked) {
                badge.textContent = position; badge.style.display = 'flex'; label.classList.add('is-checked');
                const chip = document.createElement('span'); chip.className = 'section-order-chip';
                chip.innerHTML = '<span class="section-order-chip__number">' + position + '</span>' + (cb.dataset.label || cb.value);
                list.appendChild(chip); position++;
            } else { badge.style.display = 'none'; label.classList.remove('is-checked'); }
        });
        summary.style.display = position > 1 ? 'block' : 'none';
    };
    // Cada grupo (el sitio y cada pagina ya creada) ofrece la misma seccion con
    // su propia casilla, porque el contenido que hereda es distinto. Lo que no
    // tiene sentido es meter la misma seccion dos veces en la pagina, asi que al
    // marcar una se desmarca la equivalente del otro grupo: manda la ultima.
    const soltarGemelas = (casilla) => {
        if (!grid || !casilla.checked) return;
        grid.querySelectorAll('.sec-checkbox').forEach(otra => {
            if (otra === casilla || !otra.checked) return;
            if ((otra.dataset.base || otra.value) !== (casilla.dataset.base || casilla.value)) return;
            otra.checked = false;
            // Un parpadeo corto para que se vea de donde se quito.
            const etiqueta = otra.closest('.sec-label');
            if (!etiqueta) return;
            etiqueta.classList.remove('sec-label--reemplazada');
            void etiqueta.offsetWidth;
            etiqueta.classList.add('sec-label--reemplazada');
        });
    };
    updateOrder();
    if (grid) grid.addEventListener('change', event => {
        if (!event.target.classList.contains('sec-checkbox')) return;
        soltarGemelas(event.target);
        updateOrder();
    });
});
function openIconModal() { document.getElementById('iconModal').style.display = 'flex'; }
function closeIconModal() { document.getElementById('iconModal').style.display = 'none'; }
function selectIcon(cls) { const full = 'bi ' + cls; document.getElementById('input_icon_val').value = full; document.getElementById('current_icon_i').className = full; document.getElementById('preview_title_icon').className = full; closeIconModal(); }
function filterIcons(query) { const term = String(query || '').toLowerCase(); document.querySelectorAll('.icon-option-card').forEach(card => { card.style.display = card.dataset.name.includes(term) ? '' : 'none'; }); }
