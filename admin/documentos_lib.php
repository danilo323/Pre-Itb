<?php
// admin/documentos_lib.php
//
// Biblioteca de imÃ¡genes: el Ãºnico sitio del panel por donde entra una documento.
//
// La lista NO se guarda en ningÃºn Ã­ndice: es el contenido real de la carpeta
// /img leÃ­do en el momento. AsÃ­ nunca se puede desincronizar (si alguien copia
// o borra un archivo por FTP, la biblioteca lo refleja sin mÃ¡s), y las 100 y
// pico imÃ¡genes que ya traÃ­a el sitio aparecen solas el primer dÃ­a.
//
// Lo usan dos sitios y por eso vive aparte:
//   - admin/biblioteca.php ....... la pantalla (subir, buscar, eliminar).
//   - admin/fields/image.php ..... el selector que sale en cada secciÃ³n.

require_once __DIR__ . '/../includes/content_helper.php';

/** Carpeta fÃ­sica donde viven las imÃ¡genes del sitio. */
function documentos_dir(): string {
    $dir = dirname(__DIR__) . '/docs';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    return $dir;
}

/** Extensiones que la biblioteca acepta y muestra. */
function documentos_extensiones(): array {
    return ['pdf', 'xml', 'doc', 'docx', 'xls', 'xlsx'];
}

/**
 * Convierte lo que se guarda en el contenido ('img/foto.jpg') en el nombre de
 * archivo, y de paso corta cualquier intento de salirse de /img con '../'.
 * Devuelve '' si la ruta no es una documento de la biblioteca.
 */
function documentos_nombre_de_ruta(?string $ruta): string {
    $ruta = trim(str_replace('\\', '/', (string) $ruta));
    if ($ruta === '') return '';

    // La carpeta tiene que ser /img o ninguna. Sin esta comprobaciÃ³n, un
    // 'uploads/foto.jpg' se convertirÃ­a en 'img/foto.jpg' â€” una ruta que
    // seguramente no existe â€” en vez de rechazarse y conservar la anterior.
    $carpeta = trim(dirname($ruta), '/');
    if ($carpeta !== '' && $carpeta !== '.' && strtolower($carpeta) !== 'docs') return '';

    // basename() se queda con el Ãºltimo tramo, asÃ­ que 'img/../../x' no puede
    // escapar de la carpeta pase lo que pase.
    $nombre = basename($ruta);
    if ($nombre === '' || $nombre === '.' || $nombre === '..') return '';
    $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
    if (!in_array($ext, documentos_extensiones(), true)) return '';
    return $nombre;
}

/**
 * Comprueba que un valor guardado en un campo de documento sea aceptable.
 *
 * Se valida la FORMA (que sea 'img/<archivo>.<ext permitida>'), no que el
 * archivo siga existiendo: si alguien borra una foto por FTP, el campo debe
 * conservar su ruta y enseÃ±ar el aviso de "documento no encontrada", no perder
 * el dato en silencio la prÃ³xima vez que se guarde esa secciÃ³n.
 */
function documentos_ruta_valida(?string $ruta): bool {
    return documentos_nombre_de_ruta($ruta) !== '';
}

/** Ruta tal y como se guarda en el contenido: 'img/<archivo>'. */
function documentos_ruta(string $nombre): string {
    return 'docs/' . $nombre;
}

/**
 * Todas las imÃ¡genes de la biblioteca, de la mÃ¡s reciente a la mÃ¡s antigua
 * (que es el orden Ãºtil: lo que acabas de subir sale primero).
 *
 * @param string $buscar Filtra por nombre de archivo. VacÃ­o = todas.
 */
