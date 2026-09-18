<?php
// test/verify_security_standards.php
// Verificación integral de normas de ciberseguridad y base de datos (GUIA-PANEL-ADMIN)

require_once __DIR__ . '/../admin/auth.php';
require_once __DIR__ . '/../admin/storage.php';
require_once __DIR__ . '/../includes/db.php';

auth_session_start();

echo "=== INICIANDO AUDITORÍA DE SEGURIDAD Y BASE DE DATOS ===\n\n";
$passed = 0;
$failed = 0;

function assert_test($description, $condition) {
    global $passed, $failed;
    if ($condition) {
        echo " [PASS] {$description}\n";
        $passed++;
    } else {
        echo " [FAIL] {$description}\n";
        $failed++;
    }
}

// 1. Verificación de Autenticación Criptográfica Estricta
$correctPass = auth_verify_credentials('admin', '1234');
$wrongPass   = auth_verify_credentials('admin', 'admin'); // Antiguo bypass
$invalidUser = auth_verify_credentials('hacker', '1234');

assert_test("Credenciales 'admin / 1234' verifican con hash Bcrypt (password_verify)", $correctPass === true);
assert_test("Bypass antiguo de texto plano ('admin') es rechazado", $wrongPass === false);
assert_test("Usuario inválido es rechazado", $invalidUser === false);

// 2. Verificación de Rate Limiting
$testIp = '192.168.99.99';
auth_rate_limit_reset($testIp);
$initialCheck = auth_rate_limit_check($testIp);
assert_test("Rate limiting inicial para IP de prueba está desbloqueado", $initialCheck['locked'] === false);

for ($i = 0; $i < 5; $i++) {
    auth_rate_limit_fail($testIp);
}
$lockedCheck = auth_rate_limit_check($testIp);
assert_test("5 intentos fallidos activan el bloqueo por Rate Limiting", $lockedCheck['locked'] === true);

auth_rate_limit_reset($testIp);
$clearedCheck = auth_rate_limit_check($testIp);
assert_test("Login exitoso resetea el contador de Rate Limiting", $clearedCheck['locked'] === false);

// 3. Verificación de Sesión Segura
auth_session_start();
assert_test("Nombre de sesión personalizado configurado (itb_admin_sess)", session_name() === 'itb_admin_sess');

// 4. Verificación del Motor de Persistencia 100% MySQL (Transacciones, UPSERT)
$testData = storage_load();
assert_test("storage_load() carga datos válidos desde MySQL site_content", is_array($testData) && !empty($testData));

$saveResult = storage_save($testData);
assert_test("storage_save() ejecuta guardado transaccional en MySQL exitosamente", $saveResult === true);

// 5. Verificación de ID Autoincremental Seguro
$id1 = storage_next_id('test_col', $testData);
$id2 = storage_next_id('test_col', $testData);
assert_test("storage_next_id() genera IDs secuenciales únicos (ID1={$id1}, ID2={$id2})", $id2 === $id1 + 1);

// 6. Verificación de Protección HTTP (.htaccess)
$imgHtaccess  = dirname(__DIR__) . '/img/.htaccess';
assert_test("img/.htaccess existe para bloquear ejecución de scripts PHP", file_exists($imgHtaccess));

$imgContent = file_get_contents($imgHtaccess);
assert_test("img/.htaccess desactiva motor PHP (php_flag engine off)", strpos($imgContent, 'php_flag engine off') !== false);

// 7. Verificación de Capa PDO MySQL y Tablas Activas
$pdo = db();
assert_test("Conexión PDO a MySQL inicializada exitosamente", $pdo instanceof PDO);

if ($pdo) {
    $stmt = Database::query("SELECT 1 as alive");
    $row = $stmt ? $stmt->fetch() : null;
    assert_test("Consulta preparada PDO ejecutada con éxito", $row && $row['alive'] == 1);

    // Verificar existencia de tablas requeridas en MySQL
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    assert_test("Tabla site_content activa en MySQL", in_array('site_content', $tables));
    assert_test("Tabla form_registros activa en MySQL", in_array('form_registros', $tables));
    assert_test("Tabla auth_rate_limits activa en MySQL", in_array('auth_rate_limits', $tables));
}

echo "\n=== RESUMEN DE AUDITORÍA ===\n";
echo "Pruebas superadas: {$passed}\n";
echo "Pruebas fallidas:  {$failed}\n";

if ($failed === 0) {
    echo "¡TODAS LAS NORMAS DE CIBERSEGURIDAD Y BASE DE DATOS SE CUMPLEN AL 100%!\n";
} else {
    echo "Hay inconformidades pendientes por resolver.\n";
    exit(1);
}
