<?php
// admin/pagina_custom.php
// ACCIÓN: Crear, editar y eliminar páginas personalizadas dinámicas.
// Patrón idéntico a coleccion.php / singleton.php: es UNA ACCIÓN genérica,
// no un archivo por entidad. Toda la funcionalidad de "crear página" vive aquí.
// GET ?id=pg_xxx → editar. Sin id → crear nueva.
// POST action=delete → eliminar. POST action=save → guardar.

require_once __DIR__ . '/views/layout.php';
require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/csrf.php';

$ab = admin_base();

// ────────────────────────────────────────────────────────────────────────────
// MANEJADORES POST (Guardar / Eliminar)
// ────────────────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $action = $_POST['action'] ?? '';
    $data   = storage_load();

    if (!isset($data['_paginas_creadas'])) {
        $data['_paginas_creadas'] = [];
    }

    // ── ELIMINAR ──────────────────────────────────────────────────────────
    if ($action === 'delete') {
        $id = trim($_POST['id'] ?? '');
        if ($id && isset($data['_paginas_creadas'][$id])) {
            $pg = $data['_paginas_creadas'][$id];
            unset($data['_paginas_creadas'][$id]);
            // Quitar del menú navegable público
            _pagina_custom_remove_from_menu($data, $pg['slug'] ?? '');
            storage_save($data);
            flash_set("Página '{$pg['nombre']}' eliminada.", 'success');
        } else {
            flash_set('No se encontró la página.', 'error');
        }
        header("Location: {$ab}/index.php");
        exit;
    }

    // ── GUARDAR / ACTUALIZAR ───────────────────────────────────────────────
    if ($action === 'save') {
        $id         = trim($_POST['id'] ?? '');
        $nombre     = trim($_POST['nombre'] ?? '');
        $raw_slug   = trim($_POST['slug'] ?? '');
        $icon       = trim($_POST['icon'] ?? 'bi bi-file-earmark-text');
        $menu_pos   = trim($_POST['menu_pos'] ?? 'none');
        $secciones  = array_values(array_filter((array)($_POST['secciones'] ?? [])));

        if (!$nombre) {
            flash_set('El nombre de la página no puede estar vacío.', 'error');
            $back = $id ? "pagina_custom.php?id={$id}" : 'pagina_custom.php';
            header("Location: {$ab}/{$back}");
            exit;
        }

        // Slug amigable
        $slug = strtolower($raw_slug ?: $nombre);
        $slug = preg_replace('/[^a-z0-9\-]/', '-', iconv('UTF-8', 'ASCII//TRANSLIT', $slug) ?: $slug);
        $slug = preg_replace('/-+/', '-', trim($slug, '-')) ?: ('pg-' . time());

        // ID nuevo si es creación
        if (!$id) {
            $id = 'pg_' . bin2hex(random_bytes(6));
        }

        // Asegurar prefijo "bi bi-" en el ícono
        if ($icon && strpos($icon, 'bi bi-') !== 0 && strpos($icon, 'bi-') === 0) {
            $icon = 'bi ' . $icon;
        }

        // Slug anterior (para actualizar el menú si cambió)
        $old_slug = $data['_paginas_creadas'][$id]['slug'] ?? '';
        if ($old_slug && $old_slug !== $slug) {
            _pagina_custom_remove_from_menu($data, $old_slug);
        }

        $data['_paginas_creadas'][$id] = [
            'id'         => $id,
            'nombre'     => $nombre,
            'slug'       => $slug,
            'icon'       => $icon,
            'menu_pos'   => $menu_pos,
            'secciones'  => $secciones,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Sincronizar en el menú navegable público
        _pagina_custom_sync_menu($data, $nombre, $slug . '.php', $menu_pos);

        if (storage_save($data)) {
            flash_set("Página '{$nombre}' guardada en /{$slug}.", 'success');
        } else {
            flash_set('Error al guardar en disco.', 'error');
        }

        header("Location: {$ab}/pagina_custom.php?id={$id}");
        exit;
    }

    header("Location: {$ab}/index.php");
    exit;
}

