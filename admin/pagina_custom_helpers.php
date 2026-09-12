<?php
// Utilidades compartidas para las páginas creadas desde el panel.

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
