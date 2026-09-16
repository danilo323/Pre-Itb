<?php
// includes/content_helper.php
// Lee el contenido editable (Versión 2.0)

require_once dirname(__DIR__) . '/admin/storage.php';

/**
 * Ruta del archivo persistente JSON (data/content.json).
 */
function content_storage_file(): string {
    return storage_file();
}

/**
 * Carga los datos almacenados en disco (con soporte de semilla).
 */
function content_storage_load(): array {
    return storage_load();
}

/**
 * Obtiene los datos vivos directamente del almacenamiento / Base de Datos MySQL.
 * Se cachea en memoria únicamente durante la petición actual (request-scoped).
 */
function content_all_data(): array {
    static $live_data = null;
    // Si el caché fue invalidado (tras un guardado), forzar recarga
    if ($live_data !== null && empty($GLOBALS['_content_cache_cleared'])) {
        return $live_data;
    }
    unset($GLOBALS['_content_cache_cleared']);
    $live_data = content_storage_load();
    // Una página personalizada puede llevar su propia copia de las secciones
    // heredadas. Se superpone solo durante el renderizado de esa página.
    $custom_content = $GLOBALS['CURRENT_DYNAMIC_PAGE']['contenido'] ?? null;
    if (is_array($custom_content)) {
        foreach ($custom_content as $section => $values) {
            if (is_array($values)) {
                $live_data[$section] = array_replace($live_data[$section] ?? [], $values);
            }
        }
    }
    return $live_data;
}

/**
 * Invalida el caché de request de content_all_data().
 * Se debe llamar después de cualquier guardado para que las lecturas
 * posteriores obtengan los datos actualizados.
 */
function content_cache_clear(): void {
    // Resetear la variable estática trick: llamar con un valor centinela
    // PHP no permite resetear static directamente, así que usamos una variable global.
    $GLOBALS['_content_cache_cleared'] = true;
}

/**
 * Guarda el array de datos permanentemente en disco y en MySQL.
 */
function content_storage_save(?array $data = null): bool {
    if ($data === null) {
        if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
        $data = $_SESSION['admin_data'] ?? [];
    }
    $res = storage_save($data);
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['admin_data'] = $data;
    }
    // Invalidar el caché de request para que lecturas posteriores obtengan datos frescos
    content_cache_clear();
    return $res;
}

/**
 * Asegura que $_SESSION['admin_data'] esté cargado para los formularios del panel de admin.
 */
function content_ensure_session_loaded(bool $force_reload = false): void {
    if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
        @session_start();
    }
    if ($force_reload || !isset($_SESSION['admin_data']) || empty($_SESSION['admin_data'])) {
        $_SESSION['admin_data'] = content_storage_load();
    }
}

