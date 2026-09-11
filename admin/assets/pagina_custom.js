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
        const before = document.getElementById('menu_before');
        const preview = document.getElementById('menu-new-page-preview');
        let children = [], position = 0;
        const updateOrder = function () {
            const parent = select.value.indexOf('hijo:') === 0 ? select.value.slice(5) : '';
            panel.querySelectorAll('.page-menu-placement__parent-row').forEach(row => row.classList.toggle('is-selected', row.dataset.menuPos === select.value));
            if (!parent) return;
            const parentRow = Array.from(panel.querySelectorAll('.page-menu-placement__parent-row')).find(row => row.dataset.menuPos === select.value);
            children = parentRow ? Array.from(parentRow.querySelectorAll('.page-menu-placement__existing-child')) : [];
            position = before.value ? Math.max(0, children.findIndex(child => child.dataset.child === before.value)) : 0;
            drawPreview();
        };
        const drawPreview = function () {
            if (!preview || !children.length && position !== 0) return;
            preview.hidden = false;
            if (position < children.length) children[position].before(preview); else if (children.length) children[children.length - 1].after(preview); else { const row = Array.from(panel.querySelectorAll('.page-menu-placement__parent-row')).find(item => item.dataset.menuPos === select.value); if (row) row.querySelector('.page-menu-placement__children').appendChild(preview); }
            before.value = position < children.length ? children[position].dataset.child : '';
        };
        const mark = () => panel.querySelectorAll('[data-menu-pos]').forEach(button => button.classList.toggle('is-selected', button.dataset.menuPos === select.value));
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
    updateOrder(); if (grid) grid.addEventListener('change', event => { if (event.target.classList.contains('sec-checkbox')) updateOrder(); });
});
function openIconModal() { document.getElementById('iconModal').style.display = 'flex'; }
function closeIconModal() { document.getElementById('iconModal').style.display = 'none'; }
function selectIcon(cls) { const full = 'bi ' + cls; document.getElementById('input_icon_val').value = full; document.getElementById('current_icon_i').className = full; document.getElementById('preview_title_icon').className = full; closeIconModal(); }
function filterIcons(query) { const term = String(query || '').toLowerCase(); document.querySelectorAll('.icon-option-card').forEach(card => { card.style.display = card.dataset.name.includes(term) ? '' : 'none'; }); }
