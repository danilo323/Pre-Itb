<?php
// Utilidades compartidas para las páginas creadas desde el panel.

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
    $snapshot = [];
    foreach (pagina_custom_section_configs($secciones) as $section => $config) {
        $values = $data[$section] ?? [];
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
