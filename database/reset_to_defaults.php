<?php
// database/reset_to_defaults.php
// Resetea todos los textos, imágenes y configuraciones a los valores originales de fábrica.

require_once dirname(__DIR__) . '/includes/db.php';

echo "== Iniciando reseteo a contenido original de fábrica ==\n";

try {
    $pdo = db_connect();

    // 1. Limpiar tabla site_content
    $pdo->exec("TRUNCATE TABLE `site_content`");
    echo "[OK] Tabla site_content vaciada.\n";

    // 2. Cargar schema original
    $schema = require dirname(__DIR__) . '/admin/schema_mock.php';

    $insertStmt = $pdo->prepare("INSERT INTO `site_content` (`section`, `field_key`, `field_value`) 
        VALUES (:section, :field_key, :field_value)
        ON DUPLICATE KEY UPDATE `field_value` = VALUES(`field_value`)");

    $saveField = function(string $sec, string $key, $val) use ($insertStmt) {
        if ($val === null) return;
        if (is_array($val)) {
            $val = json_encode($val, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } elseif (is_bool($val)) {
            $val = $val ? '1' : '0';
        } else {
            $val = (string)$val;
        }
        $insertStmt->execute([
            ':section'     => $sec,
            ':field_key'   => $key,
            ':field_value' => $val,
        ]);
    };

    // 3. Procesar Ajustes, Menú, Footer (singletons globales)
    foreach (['ajustes', 'menu', 'footer'] as $globalSec) {
        if (isset($schema['items'][$globalSec]['fields'])) {
            foreach ($schema['items'][$globalSec]['fields'] as $fKey => $fConfig) {
                if (isset($fConfig['default'])) {
                    $saveField($globalSec, $fKey, $fConfig['default']);
                }
            }
        }
        echo "[OK] Sección global '{$globalSec}' reseteada.\n";
    }

    // 4. Procesar Secciones de la página de Inicio
    if (isset($schema['items']['inicio']['sections'])) {
        foreach ($schema['items']['inicio']['sections'] as $secKey => $secConfig) {
            // Guardar visibilidad por defecto (1)
            $saveField($secKey, '_visible', '1');

            if (!empty($secConfig['fields'])) {
                foreach ($secConfig['fields'] as $fKey => $fConfig) {
                    if (isset($fConfig['default'])) {
                        $saveField($secKey, $fKey, $fConfig['default']);
                    }
                }
            }
            echo "[OK] Sección '{$secKey}' de Inicio reseteada.\n";
        }
    }

    // 5. Resetear Colección Equipo con los 4 miembros originales
    $equipoOriginal = [
        [
            'id' => 1,
            'orden' => '1',
            'nombre' => 'Roberto Tolozano Benites',
            'nombre_completo' => 'PhD. Roberto Tolozano Benites',
            'cargo' => 'Canciller',
            'linkedin' => '#',
            'email' => 'cancilleria@itb.edu.ec',
            'foto' => 'img/autoridad_1.png',
            'mostrar_en_home' => true,
            'publicado' => true
        ],
        [
            'id' => 2,
            'orden' => '2',
            'nombre' => 'Elena Tolozano Benites',
            'nombre_completo' => 'PhD. Elena Tolozano Benites',
            'cargo' => 'Rectora',
            'linkedin' => '#',
            'email' => 'rectorado@itb.edu.ec',
            'foto' => 'img/autoridad_2.png',
            'mostrar_en_home' => true,
            'publicado' => true
        ],
        [
            'id' => 3,
            'orden' => '3',
            'nombre' => 'Luis Alzate Peralta',
            'nombre_completo' => 'PhD. Luis Alzate Peralta',
            'cargo' => 'Vicerrector Académico y de Investigación',
            'linkedin' => '#',
            'email' => 'vicerrectorado@itb.edu.ec',
            'foto' => 'img/autoridad_3.png',
            'mostrar_en_home' => true,
            'publicado' => true
        ],
        [
            'id' => 4,
            'orden' => '4',
            'nombre' => 'Michelle Tolozano Lapierre',
            'nombre_completo' => 'PhD. Michelle Tolozano Lapierre',
            'cargo' => 'Vicerrectora de Extensión y Gestión Administrativa',
            'linkedin' => '#',
            'email' => 'extension@itb.edu.ec',
            'foto' => 'img/autoridad_4.png',
            'mostrar_en_home' => true,
            'publicado' => true
        ]
    ];
    $saveField('equipo', 'items', $equipoOriginal);
    echo "[OK] Colección 'equipo' reseteada a autoridades originales.\n";

    // 6. Eliminar imágenes de prueba generadas recientemente en img/ (img/1788*)
    $imgDir = dirname(__DIR__) . '/img';
    $deletedImages = 0;
    if (is_dir($imgDir)) {
        $files = glob($imgDir . '/1788*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
                $deletedImages++;
            }
        }
    }
    echo "[OK] Se eliminaron {$deletedImages} imágenes de prueba subidas.\n";

    // 7. Limpiar sesiones PHP activas para evitar datos en memoria obsoletos
    if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
        @session_start();
    }
    unset($_SESSION['admin_data']);

    // 8. Limpiar caché de storage
    if (file_exists(dirname(__DIR__) . '/admin/storage.php')) {
        require_once dirname(__DIR__) . '/admin/storage.php';
        if (function_exists('storage_clear_cache')) {
            storage_clear_cache();
            echo "[OK] Caché de almacenamiento limpiada.\n";
        }
    }

    echo "== Reseteo completado con éxito. Todos los textos e imágenes originales están activos. ==\n";

} catch (Exception $e) {
    echo "[ERROR] Falló el reseteo: " . $e->getMessage() . "\n";
    exit(1);
}
