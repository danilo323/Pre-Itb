// admin/assets/admin.js
// Motor del CMS: repeaters, imágenes, flash messages.
// SOLO para las páginas del panel de administración (singleton.php, editar.php, coleccion.php).
// El JS de la landing pública va en js/ — no mezclar.

document.addEventListener('DOMContentLoaded', function () {

    // ── CUSTOM CONFIRM MODAL ───────────────────────────
    window.customConfirm = function(message, onConfirm) {
        let modal = document.getElementById('admin-confirm-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'admin-confirm-modal';
            modal.className = 'admin-confirm-overlay';
            modal.innerHTML = `
                <div class="admin-confirm-box">
                    <div class="admin-confirm-head">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <h3>Confirmar acción</h3>
                    </div>
                    <p id="admin-confirm-msg" class="admin-confirm-msg"></p>
                    <div class="admin-confirm-actions">
                        <button type="button" id="admin-confirm-cancel" class="btn btn-outline">Cancelar</button>
                        <button type="button" id="admin-confirm-ok" class="btn btn-primary">Sí, eliminar</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        const msgEl = document.getElementById('admin-confirm-msg');
        const btnCancel = document.getElementById('admin-confirm-cancel');
        const btnOk = document.getElementById('admin-confirm-ok');

        msgEl.textContent = message;
        modal.classList.remove('is-hidden');
        setTimeout(() => {
            modal.classList.add('is-visible');
        }, 10);

        const close = () => {
            modal.classList.remove('is-visible');
            setTimeout(() => { modal.classList.add('is-hidden'); }, 200);
        };

        btnCancel.onclick = close;
        btnOk.onclick = () => {
            close();
            if (typeof onConfirm === 'function') onConfirm();
        };
    };

    // ── BOTONES ELIMINAR COLECCIÓN ─────────────────────
    document.querySelectorAll('.js-delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('form');
            window.customConfirm('¿Estás seguro de eliminar este registro?', () => {
                form.submit();
            });
        });
    });

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
                    if (preview) preview.classList.remove('is-hidden');
                    if (placeholder) placeholder.classList.add('is-hidden');
                    if (removeBtn) removeBtn.classList.remove('is-hidden');
                };
                reader.readAsDataURL(file);
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                window.customConfirm('¿Estás seguro de que deseas quitar esta imagen?', () => {
                    if (fileInput) fileInput.value = '';
                    if (hiddenInput) hiddenInput.value = '';
                    if (img) img.src = '';
                    if (preview) preview.classList.add('is-hidden');
                    if (placeholder) placeholder.classList.remove('is-hidden');
                    removeBtn.classList.add('is-hidden');
                });
            });
        }
    }

    // Inicializar todos los campos imagen existentes al cargar la página
    document.querySelectorAll('.field-image').forEach(group => {
        initImageField(group);
    });

    // ── Preview de audio (mismo patrón que el de imagen) ─
    // admin/fields/audio.php genera la misma estructura que image.php pero con
    // sus propias clases, así que necesita su propio inicializador.
    function initAudioField(group) {
        const fileInput   = group.querySelector('input[type="file"]');
        const hiddenInput = group.querySelector('input[type="hidden"]');
        const box         = group.querySelector('.audio-preview-wrapper');
        const preview     = box ? box.querySelector('.audio-preview') : null;
        const audio       = preview ? preview.querySelector('audio') : null;
        const placeholder = box ? box.querySelector('.audio-placeholder') : null;
        const removeBtn   = box ? box.querySelector('.btn-remove-audio') : null;

        if (fileInput) {
            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;
                // Un audio pesa demasiado para leerlo entero como data URL (lo
                // que hace el campo de imagen), así que se escucha por
                // referencia al archivo local, sin cargarlo en memoria.
                if (audio) {
                    if (audio.dataset.objectUrl) URL.revokeObjectURL(audio.dataset.objectUrl);
                    const url = URL.createObjectURL(file);
                    audio.dataset.objectUrl = url;
                    audio.src = url;
                }
                if (preview) preview.classList.remove('is-hidden');
                if (placeholder) placeholder.classList.add('is-hidden');
                if (removeBtn) removeBtn.classList.remove('is-hidden');
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                window.customConfirm('¿Estás seguro de que deseas quitar este audio?', () => {
                    if (fileInput) fileInput.value = '';
                    if (hiddenInput) hiddenInput.value = '';
                    if (audio) {
                        audio.pause();
                        if (audio.dataset.objectUrl) {
                            URL.revokeObjectURL(audio.dataset.objectUrl);
                            delete audio.dataset.objectUrl;
                        }
                        audio.removeAttribute('src');
                        audio.load();
                    }
                    if (preview) preview.classList.add('is-hidden');
                    if (placeholder) placeholder.classList.remove('is-hidden');
                    removeBtn.classList.add('is-hidden');
                });
            });
        }
    }

    document.querySelectorAll('.field-audio').forEach(group => {
        initAudioField(group);
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
                window.customConfirm('¿Estás seguro de que deseas eliminar este elemento?', () => {
                    btn.closest('.repeater-item').remove();
                    recalcularIndices(items);
                });
            });
        });

        // Reordenar con flechas ↑ ↓.
        // Se usa delegación para que los items clonados después también funcionen
        // sin tener que volver a asociar eventos.
        items.addEventListener('click', function (e) {
            const up   = e.target.closest('.btn-move-up');
            const down = e.target.closest('.btn-move-down');
            if (!up && !down) return;

            const item = (up || down).closest('.repeater-item');
            if (!item) return;

            if (up && item.previousElementSibling) {
                items.insertBefore(item, item.previousElementSibling);
            } else if (down && item.nextElementSibling) {
                items.insertBefore(item.nextElementSibling, item);
            } else {
                return; // ya está en el extremo, no hay nada que mover
            }

            recalcularIndices(items);
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
            newItem.querySelectorAll('.image-preview').forEach(div => div.classList.add('is-hidden'));
            newItem.querySelectorAll('.image-placeholder').forEach(div => div.classList.remove('is-hidden'));
            newItem.querySelectorAll('.btn-remove-image').forEach(btn => btn.classList.add('is-hidden'));

            // Actualizar número en el título del item
            const repeaterGroup = items.closest('.repeater-group');
            const itemLabel = repeaterGroup ? (repeaterGroup.dataset.itemLabel || 'Item') : 'Item';
            const h4 = newItem.querySelector('h4');
            if (h4 && h4.childNodes[0]) h4.childNodes[0].textContent = itemLabel + ' ' + (newIndex + 1) + ' ';

            // Asociar botón de eliminar al nuevo item
            const btnRemove = newItem.querySelector('.btn-remove');
            if (btnRemove) {
                btnRemove.addEventListener('click', () => {
                    window.customConfirm('¿Estás seguro de que deseas eliminar este elemento?', () => {
                        newItem.remove();
                        recalcularIndices(items);
                    });
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

    // ── CAMPO: MENU BUILDER ────────────────────────────
    // (antes vivía inline en admin/fields/menu_builder.php)
    function initMenuBuilder(container) {
        const itemsContainer = container.querySelector('.menu-builder-items');
        const btnAdd = container.querySelector('.btn-add-menu-item');
        const template = container.querySelector('.menu-builder-template');
        if (!itemsContainer || !btnAdd || !template) return;

        function updateIndices() {
            const items = itemsContainer.querySelectorAll('.menu-builder-item');
            items.forEach((item, index) => {
                item.dataset.index = index;
                item.querySelector('.mb-number').textContent = '#' + (index + 1);

                // Actualizar names
                item.querySelectorAll('input').forEach(input => {
                    const name = input.getAttribute('name');
                    if (name) {
                        input.setAttribute('name', name.replace(/\[\d+\]/, '[' + index + ']'));
                    }
                });
            });
        }

        function attachEvents(item) {
            const btnLeft = item.querySelector('.mb-btn-indent-left');
            const btnRight = item.querySelector('.mb-btn-indent-right');
            const btnUp = item.querySelector('.mb-btn-up');
            const btnDown = item.querySelector('.mb-btn-down');
            const btnRemove = item.querySelector('.mb-btn-remove');
            const btnEdit = item.querySelector('.mb-btn-edit');
            const btnCloseEdit = item.querySelector('.mb-btn-close-edit');
            const btnCopy = item.querySelector('.mb-btn-copy');
            const btnAddChild = item.querySelector('.mb-btn-add-child');

            const inputNivel = item.querySelector('.mb-input-nivel');
            const inputText = item.querySelector('.mb-input-text');
            const titleDisplay = item.querySelector('.mb-title-display');

            // Sincronizar texto
            inputText.addEventListener('input', () => {
                titleDisplay.textContent = inputText.value || 'Nuevo Item';
            });

            // Editar
            btnEdit.addEventListener('click', () => {
                item.classList.toggle('is-editing');
            });

            btnCloseEdit.addEventListener('click', () => {
                item.classList.remove('is-editing');
            });

            // Indentar
            btnLeft.addEventListener('click', () => {
                item.classList.remove('is-hijo');
                inputNivel.value = 'padre';
            });
            btnRight.addEventListener('click', () => {
                item.classList.add('is-hijo');
                inputNivel.value = 'hijo';
            });

            // Reordenar
            btnUp.addEventListener('click', () => {
                const prev = item.previousElementSibling;
                if (prev) {
                    itemsContainer.insertBefore(item, prev);
                    updateIndices();
                }
            });
            btnDown.addEventListener('click', () => {
                const next = item.nextElementSibling;
                if (next) {
                    itemsContainer.insertBefore(next, item);
                    updateIndices();
                }
            });

            // Eliminar
            btnRemove.addEventListener('click', () => {
                if (confirm('¿Eliminar este enlace?')) {
                    item.remove();
                    updateIndices();
                }
            });

            // Duplicar
            btnCopy.addEventListener('click', () => {
                const clone = item.cloneNode(true);
                clone.classList.remove('is-editing');
                itemsContainer.insertBefore(clone, item.nextSibling);
                attachEvents(clone);
                updateIndices();
            });

            // Añadir hijo
            btnAddChild.addEventListener('click', () => {
                item.classList.remove('is-hijo'); // Asegurar que es padre
                inputNivel.value = 'padre';

                const newIndex = itemsContainer.children.length;
                const html = template.innerHTML
                    .replace(/{INDEX}/g, newIndex)
                    .replace(/{NUM}/g, newIndex + 1);

                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html.trim();
                const newItem = tempDiv.firstChild;

                newItem.classList.add('is-hijo');
                newItem.querySelector('.mb-input-nivel').value = 'hijo';
                newItem.classList.add('is-editing');
                newItem.querySelectorAll('input').forEach(i => i.removeAttribute('disabled'));

                itemsContainer.insertBefore(newItem, item.nextSibling);
                attachEvents(newItem);
                updateIndices();
            });
        }

        // Attach a los items iniciales
        itemsContainer.querySelectorAll('.menu-builder-item').forEach(attachEvents);

        // Agregar nuevo principal
        btnAdd.addEventListener('click', () => {
            const newIndex = itemsContainer.children.length;
            const html = template.innerHTML
                .replace(/{INDEX}/g, newIndex)
                .replace(/{NUM}/g, newIndex + 1);

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html.trim();
            const newItem = tempDiv.firstChild;

            newItem.classList.add('is-editing');
            newItem.querySelectorAll('input').forEach(i => i.removeAttribute('disabled'));

            itemsContainer.appendChild(newItem);
            attachEvents(newItem);
            updateIndices();
        });
    }
    document.querySelectorAll('.menu-builder-wrapper').forEach(initMenuBuilder);

    // ── COLECCIÓN: REORDENAR (flechas arriba/abajo) ────
    // (antes vivía inline en admin/coleccion.php)
    document.querySelectorAll('.js-move-up').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');
            if (row.previousElementSibling) {
                row.parentNode.insertBefore(row, row.previousElementSibling);
                const orderActions = document.getElementById('order-actions');
                if (orderActions) orderActions.classList.remove('is-hidden');
            }
        });
    });
    document.querySelectorAll('.js-move-down').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');
            if (row.nextElementSibling) {
                row.parentNode.insertBefore(row.nextElementSibling, row);
                const orderActions = document.getElementById('order-actions');
                if (orderActions) orderActions.classList.remove('is-hidden');
            }
        });
    });
    window.submitOrder = function () {
        const ids = Array.from(document.querySelectorAll('tr[data-id]')).map(tr => tr.getAttribute('data-id'));
        document.getElementById('order-data-input').value = JSON.stringify(ids);
        document.getElementById('save-order-form').submit();
    };

    // ── INTERRUPTORES EN GRUPO EXCLUSIVO ────────────────
    // Varios interruptores (admin/fields/bool.php) pueden declarar en el schema
    // que pertenecen al mismo 'exclusive_group'. Entonces dejan de ser
    // independientes y pasan a comportarse como una sola elección: siempre hay
    // EXACTAMENTE UNO encendido.
    //
    //   - Al encender uno, los demás del grupo se apagan solos.
    //   - Si se apaga el único que quedaba encendido, se enciende el que lleve
    //     'exclusive_default' (data-exclusivo-defecto) para que el grupo nunca
    //     se quede en blanco.
    //
    // Este motor no sabe qué significan los interruptores ni a qué sección
    // pertenecen: solo lee el grupo del data-attribute. Quién forma cada grupo
    // se decide en el schema.

    function interruptoresDe(grupo) {
        return Array.from(document.querySelectorAll(
            '.bool-toggle-input[data-exclusivo="' + grupo + '"]'
        ));
    }

    function normalizarGrupo(grupo) {
        const todos = interruptoresDe(grupo);
        if (!todos.length) return;

        const encendidos = todos.filter(i => i.checked);

        if (encendidos.length === 0) {
            // Nadie encendido: se enciende el de reserva. Si el schema no marcó
            // ninguno, se usa el primero para no dejar el grupo vacío.
            const reserva = todos.find(i => i.dataset.exclusivoDefecto === '1') || todos[0];
            reserva.checked = true;
            return;
        }

        if (encendidos.length > 1) {
            // Puede pasar al cargar, si los datos guardados venían inconsistentes
            // (por ejemplo, de antes de que existiera el grupo). Se respeta el
            // último que tocó el usuario si lo hay, y si no, el primero.
            const gana = encendidos.find(i => i === ultimoTocado) || encendidos[0];
            encendidos.forEach(i => { if (i !== gana) i.checked = false; });
        }
    }

    let ultimoTocado = null;

    document.addEventListener('change', function (e) {
        const control = e.target;
        if (!control.classList || !control.classList.contains('bool-toggle-input')) return;
        const grupo = control.dataset.exclusivo;
        if (!grupo) return;

        ultimoTocado = control;

        if (control.checked) {
            interruptoresDe(grupo).forEach(otro => {
                if (otro !== control) otro.checked = false;
            });
        } else {
            // Se acaba de apagar: si no queda ninguno, entra el de reserva.
            normalizarGrupo(grupo);
        }
    });

    // Los interruptores pueden aparecer o desaparecer al añadir o eliminar
    // items de un repeater. Si se borra justo el que estaba encendido, el grupo
    // se quedaría sin ninguno, así que se vuelve a normalizar tras cada cambio
    // del DOM. Se agrupa con un temporizador porque una sola acción (clonar un
    // item) dispara muchas mutaciones seguidas.
    const gruposEnPagina = Array.from(
        new Set(Array.from(document.querySelectorAll('[data-exclusivo]'))
            .map(i => i.dataset.exclusivo))
    );

    if (gruposEnPagina.length) {
        gruposEnPagina.forEach(normalizarGrupo);

        let esperaMutaciones = null;
        const observador = new MutationObserver(() => {
            clearTimeout(esperaMutaciones);
            esperaMutaciones = setTimeout(() => {
                gruposEnPagina.forEach(normalizarGrupo);
            }, 50);
        });
        observador.observe(document.body, { childList: true, subtree: true });
    }

});

// Estado visual del campo PDF. El archivo se sube al guardar el formulario.
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.field-file').forEach(function (field) {
        const input = field.querySelector('input[type="file"]');
        const hidden = field.querySelector('input[type="hidden"]');
        const name = field.querySelector('.file-summary-name');
        const meta = field.querySelector('.file-summary-meta');
        const link = field.querySelector('.file-preview-link');
        const remove = field.querySelector('.btn-remove-file');
        const label = field.querySelector('.file-select-label');
        if (!input || !hidden || !name || !meta || !remove) return;

        input.addEventListener('change', function () {
            const selected = input.files[0];
            if (!selected) return;
            name.textContent = selected.name;
            meta.textContent = 'PDF · ' + (selected.size / 1024 / 1024).toFixed(selected.size >= 1048576 ? 1 : 2) + ' MB · Se guardará al confirmar';
            if (link) link.remove();
            remove.disabled = false;
            if (label) label.textContent = 'Reemplazar PDF';
        });
        remove.addEventListener('click', function () {
            input.value = '';
            hidden.value = '';
            name.textContent = 'Ningún PDF seleccionado';
            meta.textContent = 'PDF · hasta 32 MB';
            if (link) link.remove();
            remove.disabled = true;
            if (label) label.textContent = 'Seleccionar PDF';
        });
    });
});
