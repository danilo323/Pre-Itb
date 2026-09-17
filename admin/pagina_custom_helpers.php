<?php
// Utilidades compartidas para las páginas creadas desde el panel.

/**
 * Arma el árbol del menú público (items_menu) para el selector de "Posición
 * en el Menú": cada nodo trae sus hijos directos en 'children' y su propia
 * 'ruta' (ej. "Instituto>Sobre Nosotros"), que es como el selector identifica
 * "insertar aquí adentro" sin importar cuántos niveles de profundidad tenga.
 * Usa menu_builder_profundidad() (admin/fields/menu_builder.php) para leer
 * tanto el formato viejo ('padre'/'hijo'/'nieto') como el nuevo (número).
 */
function pagina_custom_menu_arbol(array $items): array {
    $raiz = [];
    $pila = [];
    $pila[0] = &$raiz;
    $ruta_por_nivel = [];
    foreach ($items as $item) {
        $texto = trim($item['texto'] ?? '');
        if ($texto === '') continue;
        $prof = menu_builder_profundidad($item['nivel'] ?? 0);
        if ($prof > count($pila) - 1) $prof = count($pila) - 1;

        $ruta_padre = $prof > 0 ? ($ruta_por_nivel[$prof - 1] ?? '') : '';
        $ruta = ($ruta_padre !== '' ? $ruta_padre . '>' : '') . $texto;

        $nodo = $item;
        $nodo['children'] = [];
        $nodo['ruta'] = $ruta;
        $pila[$prof][] = $nodo;

        for ($k = count($pila) - 1; $k > $prof; $k--) {
            unset($pila[$k]);
            unset($ruta_por_nivel[$k]);
        }
        $ultimo = &$pila[$prof][count($pila[$prof]) - 1];
        $pila[$prof + 1] = &$ultimo['children'];
        $ruta_por_nivel[$prof] = $ruta;
    }
    return $raiz;
}

/**
 * Pinta el árbol del selector de posición: nivel 0 son los <details> de cada
 * menú principal; de ahí para adentro, cada nodo es una fila que puede a su
 * vez tener sus propios hijos (sin límite), indentados un poco más cada vez.
 * Cada fila trae su propio botón "+" para colgar la página nueva justo
 * debajo de ESE nodo, sin importar en qué nivel esté.
 */
function pagina_custom_menu_pintar(array $nodos, int $profundidad = 0): string {
    $html = '';
    foreach ($nodos as $nodo) {
        $texto = htmlspecialchars($nodo['texto'] ?? '', ENT_QUOTES, 'UTF-8');
        if ($texto === '') continue;
        $ruta = htmlspecialchars($nodo['ruta'], ENT_QUOTES, 'UTF-8');
        $valor_agregar = htmlspecialchars('bajo:' . $nodo['ruta'], ENT_QUOTES, 'UTF-8');
        $hijos = $nodo['children'] ?? [];
        $hijos_html = pagina_custom_menu_pintar($hijos, $profundidad + 1);

        if ($profundidad === 0) {
            $html .= '<details class="page-menu-placement__parent-row" data-menu-pos="' . $valor_agregar . '">';
            $html .= '<summary class="page-menu-placement__parent"><span class="page-menu-placement__number">#</span>';
            $html .= '<strong>' . $texto . '</strong>';
            $html .= '<span class="page-menu-placement__action"><i class="bi bi-plus-lg"></i> Añadir aquí</span></summary>';
            $html .= '<div class="page-menu-placement__children">' . $hijos_html . '</div>';
            $html .= '</details>';
        } else {
            $html .= '<div class="page-menu-placement__existing-child" data-nivel="' . $profundidad . '" data-ruta="' . $ruta . '" data-child="' . $texto . '">';
            $html .= '<i class="bi bi-arrow-return-right"></i><span>' . $texto . '</span>';
            $html .= '<span class="page-menu-placement__row-actions">';
            $html .= '<button type="button" class="js-menu-position" data-direction="before" title="Colocar la página antes"><i class="bi bi-chevron-up"></i></button>';
            $html .= '<button type="button" class="js-menu-position" data-direction="after" title="Colocar la página después"><i class="bi bi-chevron-down"></i></button>';
            $html .= '<button type="button" class="js-menu-add-nieto" data-menu-pos="' . $valor_agregar . '" title="Agregar como sub-item de «' . $texto . '»"><i class="bi bi-plus-lg"></i></button>';
            $html .= '</span></div>';
            if (!empty($hijos)) {
                $html .= '<div class="page-menu-placement__children" data-parent-ruta="' . $ruta . '">' . $hijos_html . '</div>';
            }
        }
    }
    return $html;
}

/**
 * Una sección elegida puede venir de dos sitios, y la clave lo dice:
 *   'valores'            → la sección Valores tal y como está en el sitio.
 *   'pg_ab12ef:valores'  → la sección Valores tal y como la tiene la página
 *                          creada pg_ab12ef (con SU texto, no el del sitio).
 * Son casillas distintas en el panel, pero acaban pintando el mismo componente.
 * Esta función parte la clave en [origen, sección]; sin prefijo, origen = ''.
 */