function documentos_listar(string $buscar = ''): array {
    $dir = documentos_dir();
    $to_lower = fn(string $s): string => function_exists('mb_strtolower') ? mb_strtolower($s, 'UTF-8') : strtolower($s);
    $buscar = $to_lower(trim($buscar));
    $items = [];

    foreach (@scandir($dir) ?: [] as $nombre) {
        if ($nombre === '.' || $nombre === '..') continue;
        $ruta_fisica = $dir . '/' . $nombre;
        if (!is_file($ruta_fisica)) continue;

        $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
        if (!in_array($ext, documentos_extensiones(), true)) continue;
        if ($buscar !== '' && !str_contains($to_lower($nombre), $buscar)) continue;

        $items[] = [
            'nombre' => $nombre,
            'ruta'   => documentos_ruta($nombre),
            'ext'    => $ext,
            'peso'   => (int) @filesize($ruta_fisica),
            'fecha'  => (int) @filemtime($ruta_fisica),
        ];
    }

    usort($items, fn($a, $b) => $b['fecha'] <=> $a['fecha']);
    return $items;
}

/** "1,2 MB" / "340 KB", para enseÃ±ar el peso sin que asuste. */
function documentos_peso_legible(int $bytes): string {
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 1, ',', '.') . ' MB';
    if ($bytes >= 1024)    return number_format($bytes / 1024, 0, ',', '.') . ' KB';
    return $bytes . ' B';
}

/**
 * DÃ³nde se estÃ¡ usando una documento. Recorre TODO el contenido guardado
 * (secciones, colecciones y pÃ¡ginas creadas a mano) buscando su ruta, para
 * poder avisar antes de borrar algo que estÃ¡ publicado.
 *
 * @return array Nombres de las secciones que la usan, sin repetir.
 */
function documentos_usos(string $ruta): array {
    static $datos = null;
    if ($datos === null) {
        require_once __DIR__ . '/storage.php';
        $datos = storage_load();
    }

    $ruta = trim($ruta);
    if ($ruta === '') return [];

    $schema = require __DIR__ . '/schema_mock.php';
    $usos = [];

    foreach ($datos as $seccion => $valores) {
        if (!is_array($valores)) continue;

        $encontrado = false;
        array_walk_recursive($valores, function ($v) use ($ruta, &$encontrado) {
            if (!$encontrado && is_string($v) && $v === $ruta) $encontrado = true;
        });
        if (!$encontrado) continue;

        // Nombre bonito si el schema lo conoce; si no, la clave cruda (pasa con
        // las secciones de las pÃ¡ginas creadas desde el panel).
        $usos[] = $schema['items'][$seccion]['label']
            ?? documentos_etiqueta_de_seccion($schema, $seccion)
            ?? $seccion;
    }

    // Una documento puede no estar en los datos guardados y aun asÃ­ salir en la
    // web: es la que el schema trae como 'default' de un campo, y se usa
    // mientras esa secciÃ³n no se haya guardado nunca. Borrarla romperÃ­a la
    // pÃ¡gina igual, asÃ­ que tambiÃ©n cuenta como en uso.
    $en_schema = false;
    array_walk_recursive($schema, function ($v) use ($ruta, &$en_schema) {
        if (!$en_schema && is_string($v) && $v === $ruta) $en_schema = true;
    });
    if ($en_schema) $usos[] = 'valores por defecto del panel';

    return array_values(array_unique($usos));
}

/**
 * Busca la etiqueta de una secciÃ³n que vive DENTRO de una pÃ¡gina
 * (schema['items'][pagina]['sections'][seccion]), para que el aviso diga
 * "Inicio â†’ PROGRAMAS DESTACADOS" y no 'programas' a secas.
 */
function documentos_etiqueta_de_seccion(array $schema, string $seccion): ?string {
    foreach ($schema['items'] ?? [] as $item) {
        if (empty($item['sections'][$seccion])) continue;
        return ($item['label'] ?? '?') . ' â†’ ' . ($item['sections'][$seccion]['label'] ?? $seccion);
    }
    return null;
}

