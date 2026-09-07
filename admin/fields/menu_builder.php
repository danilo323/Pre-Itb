<?php
// admin/fields/menu_builder.php

function field_menu_builder_render(string $name_path, $value, array $config): string {
    $items = is_array($value) ? $value : [];
    
    // HTML y Estilos
    ob_start();
    ?>
    <div class="menu-builder-wrapper" data-name="<?= htmlspecialchars($name_path, ENT_QUOTES, 'UTF-8') ?>">
        <div class="menu-builder-header">
            <h4><?= htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8') ?></h4>
            <div class="header-actions">
                <button type="button" class="btn-add-menu-item" title="Añadir Item Principal"><i class="bi bi-plus-lg"></i></button>
            </div>
        </div>
        
        <?php if (!empty($config['help'])): ?>
            <p class="field-help"><?= htmlspecialchars($config['help'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <div class="menu-builder-items">
            <?php foreach ($items as $index => $item): ?>
                <?php 
                $texto = htmlspecialchars($item['texto'] ?? '', ENT_QUOTES, 'UTF-8');
                $url = htmlspecialchars($item['url'] ?? '', ENT_QUOTES, 'UTF-8');
                $nivel = htmlspecialchars($item['nivel'] ?? 'padre', ENT_QUOTES, 'UTF-8');
                $is_hijo = $nivel === 'hijo' ? 'is-hijo' : '';
                ?>
                <div class="menu-builder-item <?= $is_hijo ?>" data-index="<?= $index ?>">
                    <div class="mb-display-row">
                        <div class="mb-drag-handle">
                            <span class="mb-indent-dash">—</span>
                            <span class="mb-number">#<?= $index + 1 ?></span>
                        </div>
                        <div class="mb-title-display"><?= $texto === '' ? 'Nuevo Item' : $texto ?></div>
                        <div class="mb-actions">
                            <button type="button" class="mb-btn mb-btn-eye" title="Ocultar"><i class="bi bi-eye"></i></button>
                            <button type="button" class="mb-btn mb-btn-indent-left" title="Nivel Padre"><i class="bi bi-arrow-bar-left"></i></button>
                            <button type="button" class="mb-btn mb-btn-indent-right" title="Nivel Hijo"><i class="bi bi-arrow-bar-right"></i></button>
                            <button type="button" class="mb-btn mb-btn-add-child" title="Añadir Sub-item"><i class="bi bi-plus"></i></button>
                            <button type="button" class="mb-btn mb-btn-up" title="Subir"><i class="bi bi-chevron-up"></i></button>
                            <button type="button" class="mb-btn mb-btn-down" title="Bajar"><i class="bi bi-chevron-down"></i></button>
                            <button type="button" class="mb-btn mb-btn-copy" title="Duplicar"><i class="bi bi-files"></i></button>
                            <button type="button" class="mb-btn mb-btn-edit" title="Editar"><i class="bi bi-pencil"></i></button>
                            <button type="button" class="mb-btn mb-btn-remove" title="Eliminar"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                    <div class="mb-edit-row">
                        <div class="mb-edit-fields">
                            <input type="text" name="<?= $name_path ?>[<?= $index ?>][texto]" value="<?= $texto ?>" placeholder="Texto del enlace (Ej: Quienes Somos)" class="mb-input-text" required>
                            <input type="text" name="<?= $name_path ?>[<?= $index ?>][url]" value="<?= $url ?>" placeholder="URL (Ej: /quienes-somos)" class="mb-input-url">
                            <input type="hidden" name="<?= $name_path ?>[<?= $index ?>][nivel]" value="<?= $nivel ?>" class="mb-input-nivel">
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary mb-btn-close-edit">Listo</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Template oculto para nuevos items -->
        <template class="menu-builder-template">
            <div class="menu-builder-item" data-index="{INDEX}">
                <div class="mb-display-row">
                    <div class="mb-drag-handle">
                        <span class="mb-indent-dash">—</span>
                        <span class="mb-number">#{NUM}</span>
                    </div>
                    <div class="mb-title-display">Nuevo Item</div>
                    <div class="mb-actions">
                        <button type="button" class="mb-btn mb-btn-eye" title="Ocultar"><i class="bi bi-eye"></i></button>
                        <button type="button" class="mb-btn mb-btn-indent-left" title="Nivel Padre"><i class="bi bi-arrow-bar-left"></i></button>
                        <button type="button" class="mb-btn mb-btn-indent-right" title="Nivel Hijo"><i class="bi bi-arrow-bar-right"></i></button>
                        <button type="button" class="mb-btn mb-btn-add-child" title="Añadir Sub-item"><i class="bi bi-plus"></i></button>
                        <button type="button" class="mb-btn mb-btn-up" title="Subir"><i class="bi bi-chevron-up"></i></button>
                        <button type="button" class="mb-btn mb-btn-down" title="Bajar"><i class="bi bi-chevron-down"></i></button>
                        <button type="button" class="mb-btn mb-btn-copy" title="Duplicar"><i class="bi bi-files"></i></button>
                        <button type="button" class="mb-btn mb-btn-edit" title="Editar"><i class="bi bi-pencil"></i></button>
                        <button type="button" class="mb-btn mb-btn-remove" title="Eliminar"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
                <div class="mb-edit-row">
                    <div class="mb-edit-fields">
                        <input type="text" name="<?= $name_path ?>[{INDEX}][texto]" value="" placeholder="Texto del enlace" class="mb-input-text" required disabled>
                        <input type="text" name="<?= $name_path ?>[{INDEX}][url]" value="" placeholder="URL" class="mb-input-url" disabled>
                        <input type="hidden" name="<?= $name_path ?>[{INDEX}][nivel]" value="padre" class="mb-input-nivel" disabled>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary mb-btn-close-edit">Listo</button>
                </div>
            </div>
        </template>

        <style>
            .menu-builder-wrapper {
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                margin-bottom: 30px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            }
            .menu-builder-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 16px 24px;
                border-bottom: 1px solid #e2e8f0;
            }
            .menu-builder-header h4 {
                margin: 0;
                font-size: 15px;
                font-weight: 600;
                color: #1e293b;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .btn-add-menu-item {
                background: none;
                border: none;
                color: #f97316; /* Orange ITB */
                font-size: 18px;
                cursor: pointer;
                transition: color 0.2s;
            }
            .btn-add-menu-item:hover { color: #ea580c; }
            .menu-builder-wrapper .field-help {
                padding: 16px 24px 0;
                margin: 0;
                color: #64748b;
                font-size: 14px;
                line-height: 1.6;
            }
            .menu-builder-items {
                padding: 16px 0;
            }
            .menu-builder-item {
                border-bottom: 1px solid #f1f5f9;
                transition: background 0.2s;
            }
            .menu-builder-item:last-child {
                border-bottom: none;
            }
            .menu-builder-item:hover {
                background: #f8fafc;
            }
            .mb-display-row {
                display: flex;
                align-items: center;
                padding: 12px 24px;
                min-height: 56px;
            }
            .mb-drag-handle {
                display: flex;
                align-items: center;
                width: 60px;
                color: #64748b;
                font-size: 13px;
                font-weight: 500;
                transition: margin-left 0.2s ease;
            }
            .mb-indent-dash {
                display: none;
                color: #cbd5e1;
                margin-right: 8px;
                font-weight: 300;
            }
            .menu-builder-item.is-hijo .mb-drag-handle {
                margin-left: 40px;
                width: 100px;
            }
            .menu-builder-item.is-hijo .mb-indent-dash {
                display: inline-block;
            }
            .mb-title-display {
                flex: 1;
                font-weight: 600;
                color: #1e3a8a; /* Dark blue ITB */
                font-size: 15px;
            }
            .mb-actions {
                display: flex;
                gap: 4px;
                opacity: 1; /* Siempre visible */
            }
            .mb-btn {
                background: transparent;
                border: none;
                color: #9ca3af;
                cursor: pointer;
                padding: 6px;
                border-radius: 4px;
                font-size: 15px;
                transition: all 0.2s;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .mb-btn:hover { background: #fff7ed; color: #f97316; } /* Orange ITB */
            .mb-btn-remove:hover { background: #fee2e2; color: #ef4444; }
            
            .mb-edit-row {
                display: none;
                padding: 16px 24px 16px 84px;
                background: #f8fafc;
                border-top: 1px dashed #e2e8f0;
                align-items: flex-start;
                gap: 16px;
            }
            .menu-builder-item.is-hijo .mb-edit-row {
                padding-left: 124px;
            }
            .menu-builder-item.is-editing .mb-edit-row {
                display: flex;
            }
            .menu-builder-item.is-editing .mb-display-row {
                background: #f8fafc;
            }
            .mb-edit-fields {
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            .mb-input-text, .mb-input-url {
                width: 100%;
                padding: 8px 12px;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                font-size: 14px;
                background: #fff;
            }
            .mb-input-text:focus, .mb-input-url:focus {
                outline: none;
                border-color: #f97316; /* Orange ITB */
                box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
            }
        </style>

        <script>
            (function() {
                const scripts = document.querySelectorAll('script');
                const container = scripts[scripts.length - 1].closest('.menu-builder-wrapper');
                if (!container) return;
                
                const itemsContainer = container.querySelector('.menu-builder-items');
                const btnAdd = container.querySelector('.btn-add-menu-item');
                const template = container.querySelector('.menu-builder-template');

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
            })();
        </script>
    </div>
    <?php
    return ob_get_clean();
}

function field_menu_builder_parse($raw, array $config) {
    if (!is_array($raw)) return [];
    
    $clean = [];
    foreach ($raw as $item) {
        $clean[] = [
            'texto' => htmlspecialchars(trim($item['texto'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'url' => htmlspecialchars(trim($item['url'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'nivel' => in_array($item['nivel'] ?? '', ['padre', 'hijo']) ? $item['nivel'] : 'padre'
        ];
    }
    return $clean;
}
