<?php
// test_auth.php
// Script de prueba rápida para verificar el sistema de autenticación y seguridad de ITB

require_once __DIR__ . '/admin/auth.php';

echo "\n======================================================\n";
echo "   VALIDACIÓN DEL SISTEMA DE SEGURIDAD ITB ADMIN       \n";
echo "======================================================\n\n";

$testsPassed = 0;
$testsTotal = 0;

function checkTest(bool $condition, string $description): void {
    global $testsPassed, $testsTotal;
    $testsTotal++;
    if ($condition) {
        echo "  [\033[32mOK\033[0m] {$description}\n";
        $testsPassed++;
    } else {
        echo "  [\033[31mERROR\033[0m] {$description}\n";
    }
}

// 1. Conexión a Base de Datos
try {
    $pdo = db_connect();
    checkTest($pdo instanceof PDO, "Conexión PDO a MySQL exitosa");

    $stmt = $pdo->query("SELECT count(*) as total FROM admin_users");
    $row = $stmt->fetch();
    checkTest($row['total'] >= 1, "Tabla 'admin_users' accesible y con usuarios registrados");
} catch (Exception $e) {
    checkTest(false, "Fallo al conectar a MySQL: " . $e->getMessage());
}

// 2. Verificación de Protección CSRF
$token = csrf_token();
checkTest(!empty($token) && strlen($token) === 64, "Token CSRF generado (64 caracteres)");
checkTest(csrf_verify($token), "Validación timing-safe de CSRF exitosa");
checkTest(!csrf_verify('token_invalido'), "Rechazo de tokens CSRF adulterados");

// 3. Verificación de Helper de escape anti-XSS
$xss = '<script>alert("test")</script>';
checkTest(h($xss) === '&lt;script&gt;alert(&quot;test&quot;)&lt;/script&gt;', "Helper h() neutraliza inyecciones HTML/XSS");

// 4. Rate Limiting por IP
$ipTest = '10.0.0.123';
auth_rate_limit_clear($ipTest);
$rlInit = auth_rate_limit_check($ipTest);
checkTest($rlInit['allowed'] === true && $rlInit['remaining'] === 5, "Rate limit: 5 intentos disponibles por defecto");

for ($i = 0; $i < 5; $i++) {
    auth_rate_limit_record_fail($ipTest);
}
$rlBloqueado = auth_rate_limit_check($ipTest);
checkTest($rlBloqueado['allowed'] === false && $rlBloqueado['remaining'] === 0, "Rate limit: Bloqueo activo tras 5 intentos fallidos");
auth_rate_limit_clear($ipTest);

// 5. Autenticación con MySQL
$ipLocal = auth_get_client_ip();
auth_rate_limit_clear($ipLocal);

$bad = auth_attempt('admin@itb.edu.ec', 'ClaveErronea123');
checkTest($bad === false, "auth_attempt() rechaza contraseña incorrecta");

$good = auth_attempt('admin@itb.edu.ec', 'Admin123*');
checkTest($good === true, "auth_attempt() valida credenciales correctas ('admin@itb.edu.ec' / 'Admin123*')");
checkTest(!empty($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true, "Variable de sesión 'admin_logged' activada");
checkTest(auth_check() === true, "auth_check() reconoce la sesión activa");

// 6. Cierre de sesión
auth_logout();
checkTest(auth_check() === false, "auth_logout() destruye la sesión correctamente");

echo "\n------------------------------------------------------\n";
echo "  RESULTADO: {$testsPassed} de {$testsTotal} pruebas superadas exitosamente.\n";
echo "======================================================\n\n";
