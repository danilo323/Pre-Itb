// admin/assets/admin.js
// Motor del CMS: repeaters, imágenes, flash messages.
// SOLO para las páginas del panel de administración (singleton.php, editar.php, coleccion.php).
// El JS de la landing pública va en js/ — no mezclar.

document.addEventListener('DOMContentLoaded', function () {

    // ── Auto-cerrar flash message ───────────────────────
    const flash = document.getElementById('panel-flash');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.4s ease';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 400);
        }, 3500);
    }

    // ── Preview de imagen (función reutilizable) ─────────
    function initImageField(group) {
        const fileInput   = group.querySelector('input[type="file"]');
        const hiddenInput = group.querySelector('input[type="hidden"]');
        const box         = group.querySelector('.image-preview-wrapper');
        const preview     = box ? box.querySelector('.image-preview') : null;
        const img         = preview ? preview.querySelector('img') : null;
        const placeholder = box ? box.querySelector('.image-placeholder') : null;
        const removeBtn   = box ? box.querySelector('.btn-remove-image') : null;

        if (fileInput) {
            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = e => {
                    if (img) img.src = e.target.result;
                    if (preview) preview.style.display = 'block';
                    if (placeholder) placeholder.style.display = 'none';
                    if (removeBtn) removeBtn.style.display = 'inline-flex';
                };
                reader.readAsDataURL(file);
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                if (fileInput) fileInput.value = '';
                if (hiddenInput) hiddenInput.value = '';
                if (img) img.src = '';
                if (preview) preview.style.display = 'none';
                if (placeholder) placeholder.style.display = 'block';
                this.style.display = 'none';
            });
        }
    }

    // Inicializar todos los campos imagen existentes al cargar la página
    document.querySelectorAll('.field-image').forEach(group => {
        initImageField(group);
    });

    // ── REPEATER — añadir y eliminar ────────────────────
    function recalcularIndices(container) {
        const repeaterGroup = container.closest('.repeater-group');
        const itemLabel = repeaterGroup ? (repeaterGroup.dataset.itemLabel || 'Item') : 'Item';

        container.querySelectorAll('.repeater-item').forEach((item, index) => {
            item.dataset.index = index;
            const h4 = item.querySelector('h4');
            if (h4 && h4.childNodes[0]) h4.childNodes[0].textContent = itemLabel + ' ' + (index + 1) + ' ';
            item.querySelectorAll('input, textarea, select').forEach(input => {
                input.name = input.name.replace(/\[\d+\]/, '[' + index + ']');
            });
        });
    }

    document.querySelectorAll('.repeater-group').forEach(repeater => {
        const items  = repeater.querySelector('.repeater-items');
        const btnAdd = repeater.querySelector('.btn-add');
        if (!items || !btnAdd) return;

        // Botones de eliminar en items existentes al cargar
        items.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.repeater-item').remove();
                recalcularIndices(items);
            });
        });

        // Botón añadir nuevo item
        btnAdd.addEventListener('click', () => {
            const allItems = items.querySelectorAll('.repeater-item');
            const newIndex = allItems.length;
            const template = items.querySelector('.repeater-item');
            if (!template) return;

            const newItem = template.cloneNode(true);
            newItem.dataset.index = newIndex;

            // Actualizar nombres de campos con el nuevo índice
            newItem.querySelectorAll('input, textarea, select').forEach(input => {
                input.name = input.name.replace(/\[\d+\]/, '[' + newIndex + ']');
            });

            // Limpiar todos los valores del clon
            newItem.querySelectorAll('input').forEach(i => {
                if (i.type === 'checkbox' || i.type === 'radio') i.checked = false;
                else if (i.type !== 'hidden') i.value = '';
            });
            newItem.querySelectorAll('.field-image input[type="hidden"]').forEach(i => i.value = '');
            newItem.querySelectorAll('textarea').forEach(t => t.value = '');

            // Limpiar preview de imagen
            newItem.querySelectorAll('.image-preview img').forEach(img => img.src = '');
            newItem.querySelectorAll('.image-preview').forEach(div => div.style.display = 'none');
            newItem.querySelectorAll('.image-placeholder').forEach(div => div.style.display = 'block');
            newItem.querySelectorAll('.btn-remove-image').forEach(btn => btn.style.display = 'none');

            // Actualizar número en el título del item
            const repeaterGroup = items.closest('.repeater-group');
            const itemLabel = repeaterGroup ? (repeaterGroup.dataset.itemLabel || 'Item') : 'Item';
            const h4 = newItem.querySelector('h4');
            if (h4 && h4.childNodes[0]) h4.childNodes[0].textContent = itemLabel + ' ' + (newIndex + 1) + ' ';

            // Asociar botón de eliminar al nuevo item
            const btnRemove = newItem.querySelector('.btn-remove');
            if (btnRemove) {
                btnRemove.addEventListener('click', () => {
                    newItem.remove();
                    recalcularIndices(items);
                });
            }

            // Regenerar IDs únicos para los inputs de imagen clonados
            // Si no se hace esto, el label[for] apunta al file input del item original
            // y el botón "Cambiar imagen" nunca abre el selector del item nuevo.
            newItem.querySelectorAll('.field-image').forEach(fieldImg => {
                const oldInput  = fieldImg.querySelector('input[type="file"]');
                const labelBtn  = fieldImg.querySelector('label.btn[for]');
                if (oldInput && labelBtn) {
                    const newId = 'file_' + Math.random().toString(36).substr(2, 9);
                    oldInput.id = newId;
                    labelBtn.setAttribute('for', newId);
                }
            });

            items.appendChild(newItem);

            // Inicializar eventos de imagen en el item recién clonado
            newItem.querySelectorAll('.field-image').forEach(group => {
                initImageField(group);
            });
        });
    });

});
