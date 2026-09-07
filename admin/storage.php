<?php
// admin/storage.php
// Motor de almacenamiento JSON y sincronización con MySQL (GUIA-PANEL-ADMIN Sección 5)

require_once dirname(__DIR__) . '/includes/db.php';

/**
 * Retorna la ruta al archivo principal de almacenamiento JSON.
 */
function storage_file(): string {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir . '/content.json';
}

/**
 * Retorna la ruta a la semilla base.
 */
function storage_seed_file(): string {
    return dirname(__DIR__) . '/data/seed/content.json';
}

/**
 * Carga los datos almacenados.
 * Prioridad:
 * 1. Base de datos MySQL (tabla site_content) si está conectada y tiene datos.
 * 2. Archivo local data/content.json.
 * 3. Semilla data/seed/content.json.
 */
function storage_load(): array {
    // 1. Intentar cargar desde MySQL (si está disponible)
    $pdo = db();
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT section, field_key, field_value FROM site_content");
            $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
            if (!empty($rows)) {
                $dbData = [];
                foreach ($rows as $r) {
                    $sec = $r['section'];
                    $fk  = $r['field_key'];
                    $val = $r['field_value'];

                    if (is_string($val) && $val !== '' && ($val[0] === '[' || $val[0] === '{')) {
                        $decoded = json_decode($val, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $val = $decoded;
                        }
                    }
                    $dbData[$sec][$fk] = $val;
                }
                if (!empty($dbData)) {
                    return $dbData;
                }
            }
        } catch (Exception $e) {
            error_log('Error loading from MySQL site_content: ' . $e->getMessage());
        }
    }

    // 2. Fallback a archivo JSON en disco
    $file = storage_file();

    if (!file_exists($file)) {
        $seed = storage_seed_file();
        if (file_exists($seed)) {
            $rawSeed = @file_get_contents($seed);
            if ($rawSeed !== false && $rawSeed !== '') {
                $decodedSeed = json_decode($rawSeed, true);
                if (is_array($decodedSeed)) {
                    storage_save($decodedSeed);
                    return $decodedSeed;
                }
            }
        }
        return [];
    }

    $raw = @file_get_contents($file);
    if ($raw !== false && $raw !== '') {
        $data = json_decode($raw, true);
        if (is_array($data)) {
            return $data;
        }
    }

    return [];
}

/**
 * Genera un backup rotativo (últimas $maxBackups copias) antes de sobrescribir.
 */
function storage_rotate_backups(string $file, int $maxBackups = 5): void {
    if (!file_exists($file)) {
        return;
    }

    for ($i = $maxBackups - 1; $i >= 1; $i--) {
        $curr = "{$file}.bak_{$i}";
        $next = "{$file}.bak_" . ($i + 1);
        if (file_exists($curr)) {
            @rename($curr, $next);
        }
    }

    @copy($file, "{$file}.bak_1");
}

/**
 * Guarda los datos simultáneamente en disco (con atomicidad y backups)
 * y en la Base de Datos MySQL (tabla site_content).
 */
function storage_save(array $data): bool {
    $file = storage_file();
    $dir  = dirname($file);

    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    // 1. Generar backup rotativo del archivo actual
    storage_rotate_backups($file, 5);

    // 2. Serialización legible y limpia
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    // 3. Escritura atómica en archivo temporal
    $tmpFile = $dir . '/content.json.tmp_' . bin2hex(random_bytes(8));
    $written = @file_put_contents($tmpFile, $json, LOCK_EX);

    if ($written === false) {
        if (file_exists($tmpFile)) @unlink($tmpFile);
        return false;
    }

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    $renamed = @rename($tmpFile, $file);
    if (!$renamed) {
        @file_put_contents($file, $json, LOCK_EX);
        if (file_exists($tmpFile)) @unlink($tmpFile);
    }

    // 4. Guardar en Base de Datos MySQL (tabla site_content) mediante PDO preparado
    $pdo = db();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO site_content (section, field_key, field_value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE field_value = VALUES(field_value)");
            foreach ($data as $secKey => $secFields) {
                if ($secKey === '_meta' || !is_array($secFields)) continue;
                foreach ($secFields as $fKey => $fVal) {
                    $valStr = is_array($fVal) ? json_encode($fVal, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string)$fVal;
                    $stmt->execute([$secKey, $fKey, $valStr]);
                }
            }
        } catch (Exception $e) {
            error_log('Error saving to MySQL site_content: ' . $e->getMessage());
        }
    }

    return true;
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

    if ($lastId === 0 && !empty($data[$collection]['items'])) {
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
