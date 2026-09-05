<?php
// database/setup_db.php
// Script de inicialización de Base de Datos para ITB Admin

echo "== Iniciando configuración de base de datos ITB Admin ==\n";

$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = '123456789';
$dbname = 'itb_admin';

try {
    // 1. Conectar a MySQL sin seleccionar BD
    $pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "[OK] Conexión establecida con el servidor MySQL.\n";

    // 2. Crear base de datos si no existe
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "[OK] Base de datos '{$dbname}' asegurada.\n";

    // 3. Conectar a la base de datos itb_admin
    $pdo->exec("USE `{$dbname}`;");

    // 4. Crear tabla admin_users
    $sqlTable = "CREATE TABLE IF NOT EXISTS `admin_users` (
        `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `email`         VARCHAR(255) NOT NULL UNIQUE,
        `password_hash` VARCHAR(255) NOT NULL,
        `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sqlTable);
    echo "[OK] Tabla 'admin_users' asegurada.\n";

    // 4b. Crear tabla site_content para persistencia de textos e imágenes
    $sqlContent = "CREATE TABLE IF NOT EXISTS `site_content` (
        `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `section`     VARCHAR(64) NOT NULL,
        `field_key`   VARCHAR(64) NOT NULL,
        `field_value` LONGTEXT NULL,
        `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `uk_section_field` (`section`, `field_key`),
        INDEX `idx_section` (`section`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sqlContent);
    echo "[OK] Tabla 'site_content' asegurada.\n";

    // 5. Comprobar si el usuario inicial existe
    $adminEmail = 'admin@itb.edu.ec';
    $initialPassword = 'Admin123*';

    $stmt = $pdo->prepare("SELECT id, email FROM `admin_users` WHERE `email` = ?");
    $stmt->execute([$adminEmail]);
    $existing = $stmt->fetch();

    if (!$existing) {
        $hash = password_hash($initialPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $ins = $pdo->prepare("INSERT INTO `admin_users` (`email`, `password_hash`) VALUES (?, ?)");
        $ins->execute([$adminEmail, $hash]);
        echo "[OK] Usuario administrador inicial creado: {$adminEmail}\n";
        echo "     Contraseña inicial: {$initialPassword}\n";
    } else {
        echo "[INFO] El usuario '{$adminEmail}' ya existe en la base de datos.\n";
    }

    // -------------------------------------------------
    // Generar archivo de configuración (admin/config.php) si no existe
    // -------------------------------------------------
    $adminConfigPath = dirname(__DIR__) . '/admin/config.php';
    if (!file_exists($adminConfigPath)) {
        $configContent = "<?php\n// admin/config.php - Generado automáticamente\nreturn [\n"
            . "    'db_host'               => '{$host}',\n"
            . "    'db_port'               => {$port},\n"
            . "    'db_name'               => '{$dbname}',\n"
            . "    'db_user'               => '{$user}',\n"
            . "    'db_pass'               => '{$pass}',\n"
            . "    'session_name'          => 'itb_admin_sess',\n"
            . "    'session_timeout'       => 3600,\n"
            . "    'login_max_attempts'    => 5,\n"
            . "    'login_lockout_seconds' => 900,\n"
            . "    'data_path'             => __DIR__ . '/../data',\n"
            . "    'uploads_path'          => __DIR__ . '/../uploads',\n"
            . "    'max_upload_mb'         => 8,\n"
            . "];\n";
        if (file_put_contents($adminConfigPath, $configContent) !== false) {
            echo "[OK] Archivo de configuración creado: admin/config.php\n";
        }
    }

    echo "== Configuración completada con éxito ==\n";

} catch (PDOException $e) {
    echo "[ERROR] Falló la configuración de base de datos: " . $e->getMessage() . "\n";
    exit(1);
}