// ────────────────────────────────────────────────────────────────────────────
// FUNCIONES AUXILIARES DE MENÚ
// ────────────────────────────────────────────────────────────────────────────
function _pagina_custom_remove_from_menu(array &$data, string $slug): void {
    if (!is_array($data['menu']['items_menu'] ?? null)) return;
    $url = $slug . '.php';
    $data['menu']['items_menu'] = array_values(array_filter(
        $data['menu']['items_menu'],
        fn($i) => ($i['url'] ?? '') !== $url && ($i['url'] ?? '') !== $slug
    ));
}

function _pagina_custom_sync_menu(array &$data, string $nombre, string $url, string $menu_pos): void {
    if (!isset($data['menu'])) $data['menu'] = [];
    if (!is_array($data['menu']['items_menu'] ?? null)) $data['menu']['items_menu'] = [];

    $items = &$data['menu']['items_menu'];

    // Quitar entrada previa de esta misma URL
    $items = array_values(array_filter($items, fn($i) => ($i['url'] ?? '') !== $url));

    if ($menu_pos === 'none' || !$menu_pos) return;

    if ($menu_pos === 'padre') {
        $items[] = ['texto' => $nombre, 'url' => $url, 'nivel' => 'padre'];
        return;
    }

    if (strpos($menu_pos, 'hijo:') === 0) {
        $parent_text = substr($menu_pos, 5);
        $new = [];
        $inserted = false;
        foreach ($items as $it) {
            $new[] = $it;
            if (!$inserted && ($it['nivel'] ?? '') === 'padre' && ($it['texto'] ?? '') === $parent_text) {
                $new[] = ['texto' => $nombre, 'url' => $url, 'nivel' => 'hijo'];
                $inserted = true;
            }
        }
        if (!$inserted) $new[] = ['texto' => $nombre, 'url' => $url, 'nivel' => 'hijo'];
        $items = $new;
    }
}

// ────────────────────────────────────────────────────────────────────────────
// RENDERIZADO GET (Formulario Crear / Editar)
// ────────────────────────────────────────────────────────────────────────────
$saved_data     = storage_load();
$paginas        = $saved_data['_paginas_creadas'] ?? [];

$id             = trim($_GET['id'] ?? '');
$is_editing     = $id && isset($paginas[$id]);
$pg             = $is_editing ? $paginas[$id] : [];

$nombre         = $pg['nombre'] ?? '';
$slug           = $pg['slug'] ?? '';
$icon           = $pg['icon'] ?? 'bi bi-file-earmark-text';
$menu_pos       = $pg['menu_pos'] ?? 'none';
$secs_selected  = $pg['secciones'] ?? [];

$page_title  = $is_editing ? "Editar: {$nombre}" : 'Crear nueva página';
$current_key = $is_editing ? "custom_{$id}" : 'paginas_crear';

// Menús padre disponibles del sitio público
$public_items = $saved_data['menu']['items_menu'] ?? [
    ['texto' => 'Instituto', 'nivel' => 'padre'],
    ['texto' => 'Oferta Académica', 'nivel' => 'padre'],
    ['texto' => 'Vida Estudiantil', 'nivel' => 'padre'],
    ['texto' => 'Admisiones', 'nivel' => 'padre'],
];
$padres = array_filter($public_items, fn($i) => ($i['nivel'] ?? 'padre') === 'padre');