/**
 * Guarda UNA documento subida en la biblioteca.
 *
 * Es el antiguo cuerpo de field_image_parse(): mismas comprobaciones contra
 * subidas maliciosas (extensiÃ³n, MIME real, tamaÃ±o, nombre aleatorio) y la
 * misma compresiÃ³n con GD. Se mudÃ³ aquÃ­ para que exista UNA sola implementaciÃ³n
 * ahora que las imÃ¡genes ya no entran por los formularios de secciÃ³n.
 *
 * @param array $archivo Una entrada de $_FILES.
 * @return array ['ok' => bool, 'ruta' => string, 'error' => string]
 */
function documentos_guardar_subida(array $archivo): array {
    $fallo = fn(string $msg) => ['ok' => false, 'ruta' => '', 'error' => $msg];

    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        // El caso tÃ­pico: el archivo pasa del lÃ­mite de PHP (upload_max_filesize).
        if (($archivo['error'] ?? null) === UPLOAD_ERR_INI_SIZE) {
            return $fallo('El archivo es demasiado grande para el servidor.');
        }
        return $fallo('No se pudo recibir el archivo.');
    }

    $tmp_name      = $archivo['tmp_name'];
    $original_name = basename((string) $archivo['name']);
    $file_size     = (int) ($archivo['size'] ?? 0);

    // A. Lista blanca estricta de extensiones.
    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    if (!in_array($ext, documentos_extensiones(), true)) {
        return $fallo("Â«{$original_name}Â»: la extensiÃ³n .{$ext} no estÃ¡ permitida.");
    }

    // B. Tipo MIME real con finfo
    $allowed_mimes = [
        'application/pdf', 
        'text/xml', 'application/xml',
        'application/msword', 
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];
    $mime = false;
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? finfo_file($finfo, $tmp_name) : false;
        if ($finfo) finfo_close($finfo);
    } elseif (function_exists('mime_content_type')) {
        $mime = @mime_content_type($tmp_name);
    }
    if ($mime !== false && !in_array($mime, $allowed_mimes, true)) {
        return $fallo("«{$original_name}»: no parece un documento válido ({$mime}).");
    }

    // C. Tamaño máximo.
    $config_sys = file_exists(__DIR__ . '/config.php') ? (require __DIR__ . '/config.php') : [];
    $max_mb = (int) ($config_sys['max_upload_mb'] ?? 32);
    if ($file_size > $max_mb * 1024 * 1024) {
        return $fallo("«{$original_name}»: supera el máximo de {$max_mb} MB.");
    }

    $upload_dir = documentos_dir() . '/';

    // D. Guardar tal cual, con nombre aleatorio.
    $nombre = bin2hex(random_bytes(16)) . '.' . $ext;
    if (!move_uploaded_file($tmp_name, $upload_dir . $nombre)) {
        return $fallo("Â«{$original_name}Â»: no se pudo guardar en el servidor.");
    }
    return ['ok' => true, 'ruta' => documentos_ruta($nombre), 'error' => ''];
}

/**
 * Borra una documento de la biblioteca.
 *
 * Se niega si la documento estÃ¡ puesta en alguna secciÃ³n: borrarla dejarÃ­a un
 * hueco en la web pÃºblica y el panel no tendrÃ­a cÃ³mo avisar despuÃ©s.
 */
function documentos_eliminar(string $ruta): array {
    $nombre = documentos_nombre_de_ruta($ruta);
    if ($nombre === '') return ['ok' => false, 'error' => 'Esa documento no es vÃ¡lida.'];

    $usos = documentos_usos(documentos_ruta($nombre));
    if (!empty($usos)) {
        return ['ok' => false, 'error' => 'No se puede eliminar: la usa ' . implode(', ', $usos) . '. QuÃ­tala de ahÃ­ primero.'];
    }

    $ruta_fisica = documentos_dir() . '/' . $nombre;
    if (!is_file($ruta_fisica)) return ['ok' => false, 'error' => 'Esa documento ya no existe.'];
    if (!@unlink($ruta_fisica)) return ['ok' => false, 'error' => 'No se pudo eliminar el archivo.'];

    return ['ok' => true, 'error' => ''];
}


