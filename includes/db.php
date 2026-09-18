<?php
// includes/db.php
// Capa de conexión a Base de Datos MySQL mediante PDO (GUIA-PANEL-ADMIN Sección 5 y 6)
// Implementa PDO con sentencias preparadas obligatorias y manejo seguro de errores.
// Auto-crea la base de datos y tabla site_content si no existen.

class Database {
    private static ?PDO $instance = null;
    private static bool $setup_done = false;

    /**
     * Retorna la instancia singleton de conexión PDO.
     * Si la BD o tabla no existen, las crea automáticamente.
     */
    public static function getConnection(): ?PDO {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $configFile = dirname(__DIR__) . '/admin/config.php';
        $config = file_exists($configFile) ? (require $configFile) : [];

        $host = getenv('DB_HOST') ?: ($config['db_host'] ?? '127.0.0.1');
        $port = (int)(getenv('DB_PORT') ?: ($config['db_port'] ?? 3306));
        $name = getenv('DB_NAME') ?: ($config['db_name'] ?? '');
        $user = getenv('DB_USER') ?: ($config['db_user'] ?? '');
        $pass = getenv('DB_PASS') ?: ($config['db_pass'] ?? '');

        if (empty($name) || empty($user)) {
            return null;
        }

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false // Sentencias preparadas nativas del motor
        ];

        try {
            // 1. Intentar conexión directa a la BD
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
            self::$instance = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Si el error es "Unknown database", intentar crearla
            if (strpos($e->getMessage(), 'Unknown database') !== false ||
                $e->getCode() == 1049) {
                try {
                    // 2. Conectar sin dbname para crear la BD
                    $dsn_no_db = "mysql:host={$host};port={$port};charset=utf8mb4";
                    $pdo_root = new PDO($dsn_no_db, $user, $pass, $options);

                    // Crear la base de datos con charset seguro
                    $safe_name = preg_replace('/[^a-zA-Z0-9_]/', '', $name);
                    $pdo_root->exec(
                        "CREATE DATABASE IF NOT EXISTS `{$safe_name}` 
                         CHARACTER SET utf8mb4 
                         COLLATE utf8mb4_unicode_ci"
                    );
                    $pdo_root = null; // Cerrar conexión sin BD

                    // 3. Reconectar a la BD recién creada
                    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
                    self::$instance = new PDO($dsn, $user, $pass, $options);

                    error_log("[ITB] Base de datos '{$name}' creada automáticamente.");
                } catch (PDOException $e2) {
                    error_log('Database auto-create error: ' . $e2->getMessage());
                    return null;
                }
            } else {
                // Otro error de conexión (MySQL apagado, credenciales incorrectas, etc.)
                error_log('Database connection error: ' . $e->getMessage());
                return null;
            }
        }

        // 4. Auto-crear tabla site_content si no existe (solo una vez por request)
        if (self::$instance && !self::$setup_done) {
            self::autoSetupTables();
            self::$setup_done = true;
        }

        return self::$instance;
    }

    /**
     * Crea las tablas necesarias si no existen:
     * - site_content: textos, imágenes, colecciones y páginas dinámicas
     * - form_registros: solicitudes recibidas del formulario de admisión
     * - auth_rate_limits: control de fuerza bruta para el login
     */
    private static function autoSetupTables(): void {
        if (!self::$instance) return;

        try {
            // 1. Tabla de contenido del sitio
            self::$instance->exec("
                CREATE TABLE IF NOT EXISTS `site_content` (
                    `section`     VARCHAR(100)  NOT NULL,
                    `field_key`   VARCHAR(100)  NOT NULL,
                    `field_value` LONGTEXT      NULL,
                    `updated_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`section`, `field_key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // 2. Tabla de solicitudes de admisión
            self::$instance->exec("
                CREATE TABLE IF NOT EXISTS `form_registros` (
                    `id`           INT AUTO_INCREMENT NOT NULL,
                    `fecha`        DATETIME      NOT NULL,
                    `nombre`       VARCHAR(50)   NOT NULL,
                    `apellido`     VARCHAR(50)   NOT NULL,
                    `email`        VARCHAR(120)  NOT NULL,
                    `telefono`     VARCHAR(20)   NOT NULL,
                    `cedula`       VARCHAR(20)   NOT NULL,
                    `nacionalidad` VARCHAR(20)   NOT NULL,
                    `bachiller`    VARCHAR(10)   NOT NULL,
                    `carrera`      VARCHAR(150)  NOT NULL,
                    `modalidad`    VARCHAR(80)   NOT NULL,
                    `mensaje`      TEXT          NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // 3. Tabla de rate limiting para login administrativo
            self::$instance->exec("
                CREATE TABLE IF NOT EXISTS `auth_rate_limits` (
                    `ip`           VARCHAR(45)   NOT NULL,
                    `attempts`     INT           NOT NULL DEFAULT 0,
                    `last_attempt` BIGINT        NOT NULL DEFAULT 0,
                    `updated_at`   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`ip`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // Migración inicial de registros.json a form_registros si la tabla está vacía
            $regJson = dirname(__DIR__) . '/data/registros.json';
            if (file_exists($regJson)) {
                $count = (int)self::$instance->query("SELECT COUNT(*) FROM form_registros")->fetchColumn();
                if ($count === 0) {
                    $jsonRaw = @file_get_contents($regJson);
                    $records = json_decode($jsonRaw ?: '[]', true);
                    if (is_array($records) && !empty($records)) {
                        $stmt = self::$instance->prepare("
                            INSERT INTO form_registros (id, fecha, nombre, apellido, email, telefono, cedula, nacionalidad, bachiller, carrera, modalidad, mensaje)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE fecha = VALUES(fecha)
                        ");
                        foreach ($records as $r) {
                            $stmt->execute([
                                $r['id'] ?? null,
                                $r['fecha'] ?? date('Y-m-d H:i:s'),
                                $r['nombre'] ?? '',
                                $r['apellido'] ?? '',
                                $r['email'] ?? '',
                                $r['telefono'] ?? '',
                                $r['cedula'] ?? '',
                                $r['nacionalidad'] ?? 'ecuatoriano',
                                $r['bachiller'] ?? 'si',
                                $r['carrera'] ?? '',
                                $r['modalidad'] ?? '',
                                $r['mensaje'] ?? ''
                            ]);
                        }
                    }
                }
            }
        } catch (PDOException $e) {
            error_log('Database auto-setup error: ' . $e->getMessage());
        }
    }

    /**
     * Helper para ejecutar consultas preparadas de forma segura y consistente.
     */
    public static function query(string $sql, array $params = []): ?PDOStatement {
        $pdo = self::getConnection();
        if (!$pdo) {
            return null;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}

/**
 * Función helper global para obtener la conexión PDO.
 */
function db(): ?PDO {
    return Database::getConnection();
}