// Catálogo Bootstrap Icons (ampliado)
$bi_icons = [
    'bi-file-earmark-text'    => 'Página / Documento',
    'bi-mortarboard-fill'     => 'Académico',
    'bi-book-fill'            => 'Libro / Estudio',
    'bi-building'             => 'Campus / Edificio',
    'bi-award-fill'           => 'Premio / Insignia',
    'bi-globe'                => 'Global / Web',
    'bi-cpu-fill'             => 'Tecnología',
    'bi-lightbulb-fill'       => 'Innovación',
    'bi-briefcase-fill'       => 'Profesional',
    'bi-people-fill'          => 'Comunidad',
    'bi-star-fill'            => 'Destacado',
    'bi-shield-check'         => 'Calidad / Acreditación',
    'bi-calendar-event'       => 'Eventos / Agenda',
    'bi-journal-bookmark-fill'=> 'Reglamentos',
    'bi-heart-pulse-fill'     => 'Salud / Bienestar',
    'bi-pc-display'           => 'Sistemas / TIC',
    'bi-calculator-fill'      => 'Finanzas / Contabilidad',
    'bi-gear-wide-connected'  => 'Gestión / Admin',
    'bi-clipboard2-data-fill' => 'Datos / Estadísticas',
    'bi-camera-fill'          => 'Diseño / Multimedia',
    'bi-truck'                => 'Logística / Transporte',
    'bi-stethoscope'          => 'Enfermería / Medicina',
];

// Catálogo de secciones heredables (agrupadas)
// formato: clave => ['icono' => '...', 'label' => '...']
$secciones_disponibles = [
    'Sobre Nosotros' => [
        'sobre_hero'    => ['icon' => 'bi-image-fill',          'label' => 'Portada (Banner Sobre Nosotros)'],
        'mision_vision' => ['icon' => 'bi-bullseye',            'label' => 'Misión y Visión'],
        'valores'       => ['icon' => 'bi-heart-fill',          'label' => 'Nuestros Valores'],
        'autoridades'   => ['icon' => 'bi-people-fill',         'label' => 'Nuestras Autoridades'],
        'himno'         => ['icon' => 'bi-music-note-beamed',   'label' => 'Himno e Identidad Institucional'],
    ],
    'Oferta Académica' => [
        'areas'     => ['icon' => 'bi-grid-3x3-gap-fill', 'label' => 'Áreas de Formación'],
        'programas' => ['icon' => 'bi-mortarboard-fill',  'label' => 'Carreras y Programas Destacados'],
        'servicios' => ['icon' => 'bi-building',          'label' => 'Servicios e Instalaciones'],
    ],
    'Otras Secciones' => [
        'hero'          => ['icon' => 'bi-play-circle-fill',      'label' => 'Portada Principal (Hero con video)'],
        'noticias'      => ['icon' => 'bi-newspaper',             'label' => 'Noticias y Eventos'],
        'transparencia' => ['icon' => 'bi-file-earmark-pdf-fill', 'label' => 'Leyes y PDFs descargables'],
        'admision'      => ['icon' => 'bi-send-fill',             'label' => 'Formulario de Admisión'],
        'alianzas'      => ['icon' => 'bi-diagram-3-fill',        'label' => 'Alianzas y Convenios'],
    ],
];

echo layout_start($page_title, $current_key);
?>

