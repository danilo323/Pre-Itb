<?php
// admin/storage.php
// Motor de persistencia 100% MySQL (tabla site_content)
// con sincronización automática a database/init.sql para Git y Docker.

require_once dirname(__DIR__) . '/includes/db.php';

/**
 * Carga todo el contenido directamente desde la Base de Datos MySQL (tabla site_content).
 */
function storage_load(): array {
    $pdo = db();
    if (!$pdo) {
        error_log('[ITB-STORAGE] No hay conexión con la base de datos MySQL.');
        return [];
    }

    try {
        $stmt = $pdo->query("SELECT section, field_key, field_value FROM site_content");
        $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        $dbData = [];

        foreach ($rows as $r) {
            $sec = $r['section'];
            $fk  = $r['field_key'];
            $val = $r['field_value'];

            // Deserializar arrays o estructuras JSON guardadas en el campo
            if (is_string($val) && $val !== '' && ($val[0] === '[' || $val[0] === '{')) {
                $decoded = json_decode($val, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $val = $decoded;
                }
            }
            $dbData[$sec][$fk] = $val;
        }

        return $dbData;
    } catch (Exception $e) {
        error_log('[ITB-STORAGE] Error cargando desde MySQL site_content: ' . $e->getMessage());
        return [];
    }
}

/**
 * Guarda los datos directamente en la Base de Datos MySQL (tabla site_content)
 * utilizando transacciones PDO y sentencias preparadas (UPSERT).
 * Además, sincroniza automáticamente database/init.sql para que viaje a Git/Docker.
 */
function storage_save(array $data): bool {
    $pdo = db();
    if (!$pdo) {
        error_log('[ITB-STORAGE] Imposible guardar: no hay conexión con MySQL.');
        return false;
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO site_content (section, field_key, field_value) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE field_value = VALUES(field_value)
        ");

        foreach ($data as $secKey => $secFields) {
            if (!is_array($secFields)) continue;
            foreach ($secFields as $fKey => $fVal) {
                $valStr = is_array($fVal) 
                    ? json_encode($fVal, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) 
                    : (string)$fVal;
                $stmt->execute([$secKey, $fKey, $valStr]);
            }
        }

        $pdo->commit();

        // Actualizar automáticamente database/init.sql
        storage_dump_sql();

        return true;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('[ITB-STORAGE] Error guardando en MySQL site_content: ' . $e->getMessage());
        return false;
    }
}

/**
 * Borra un campo concreto de la persistencia MySQL (tabla site_content)
 * y actualiza el volcado database/init.sql.
 */
function storage_delete_field(string $section, string $fieldKey): bool {
    $pdo = db();
    if (!$pdo) {
        return false;
    }

    try {
        $stmt = $pdo->prepare('DELETE FROM site_content WHERE section = ? AND field_key = ?');
        $res = $stmt->execute([$section, $fieldKey]);
        if ($res) {
            storage_dump_sql();
        }
        return $res;
    } catch (Exception $e) {
        error_log('[ITB-STORAGE] Error eliminando de MySQL site_content: ' . $e->getMessage());
        return false;
    }
}

/**
 * Exporta el estado actual de la base de datos a database/init.sql
 * para que siempre viaje la última información actualizada a Git y Docker.
 */
function storage_dump_sql(): bool {
    $pdo = db();
    if (!$pdo) {
        return false;
    }

    $outputDir = dirname(__DIR__) . '/database';
    if (!is_dir($outputDir)) {
        @mkdir($outputDir, 0755, true);
    }
    $sqlFile = $outputDir . '/init.sql';

    try {
        $out = "-- Base de Datos ITB (MySQL)\n";
        $out .= "-- Generado automáticamente al guardar en el panel de administración\n";
        $out .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
        $out .= "SET NAMES utf8mb4;\n";
        $out .= "SET CHARACTER SET utf8mb4;\n\n";
        $out .= "CREATE DATABASE IF NOT EXISTS `itb_admin` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
        $out .= "USE `itb_admin`;\n\n";

        $tables = ['admin_users', 'site_content', 'form_registros', 'auth_rate_limits'];

        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if (!$stmt || !$stmt->fetch()) {
                continue;
            }

            $out .= "-- --------------------------------------------------------\n";
            $out .= "-- Estructura de tabla para `$table`\n";
            $out .= "-- --------------------------------------------------------\n\n";

            $createStmt = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
            $createSql = $createStmt['Create Table'] ?? '';
            $out .= "DROP TABLE IF EXISTS `$table`;\n";
            $out .= $createSql . ";\n\n";

            $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $out .= "-- Volcado de datos para `$table` (" . count($rows) . " registros)\n";
                $chunks = array_chunk($rows, 50);
                foreach ($chunks as $chunk) {
                    $cols = array_keys($chunk[0]);
                    $colList = implode('`, `', $cols);
                    $out .= "INSERT INTO `$table` (`$colList`) VALUES\n";
                    $valuesArr = [];
                    foreach ($chunk as $row) {
                        $valList = [];
                        foreach ($cols as $col) {
                            $val = $row[$col];
                            if ($val === null) {
                                $valList[] = 'NULL';
                            } else {
                                $valList[] = $pdo->quote($val);
                            }
                        }
                        $valuesArr[] = '(' . implode(', ', $valList) . ')';
                    }
                    $out .= implode(",\n", $valuesArr) . ";\n";
                }
                $out .= "\n";
            }
        }

        // Escritura atómica para no dejar archivo a medias
        $tmp = $sqlFile . '.tmp_' . bin2hex(random_bytes(4));
        if (@file_put_contents($tmp, $out, LOCK_EX) !== false) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && file_exists($sqlFile)) {
                @unlink($sqlFile);
            }
            if (!@rename($tmp, $sqlFile)) {
                @file_put_contents($sqlFile, $out, LOCK_EX);
                if (file_exists($tmp)) @unlink($tmp);
            }
        }
        return true;
    } catch (Exception $e) {
        error_log('[ITB-STORAGE] Error generando dump automático init.sql: ' . $e->getMessage());
        return false;
    }
}

/**
 * Calcula un ID autoincremental para una colección que NUNCA reutiliza IDs borrados.
 */
function storage_next_id(string $collection, array &$data): int {
    if (!isset($data['_meta'])) {
        $data['_meta'] = [];
    }
    if (!isset($data['_meta'][$collection])) {
        $data['_meta'][$collection] = [];
    }

    $lastId = (int)($data['_meta'][$collection]['last_id'] ?? 0);

    if (!empty($data[$collection]['items'])) {
        foreach ($data[$collection]['items'] as $item) {
            $id = (int)($item['id'] ?? 0);
            if ($id > $lastId) {
                $lastId = $id;
            }
        }
    }

    $nextId = $lastId + 1;
    $data['_meta'][$collection]['last_id'] = $nextId;

    return $nextId;
}

/**
 * Funciones heredadas de compatibilidad (no realizan operaciones sobre archivos).
 */
function storage_file(): string {
    return dirname(__DIR__) . '/data/content.json';
}

function storage_seed_file(): string {
    return dirname(__DIR__) . '/data/seed/content.json';
}

function storage_rotate_backups(string $file, int $maxBackups = 5): void {
    // No-op: el sitio ahora funciona 100% con MySQL
}
