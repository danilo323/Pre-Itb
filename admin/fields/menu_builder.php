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