<div class="card" style="max-width:960px;margin:0 auto;">

    <!-- Encabezado -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;border-bottom:2px solid #F4F6F9;padding-bottom:16px;">
        <div>
            <h1 style="margin:0;font-size:1.5rem;color:#1A3B70;display:flex;align-items:center;gap:10px;">
                <i id="preview_title_icon" class="<?= htmlspecialchars($icon) ?>" style="color:#F15A24;"></i>
                <?= htmlspecialchars($page_title) ?>
            </h1>
            <p style="margin:4px 0 0;color:#6B7280;font-size:.9rem;">
                Configura esta página: nombre, URL, ícono, posición en el menú y secciones heredadas.
            </p>
        </div>
        <?php if ($is_editing): ?>
        <form method="POST" id="form-delete-pagina">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
            <button type="button" class="btn btn-danger"
                    style="display:flex;align-items:center;gap:6px;"
                    onclick="customConfirm('¿Eliminar la página &quot;<?= htmlspecialchars(addslashes($nombre)) ?>&quot; permanentemente? Esta acción no se puede deshacer.', function(){ document.getElementById(\'form-delete-pagina\').submit(); })">
                <i class="bi bi-trash-fill"></i> Eliminar página
            </button>
        </form>
        <?php endif; ?>
    </div>

    <form method="POST" id="form-pagina">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <!-- BLOQUE 1: DATOS BÁSICOS -->
        <div class="singleton-panel" style="background:#F4F6F9;padding:20px;border-radius:8px;margin-bottom:24px;border-left:4px solid #1A3B70;">
            <h3 style="margin-top:0;font-size:1.05rem;color:#1A3B70;margin-bottom:16px;">
                <i class="bi bi-sliders"></i> Información General y Enlace
            </h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label class="field-label">Nombre de la Página *</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($nombre) ?>" placeholder="Ej: Academia ITB" required class="form-control" id="input_nombre">
                </div>
                <div class="form-group">
                    <label class="field-label">URL / Slug *</label>
                    <div style="display:flex;align-items:center;background:#fff;border:1px solid #D1D5DB;border-radius:6px;overflow:hidden;">
                        <span style="background:#E5E7EB;padding:10px 12px;color:#6B7280;font-size:.85rem;">/</span>
                        <input type="text" name="slug" value="<?= htmlspecialchars($slug) ?>" placeholder="web-itb-academia" required class="form-control" style="border:none;" id="input_slug">
                    </div>
                    <small style="color:#6B7280;">Solo letras minúsculas, números y guiones (sin espacios ni tildes).</small>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                <!-- SELECTOR DE ÍCONO -->
                <div class="form-group">
                    <label class="field-label">Ícono de Menú (Bootstrap Icons)</label>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <div id="icon_display_box" style="width:44px;height:44px;background:#1A3B70;color:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;">
                            <i class="<?= htmlspecialchars($icon) ?>" id="current_icon_i"></i>
                        </div>
                        <input type="hidden" name="icon" id="input_icon_val" value="<?= htmlspecialchars($icon) ?>">
                        <button type="button" class="btn btn-outline" onclick="openIconModal()" style="display:flex;align-items:center;gap:6px;">
                            <i class="bi bi-grid-3x3-gap-fill"></i> Elegir ícono...
                        </button>
                    </div>
                </div>
                <!-- UBICACIÓN EN MENÚ PÚBLICO -->
                <div class="form-group">
                    <label class="field-label">Posición en el Menú Navegable del Sitio</label>
                    <select name="menu_pos" class="form-control">
                        <option value="none" <?= $menu_pos==='none'?'selected':'' ?>>-- No agregar al menú público --</option>
                        <option value="padre" <?= $menu_pos==='padre'?'selected':'' ?>>Menú Principal (Padre)</option>
                        <optgroup label="── Submenú (Hijo) de...">
                            <?php foreach ($padres as $p): $val='hijo:'.($p['texto']??''); ?>
                            <option value="<?= htmlspecialchars($val) ?>" <?= $menu_pos===$val?'selected':'' ?>>
                                ↳ Dentro de "<?= htmlspecialchars($p['texto']) ?>"
                            </option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
            </div>
        </div>

        <!-- BLOQUE 2: SECCIONES HEREDADAS -->
        <div style="border:1px solid #E5E7EB;border-radius:8px;padding:20px;margin-bottom:24px;">
            <h3 style="margin-top:0;font-size:1.05rem;color:#1A3B70;margin-bottom:6px;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-layers-half" style="color:#F15A24;"></i> Selección de Componentes / Secciones Heredables
            </h3>
            <p style="color:#6B7280;font-size:.88rem;margin:0 0 16px;">
                Selecciona los componentes que incluirá esta página. <strong style="color:#1A3B70;">El número <span style="color:#F15A24;">●</span> indica el orden de aparición de arriba a abajo.</strong>
            </p>
            <div id="secciones-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;">
                <?php foreach ($secciones_disponibles as $grupo_nombre => $grupo_items): ?>
                <div style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:8px;padding:14px;">
                    <div style="font-weight:700;color:#0F2243;font-size:.93rem;padding-bottom:8px;margin-bottom:10px;border-bottom:2px solid #1A3B70;">
                        <?= htmlspecialchars($grupo_nombre) ?>
                    </div>
                    <?php foreach ($grupo_items as $s_key => $s_info):
                        $s_label   = $s_info['label'];
                        $s_icon    = $s_info['icon'];
                        $checked   = in_array($s_key, $secs_selected) ? 'checked' : ''; ?>
                    <label class="sec-label" style="display:flex;align-items:center;gap:10px;margin-bottom:6px;cursor:pointer;padding:8px 10px;border-radius:8px;border:1.5px solid transparent;transition:all .15s;">
                        <input type="checkbox" name="secciones[]" value="<?= $s_key ?>" <?= $checked ?>
                               class="sec-checkbox" data-label="<?= htmlspecialchars($s_label) ?>"
                               style="width:16px;height:16px;accent-color:#F15A24;flex-shrink:0;">
                        <span class="sec-text" style="font-size:.875rem;color:#1F242E;line-height:1.3;"><?= htmlspecialchars($s_label) ?></span>
                        <span class="sec-badge" style="display:none;margin-left:auto;background:#F15A24;color:#fff;font-size:.7rem;font-weight:800;min-width:22px;height:22px;border-radius:50%;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 6px rgba(241,90,36,.4);"></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- PANEL RESUMEN DE ORDEN (chips) -->
            <div id="orden-resumen" style="margin-top:16px;padding:14px 16px;border-radius:10px;border:1.5px dashed #D1D5DB;display:none;">
                <div style="font-size:.8rem;font-weight:700;color:#6B7280;letter-spacing:.08em;text-transform:uppercase;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                    <i class="bi bi-arrow-down-short" style="color:#F15A24;font-size:1rem;"></i> Orden en la página pública
                </div>
                <div id="orden-lista" style="display:flex;flex-wrap:wrap;gap:8px;"></div>
            </div>
        </div><!-- /.bloque secciones heredadas -->

        <div class="form-actions">
            <a href="<?= $ab ?>/index.php" class="btn btn-outline">Cancelar</a>
            <button type="submit" class="btn btn-primary" style="display:flex;align-items:center;gap:8px;">
                <i class="bi bi-check-circle-fill"></i>
                <?= $is_editing ? 'Guardar Cambios' : 'Crear Página Ahora' ?>
            </button>
        </div>
    </form>