function content_get(string $section, string $field, string $default = ''): string {
    $data = content_all_data();
    $value = $data[$section][$field] ?? $default;
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/**
 * Direccion (href o src) lista para imprimir dentro de un atributo.
 *
 * Usar SIEMPRE esto en lugar de content_raw() dentro de href="" o src="":
 * content_raw() devuelve el valor tal cual, asi que un texto con comillas
 * escrito desde el panel podia cerrar el atributo e inyectar codigo.
 *
 * Si el campo esta vacio devuelve $default, para que un enlace sin rellenar
 * no quede apuntando a la nada.
 */
function content_url(string $section, string $field, string $default = '#'): string {
    $data = content_all_data();
    $value = trim((string)($data[$section][$field] ?? ''));
    if ($value === '') {
        $value = $default;
    }
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function content_raw(string $section, string $field, $default = '') {
    $data = content_all_data();
    $val = $data[$section][$field] ?? null;
    if ($val === null) {
        return $default;
    }
    if (is_array($default) && !empty($default) && (!is_array($val) || empty($val))) {
        return $default;
    }
    return $val;
}

/**
 * Confirma que una ruta de imagen (relativa, tipo 'img/foo.png') tenga
 * de verdad un archivo detrás antes de imprimirla en un <img src="">.
 *
 * Sin esto, si alguien borra el archivo directo desde la carpeta (no desde
 * el panel), la ruta guardada sigue "pareciendo" válida y el navegador
 * termina mostrando el ícono de imagen rota en medio de un carrusel o
 * grid — un hueco feo que nadie pidió. Se usa para filtrar esos items
 * antes de pintarlos, en vez de imprimir lo que sea que haya en el campo.
 */
function content_image_exists(?string $path): bool {
    $path = trim((string)$path);
    if ($path === '') return false;
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return true;
    return file_exists(dirname(__DIR__) . '/' . ltrim($path, '/'));
}

/**
 * Arma el texto que gira alrededor de los botones circulares de video.
 *
 * El administrador escribe UNA FRASE POR LÍNEA, sin ningún símbolo raro:
 *
 *     ¿Cómo inscribirse?
 *     Haz clic aquí
 *
 * y esta función las une con " • " y agrega el separador final, para que al
 * dar la vuelta al círculo la última frase no se pegue con la primera.
 *
 * También acepta el formato antiguo (todo en un renglón con los • escritos a
 * mano), así que el contenido ya guardado sigue viéndose igual.
 */
function content_circular(string $section, string $field, string $default = ''): string {
    $raw = (string)content_raw($section, $field, $default);

    // Separa por saltos de línea (formato nuevo) y también por • (formato viejo),
    // así de paso se normaliza el espaciado alrededor de cada punto.
    $partes = preg_split('/[\r\n]+|\s*•\s*/u', $raw);
    if (!is_array($partes)) {
        $partes = [$raw]; // por si el texto trae caracteres inválidos
    }

    $frases = [];
    foreach ($partes as $p) {
        $p = trim($p);
        if ($p !== '') $frases[] = $p;
    }

    if (empty($frases)) return '';

    return htmlspecialchars(implode(' • ', $frases) . ' • ', ENT_QUOTES, 'UTF-8');
}

/**
 * Convierte una URL normal de YouTube a formato "embed", para poder
 * reproducirla dentro del modal del sitio.
 *
 * Acepta las dos formas que un administrador pega normalmente:
 *   https://youtu.be/XXXXXXXXXXX
 *   https://www.youtube.com/watch?v=XXXXXXXXXXX
 *
 * Si no reconoce el formato devuelve la URL tal cual, para no romper nada.
 */
function youtube_embed_url(string $url): string {
    $url = trim($url);
    if ($url === '') return '';

    $patron = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i';
    if (preg_match($patron, $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1';
    }

    return $url;
}

/**
 * Formatea un título permitiendo usar asteriscos *texto* 
 * para pintarlo con la clase text-orange.
 */
function content_title(string $section, string $field, string $default = ''): string {
    $raw = content_raw($section, $field, $default);
    // Escapar todo por seguridad
    $safe = htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
    // Reemplazar *texto* por <span class="text-orange">texto</span>
    $html = preg_replace('/\*(.*?)\*/', '<span class="text-orange">$1</span>', $safe);
    return $html;
}

/**
 * Verifica si la sección completa debe mostrarse en la Landing Page.
 */
/**
 * Verifica si la sección completa debe mostrarse en la Landing Page.
 */
function is_visible(string $section): bool {
    $data = content_all_data();
    $val = $data[$section]['_visible'] ?? '1';
    return $val === '1' || $val === 1 || $val === true;
}

/**
 * Obtiene los items de una colección (como Equipo o Testimonios) directamente de la BD/Storage.
 */
function collection_items(string $collection_name): array {
    $data = content_all_data();
    
    if (isset($data[$collection_name]['items']) && is_array($data[$collection_name]['items'])) {
        return $data[$collection_name]['items'];
    }

    // Sin datos guardados, la coleccion esta vacia y punto. Antes se devolvian
    // cuatro autoridades escritas aqui, asi que el administrador las borraba
    // desde el panel y reaparecian al recargar, sin forma de evitarlo.
    return [];
}
