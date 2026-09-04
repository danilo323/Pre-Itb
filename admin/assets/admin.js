// admin/assets/admin.js

document.addEventListener('DOMContentLoaded', function () {

    const panel     = document.getElementById('admin-panel');
    const closeBtn  = document.getElementById('panel-close-btn');
    const toggleBtn = document.getElementById('admin-toggle-btn');
    const grid      = document.getElementById('panel-sections-grid');
    const formContainer = document.getElementById('panel-form-container');

    if (!panel) return;

    // ── Abrir panel al cargar ───────────────────────────
    document.body.classList.add('panel-open');

    // ── Cerrar / Reabrir panel ──────────────────────────
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            panel.classList.add('hidden');
            document.body.classList.remove('panel-open');
            toggleBtn.classList.add('visible');
        });
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            panel.classList.remove('hidden');
            document.body.classList.add('panel-open');
            toggleBtn.classList.remove('visible');
        });
    }

    // ── Tabs (SECCIONES / CUENTA) ───────────────────────
    document.querySelectorAll('.panel-tab').forEach(tab => {
        tab.addEventListener('click', function () {
            const tabId = this.dataset.tab;
            document.querySelectorAll('.panel-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.panel-tab-content').forEach(c => c.style.display = 'none');
            this.classList.add('active');
            const content = document.getElementById('tab-' + tabId);
            if (content) content.style.display = 'flex';
        });
    });

    // ── Click en card de sección ────────────────────────
    document.querySelectorAll('.section-card').forEach(card => {
        card.addEventListener('click', function () {
            const sectionKey = this.dataset.section;
            const anchor     = this.dataset.anchor;

            // 1. Resaltar card activa
            document.querySelectorAll('.section-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            // 2. Scroll suave a la sección en la landing page
            const target = document.getElementById(anchor);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            // 3. Mostrar formulario de esa sección
            mostrarFormulario(sectionKey);
        });
    });

    // ── Botón Volver ────────────────────────────────────
    document.querySelectorAll('[data-back]').forEach(btn => {
        btn.addEventListener('click', () => {
            volverAlGrid();
        });
    });

    function mostrarFormulario(sectionKey) {
        // Ocultar grid de cards
        if (grid) grid.style.display = 'none';

        // Mostrar contenedor de formularios
        if (formContainer) formContainer.style.display = 'flex';

        // Ocultar todos los forms
        document.querySelectorAll('.panel-section-form').forEach(f => f.style.display = 'none');

        // Mostrar el form correcto
        const form = document.getElementById('form-' + sectionKey);
        if (form) form.style.display = 'flex';
    }

    function volverAlGrid() {
        // Ocultar todos los forms y el contenedor
        document.querySelectorAll('.panel-section-form').forEach(f => f.style.display = 'none');
        if (formContainer) formContainer.style.display = 'none';

        // Mostrar el grid
        if (grid) grid.style.display = 'block';

        // Quitar activo de cards
        document.querySelectorAll('.section-card').forEach(c => c.classList.remove('active'));
    }

    // ── Auto-cerrar flash message ───────────────────────
    const flash = document.getElementById('panel-flash');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.4s ease';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 400);
        }, 3500);
    }

    // ── Abrir sección guardada si viene de guardar ──────
    const params = new URLSearchParams(window.location.search);
    const savedSection = params.get('panel_section');
    if (savedSection) {
        // Abrir la pestaña de secciones (por si acaso)
        document.querySelectorAll('.panel-tab').forEach(t => t.classList.remove('active'));
        document.querySelector('[data-tab="secciones"]').classList.add('active');
        document.getElementById('tab-secciones').style.display = 'flex';

        // Marcar la card como activa y mostrar el formulario
        const card = document.querySelector(`.section-card[data-section="${savedSection}"]`);
        if (card) {
            card.classList.add('active');
            mostrarFormulario(savedSection);
            
            // Hacer scroll a la sección en el frontend
            const target = document.getElementById(savedSection);
            if (target) {
                setTimeout(() => {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            }
        }

        // Limpiar la URL para que no vuelva a abrirse si el usuario recarga la página
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // ── REPEATER — clonar y eliminar ───────────────────
    document.querySelectorAll('.repeater-group').forEach(repeater => {
        const items  = repeater.querySelector('.repeater-items');
        const btnAdd = repeater.querySelector('.btn-add');
        if (!items || !btnAdd) return;

        // Eliminar en items existentes
        items.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.closest('.repeater-item').remove();
                recalcularIndices(items);
            });
        });

        // Añadir nuevo item
        btnAdd.addEventListener('click', () => {
            const allItems = items.querySelectorAll('.repeater-item');
            const newIndex = allItems.length;
            const template = items.querySelector('.repeater-item');
            if (!template) return;

            const newItem = template.cloneNode(true);
            newItem.dataset.index = newIndex;

            newItem.querySelectorAll('input, textarea, select').forEach(input => {
                input.name  = input.name.replace(/\[\d+\]/, '[' + newIndex + ']');
                input.value = '';
            });

            const h4 = newItem.querySelector('h4');
            if (h4 && h4.childNodes[0]) h4.childNodes[0].textContent = 'Item ' + (newIndex + 1) + ' ';

            const btnRemove = newItem.querySelector('.btn-remove');
            if (btnRemove) {
                btnRemove.addEventListener('click', () => {
                    newItem.remove();
                    recalcularIndices(items);
                });
            }

            items.appendChild(newItem);
        });
    });

    function recalcularIndices(container) {
        container.querySelectorAll('.repeater-item').forEach((item, index) => {
            item.dataset.index = index;
            const h4 = item.querySelector('h4');
            if (h4 && h4.childNodes[0]) h4.childNodes[0].textContent = 'Item ' + (index + 1) + ' ';
            item.querySelectorAll('input, textarea, select').forEach(input => {
                input.name = input.name.replace(/\[\d+\]/, '[' + index + ']');
            });
        });
    }

    // ── Preview de imagen ───────────────────────────────
    // ── Preview de imagen interactivo ────────────────────
    document.querySelectorAll('.field-image').forEach(group => {
        const fileInput   = group.querySelector('input[type="file"]');
        const hiddenInput = group.querySelector('input[type="hidden"]');
        const box         = group.querySelector('.image-upload-box');
        const preview     = box.querySelector('.image-preview');
        const img         = preview ? preview.querySelector('img') : null;
        const placeholder = box.querySelector('.image-placeholder');
        const removeBtn   = box.querySelector('.btn-remove-image');

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
                if (placeholder) placeholder.style.display = 'flex';
                this.style.display = 'none';
            });
        }
    });

});