</div>

<!-- MODAL SELECTOR DE ÍCONOS BOOTSTRAP -->
<div id="iconModal" style="display:none;position:fixed;inset:0;background:rgba(15,34,67,.75);backdrop-filter:blur(4px);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;width:100%;max-width:580px;border-radius:12px;padding:24px;box-shadow:0 20px 40px rgba(0,0,0,.3);max-height:80vh;display:flex;flex-direction:column;">
        <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #E5E7EB;padding-bottom:12px;margin-bottom:16px;">
            <h3 style="margin:0;color:#1A3B70;font-size:1.1rem;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-grid-3x3-gap-fill" style="color:#F15A24;"></i> Ícono de Bootstrap Icons
            </h3>
            <button type="button" onclick="closeIconModal()" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:#6B7280;">&times;</button>
        </div>
        <input type="text" id="icon_search_input" placeholder="Buscar ícono..." oninput="filterIcons(this.value)"
               style="width:100%;padding:10px;border:1px solid #D1D5DB;border-radius:6px;margin-bottom:16px;">
        <div id="icon_grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:10px;overflow-y:auto;">
            <?php foreach ($bi_icons as $bi_class => $bi_label): ?>
            <div class="icon-option-card" onclick="selectIcon('<?= $bi_class ?>')"
                 style="border:1px solid #E2E8F0;border-radius:8px;padding:12px 8px;text-align:center;cursor:pointer;transition:all .2s;"
                 data-name="<?= strtolower("{$bi_class} {$bi_label}") ?>">
                <i class="bi <?= $bi_class ?>" style="font-size:1.8rem;color:#1A3B70;display:block;margin-bottom:4px;"></i>
                <span style="font-size:.72rem;color:#6B7280;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($bi_label) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.icon-option-card:hover { border-color: #F15A24 !important; background: #FFF5F2 !important; transform: translateY(-2px); }
