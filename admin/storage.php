<?php
// admin/storage.php
// Servicio de almacenamiento y persistencia para textos e imágenes del ITB (MySQL + Respaldo JSON en disco)

require_once dirname(__DIR__) . '/includes/db.php';

/**
 * Ruta del archivo de persistencia JSON de respaldo.
 */
function storage_get_json_file(): string {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir . '/site_content.json';
}

/**
 * Guarda una copia de respaldo en el archivo JSON.
 */
function storage_sync_json(array $data): void {
    try {
        $file = storage_get_json_file();
        @file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    } catch (Throwable $e) {
        error_log("Error sincronizando archivo JSON de respaldo: " . $e->getMessage());
    }
}

/**
 * Referencia única a la caché en memoria de site_content.
 *
 * @param bool $forceReload
 * @return array
 */
function &storage_get_cache(bool $forceReload = false): array {
    static $cache = null;

    if ($cache === null || $forceReload) {
        $cache = [];
        $loadedFromDb = false;
        try {
            $pdo = db_connect();
            $stmt = $pdo->query("SELECT `section`, `field_key`, `field_value` FROM `site_content`");
            while ($row = $stmt->fetch()) {
                $s = $row['section'];
                $k = $row['field_key'];
                $v = $row['field_value'];
                if (!isset($cache[$s])) {
                    $cache[$s] = [];
                }
                $cache[$s][$k] = ($v !== null) ? (string)$v : '';
            }
            $loadedFromDb = true;
            // Sincronizar el JSON de respaldo con lo obtenido de BD
            storage_sync_json($cache);
        } catch (Throwable $e) {
            error_log("Error cargando site_content desde MySQL: " . $e->getMessage());
        }

        // Si la base de datos no cargó datos o falló la conexión, leer del archivo JSON
        if (!$loadedFromDb || empty($cache)) {
            $jsonFile = storage_get_json_file();
            if (file_exists($jsonFile)) {
                $jsonContent = @file_get_contents($jsonFile);
                if ($jsonContent) {
                    $decoded = json_decode($jsonContent, true);
                    if (is_array($decoded)) {
                        $cache = $decoded;
                    }
                }
            }
        }
    }

    return $cache;
}

/**
 * Fuerza la recarga de la caché desde la base de datos MySQL o archivo.
 */
function storage_clear_cache(): void {
    storage_get_cache(true);
}

/**
 * Carga todo el contenido dinámico del sitio.
 *
 * @return array
 */
function storage_load_all(): array {
    return storage_get_cache();
}

/**
 * Obtiene el valor de un campo específico para una sección.
 *
 * @param string $section
 * @param string $field
 * @param string $default
 * @return string
 */
function storage_get(string $section, string $field, string $default = ''): string {
    $cache = &storage_get_cache();
    if (isset($cache[$section][$field]) && $cache[$section][$field] !== '') {
        return (string)$cache[$section][$field];
    }
    return $default;
}

/**
 * Verifica si un campo existe en el almacenamiento.
 *
 * @param string $section
 * @param string $field
 * @return bool
 */
function storage_has(string $section, string $field): bool {
    $cache = &storage_get_cache();
    return isset($cache[$section][$field]);
}

/**
 * Obtiene el valor original o decodificado (array para repeaters/colecciones o string).
 *
 * @param string $section
 * @param string $field
 * @param mixed $default
 * @return mixed
 */
function storage_get_raw(string $section, string $field, $default = null) {
    $cache = &storage_get_cache();
    if (isset($cache[$section][$field])) {
        $val = $cache[$section][$field];
        if (is_array($val)) {
            return $val;
        }
        $valStr = (string)$val;
        if (is_array($default) || str_starts_with(trim($valStr), '[') || str_starts_with(trim($valStr), '{')) {
            $decoded = json_decode($valStr, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        return $valStr;
    }
    return $default;
}

/**
 * Obtiene todos los campos de una sección.
 *
 * @param string $section
 * @return array
 */
function storage_get_section(string $section): array {
    $cache = &storage_get_cache();
    return $cache[$section] ?? [];
}

/**
 * Obtiene todo el mapa de secciones y campos guardados en base de datos.
 *
 * @return array
 */
function storage_get_all(): array {
    return storage_get_cache();
}

/**
 * Guarda o actualiza un campo en la base de datos MySQL y en el archivo de respaldo.
 *
 * @param string $section
 * @param string $field
 * @param string|null $value
 * @return bool
 */
function storage_set(string $section, string $field, ?string $value): bool {
    $cache = &storage_get_cache();
    if (!isset($cache[$section])) {
        $cache[$section] = [];
    }
    $valStr = ($value !== null) ? (string)$value : '';
    $cache[$section][$field] = $valStr;

    // 1. Guardar de inmediato en el archivo JSON (garantiza persistencia local aun si MySQL no estuviera disponible)
    storage_sync_json($cache);

    // 2. Guardar en MySQL
    try {
        $pdo = db_connect();
        $sql = "INSERT INTO `site_content` (`section`, `field_key`, `field_value`)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE `field_value` = VALUES(`field_value`), `updated_at` = CURRENT_TIMESTAMP";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$section, $field, $valStr]);
        return true;
    } catch (Throwable $e) {
        error_log("Error guardando site_content ({$section}.{$field}): " . $e->getMessage());
        return false;
    }
}

/**
 * Elimina físicamente del servidor una imagen reemplazada, siempre que haya sido subida por el panel.
 *
 * @param string|null $oldPath Ruta relativa guardada previamente (ej: img/1725489000_foto.jpg)
 * @return bool True si el archivo fue eliminado, false si no existía o no correspondía eliminarlo
 */
function storage_delete_old_file(?string $oldPath): bool {
    if (empty($oldPath) || !is_string($oldPath)) {
        return false;
    }

    $projectRoot = dirname(__DIR__);
    $cleanRel = ltrim(str_replace(['\\', '/'], '/', $oldPath), '/');
    $fullPath = $projectRoot . '/' . $cleanRel;

    if (!file_exists($fullPath) || !is_file($fullPath)) {
        return false;
    }

    $realProject = realpath($projectRoot);
    $realFile    = realpath($fullPath);

    if ($realFile === false || $realProject === false) {
        return false;
    }

    // Comprobación de seguridad insensible a mayúsculas para Windows
    $lowerProject = strtolower($realProject);
    $lowerFile    = strtolower($realFile);

    if (!str_starts_with($lowerFile, $lowerProject)) {
        return false;
    }

    $filename = basename($realFile);

    // Comprobar si es un archivo subido por el panel:
    // 1. Prefijo de timestamp (ej: 1725489000_nombre.ext o 9999999901_nombre.ext)
    // 2. O ubicado dentro de uploads/
    $isUploadedFile = preg_match('/^\d{9,12}_/', $filename) ||
                      str_contains(strtolower($cleanRel), 'uploads/');

    if ($isUploadedFile) {
        return @unlink($realFile);
    }

    // No se eliminan archivos base del tema original (ej: img/logo.png)
    return false;
}
