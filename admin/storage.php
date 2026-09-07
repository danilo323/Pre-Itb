<?php
// admin/storage.php
// Servicio de almacenamiento y persistencia en MySQL para textos e imágenes del ITB

require_once dirname(__DIR__) . '/includes/db.php';

/**
 * Referencia única a la caché en memoria de site_content.
 *
 * @return array
 */
function &storage_get_cache(bool $forceReload = false): array {
    static $cache = null;

    if ($cache === null || $forceReload) {
        $cache = [];
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
                $cache[$s][$k] = (string)$v;
            }
        } catch (Exception $e) {
            error_log("Error cargando site_content: " . $e->getMessage());
        }
    }

    return $cache;
}

/**
 * Fuerza la recarga de la caché desde la base de datos MySQL.
 */
function storage_clear_cache(): void {
    storage_get_cache(true);
}

/**
 * Carga todo el contenido dinámico del sitio desde MySQL.
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
 * Guarda o actualiza un campo en la base de datos MySQL (atómico con ON DUPLICATE KEY UPDATE).
 *
 * @param string $section
 * @param string $field
 * @param string|null $value
 * @return bool
 */
function storage_set(string $section, string $field, ?string $value): bool {
    try {
        $pdo = db_connect();
        $sql = "INSERT INTO `site_content` (`section`, `field_key`, `field_value`)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE `field_value` = VALUES(`field_value`), `updated_at` = CURRENT_TIMESTAMP";
        $stmt = $pdo->prepare($sql);
        $valStr = ($value !== null) ? (string)$value : null;
        $stmt->execute([$section, $field, $valStr]);

        // Actualizar la caché en memoria inmediatamente
        $cache = &storage_get_cache();
        if (!isset($cache[$section])) {
            $cache[$section] = [];
        }
        $cache[$section][$field] = (string)$valStr;

        return true;
    } catch (Exception $e) {
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