.sec-label:hover  { background: #F0F4FF; border-color: #CBD5E1 !important; }
.sec-label.is-checked { background: #FFF5F2 !important; border-color: #F15A24 !important; }
</style>
<script>
// Auto-slug desde el nombre
document.getElementById('input_nombre').addEventListener('input', function () {
    const s = document.getElementById('input_slug');
    if (s.dataset.manual) return;
    s.value = this.value.toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s\-]/g, '')
        .trim().replace(/\s+/g, '-');
});
document.getElementById('input_slug').addEventListener('input', function () {
    this.dataset.manual = '1';
});

function openIconModal()  { document.getElementById('iconModal').style.display = 'flex'; }
function closeIconModal() { document.getElementById('iconModal').style.display = 'none'; }
function selectIcon(cls) {
    const full = 'bi ' + cls;
    document.getElementById('input_icon_val').value = full;
    document.getElementById('current_icon_i').className = full;
    document.getElementById('preview_title_icon').className = full;
    closeIconModal();
}
// ── NUMERACIÓN VISUAL DE ORDEN DE SECCIONES ──────────────────────────────────
// Lee todos los checkboxes en el orden del DOM (izquierda→derecha, arriba→abajo),
// que es exactamente el orden en que pagina_dinamica.php los incluye.
// Por cada checkbox marcado pinta un badge naranja con su posición y actualiza
// el panel de resumen inferior.
(function () {

    function actualizarOrden() {
        const checkboxes = document.querySelectorAll('.sec-checkbox');
        let pos = 1;
        const resumenLista = document.getElementById('orden-lista');
        const resumenPanel = document.getElementById('orden-resumen');
        resumenLista.innerHTML = '';

        checkboxes.forEach(function (cb) {
            const label  = cb.closest('.sec-label');
            const badge  = label.querySelector('.sec-badge');
            const icono  = label.querySelector('.bi:not(.sec-badge .bi)');
            const texto  = cb.dataset.label || cb.value;

            if (cb.checked) {
                badge.textContent = pos;
                badge.style.display = 'flex';
                label.classList.add('is-checked');

                // Chip en el resumen
                const chip = document.createElement('span');
                chip.style.cssText = 'display:inline-flex;align-items:center;gap:5px;background:#fff;border:1.5px solid #E2E8F0;border-radius:20px;padding:4px 12px 4px 8px;font-size:.8rem;color:#1A3B70;font-weight:600;box-shadow:0 1px 3px rgba(0,0,0,.07);';
                chip.innerHTML = '<span style="background:#F15A24;color:#fff;font-weight:800;font-size:.68rem;min-width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">' + pos + '</span>' + texto;
                resumenLista.appendChild(chip);

                pos++;
            } else {
                badge.style.display = 'none';
                label.classList.remove('is-checked');
            }
        });

        resumenPanel.style.display = pos > 1 ? 'block' : 'none';
    }

    // Inicializar al cargar (para páginas en edición ya guardadas)
    actualizarOrden();

    // Re-calcular en cada cambio
    document.getElementById('secciones-grid').addEventListener('change', function (e) {
        if (e.target.classList.contains('sec-checkbox')) actualizarOrden();
    });
}());
</script>

<?php echo layout_end(); ?>