function pagina_custom_split(string $clave): array {
    $corte = strpos($clave, ':');
    if ($corte === false) return ['', $clave];
    return [substr($clave, 0, $corte), substr($clave, $corte + 1)];
}

/** Solo la parte de sección de la clave ('pg_ab12ef:valores' → 'valores'). */
function pagina_custom_base(string $clave): string {
    return pagina_custom_split($clave)[1];
}

/**
 * Traduce la posición de menú guardada al formato actual.
 *
 * Antes se guardaba 'hijo:<texto>', que significaba "cuelga de la opción
 * principal llamada <texto>". Hoy eso se expresa como 'bajo:<ruta>', donde la
 * ruta puede tener varios tramos separados por '>'. Para un solo tramo son
 * exactamente lo mismo.
 *
 * Sin esta traducción, las páginas guardadas con el formato antiguo no casaban
 * con ninguna rama de _pagina_custom_sync_menu() y caían en el 'none'
 * implícito: DESAPARECÍAN del menú al volver a guardarlas. En los datos reales
 * hay 5 páginas de 6 en esa situación.
 *
 * No reescribe nada por su cuenta: normaliza al leer y al sincronizar, y el
 * valor guardado se actualiza solo la próxima vez que se guarde esa página.
 */
function pagina_custom_menu_pos_normalizar(string $menu_pos): string {
    $menu_pos = trim($menu_pos);

    if (strpos($menu_pos, 'hijo:') === 0) {
        return 'bajo:' . substr($menu_pos, 5);
    }

    // 'nieto:<texto>' nunca llegó a usarse en los datos, pero se contempla por
    // si quedara alguno suelto: es un tramo más de la misma ruta.
    if (strpos($menu_pos, 'nieto:') === 0) {
        return 'bajo:' . substr($menu_pos, 6);
    }

    return $menu_pos;
}

function pagina_custom_sources(array $secciones): array {
    $mapa = [
        'hero' => ['hero'], 'sobre_hero' => ['sobre_hero'],
        'presentacion' => ['sobre_intro'],
        'mision_vision' => ['sobre_mision', 'sobre_vision'],
        'valores' => ['sobre_valores'], 'autoridades' => ['autoridades'],
        'cogobierno' => ['sobre_cogobierno'], 'himno' => ['himno'],
        'areas' => ['areas'], 'programas' => ['programas'],
        'servicios' => ['servicios'], 'noticias' => ['noticias'],
        'transparencia' => ['transparencia_leyes'], 'admision' => ['admision'],
        'alianzas' => ['alianzas'],
    ];

    $sources = [];
    foreach ($secciones as $seccion) {
        // La clave puede traer delante la página de la que se hereda.
        $seccion = pagina_custom_base((string)$seccion);
        foreach ($mapa[$seccion] ?? [] as $source) $sources[] = $source;
    }
    return array_values(array_unique($sources));
}

function pagina_custom_section_configs(array $secciones): array {
    $schema = require __DIR__ . '/schema_mock.php';
    $wanted = array_flip(pagina_custom_sources($secciones));
    $configs = [];

    foreach ($schema['items'] as $page) {
        foreach (($page['sections'] ?? []) as $key => $config) {
            if (isset($wanted[$key])) $configs[$key] = $config;
        }
    }
    return $configs;
}

function pagina_custom_snapshot(array $data, array $secciones): array {
    // De qué página sale cada sección. Si la clave trae origen, los textos e
    // imágenes se copian de ESA página; si no, de la sección del sitio.
    $origen_de = [];
    $bases = [];
    foreach ($secciones as $clave) {
        [$origen, $base] = pagina_custom_split((string)$clave);
        $bases[] = $base;
        foreach (pagina_custom_sources([$base]) as $source) $origen_de[$source] = $origen;
    }

    $snapshot = [];
    foreach (pagina_custom_section_configs($bases) as $section => $config) {
        $origen = $origen_de[$section] ?? '';
        // Copia de la página de origen; si esa página ya no existe (la
        // borraron), se vuelve al contenido del sitio en vez de quedarse vacío.
        $fuente = ($origen !== '' && isset($data['_paginas_creadas'][$origen]['contenido']))
            ? (array)$data['_paginas_creadas'][$origen]['contenido']
            : $data;
        $values = $fuente[$section] ?? ($data[$section] ?? []);
        foreach (($config['fields'] ?? []) as $field => $field_config) {
            if (!array_key_exists($field, $values) && array_key_exists('default', $field_config)) {
                $values[$field] = $field_config['default'];
            }
        }
        // La copia debe seguir siendo visible aunque la sección original se oculte.
        if (!array_key_exists('_visible', $values)) $values['_visible'] = '1';
        $snapshot[$section] = $values;
    }
    return $snapshot;
}
