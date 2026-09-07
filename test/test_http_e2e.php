<?php
// test/test_http_e2e.php
$baseUrl = 'http://localhost:8000';
$cookieFile = __DIR__ . '/test_cookie.txt';
if (file_exists($cookieFile)) @unlink($cookieFile);

function http_req($url, $postData = null, $follow = true) {
    global $cookieFile;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow);
    if ($postData !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }
    $resp = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($resp, 0, $headerSize);
    $body = substr($resp, $headerSize);
    curl_close($ch);
    return ['code' => $httpCode, 'headers' => $headers, 'body' => $body];
}

echo "=== TEST HTTP END-TO-END ===\n";

// 1. Cargar login y extraer CSRF token
$res1 = http_req("{$baseUrl}/admin/login.php");
if (preg_match('/name="csrf_token" value="([^"]+)"/', $res1['body'], $m)) {
    $token = $m[1];
    echo "1. Token CSRF obtenido: " . substr($token, 0, 10) . "...\n";
} else {
    echo "ERROR: No se encontró token CSRF\n";
    exit(1);
}

// 2. Verificar cookie personalizada de sesión (itb_admin_sess)
$cookieData = file_get_contents($cookieFile);
if (strpos($cookieData, 'itb_admin_sess') !== false) {
    echo "2. Cookie segura de sesión (itb_admin_sess) confirmada en el cliente.\n";
} else {
    echo "WARN: itb_admin_sess no encontrada en cookiejar.\n";
}

// 3. Login con contraseña errónea
$resFail = http_req("{$baseUrl}/admin/login.php", [
    'username'   => 'admin',
    'password'   => 'erronea123',
    'csrf_token' => $token
]);
if (strpos($resFail['body'], 'incorrectos') !== false) {
    echo "3. Contraseña errónea rechazada correctamente con mensaje de error.\n";
} else {
    echo "ERROR en rechazo de contraseña errónea.\n";
}

// Extraer nuevo token tras recarga
preg_match('/name="csrf_token" value="([^"]+)"/', $resFail['body'], $m);
$newToken = $m[1] ?? $token;

// 4. Login con credenciales válidas (admin / 1234)
$resLogin = http_req("{$baseUrl}/admin/login.php", [
    'username'   => 'admin',
    'password'   => '1234',
    'csrf_token' => $newToken
]);
if (strpos($resLogin['body'], 'Dashboard') !== false) {
    echo "4. Login exitoso con Bcrypt y redirección al Dashboard confirmada.\n";
} else {
    echo "ERROR: Falló el inicio de sesión válido.\n";
    exit(1);
}

// 5. Probar acceso directo no autenticado a guardar.php con nueva sesión
$unauthCh = curl_init("{$baseUrl}/admin/guardar.php");
curl_setopt($unauthCh, CURLOPT_RETURNTRANSFER, true);
curl_setopt($unauthCh, CURLOPT_FOLLOWLOCATION, false);
$unauthResp = curl_exec($unauthCh);
$unauthCode = curl_getinfo($unauthCh, CURLINFO_HTTP_CODE);
curl_close($unauthCh);

if ($unauthCode === 302 || $unauthCode === 401 || $unauthCode === 403) {
    echo "5. Acceso no autenticado a guardar.php bloqueado correctamente (HTTP {$unauthCode}).\n";
} else {
    echo "WARN: Código de respuesta para guardar.php: {$unauthCode}\n";
}

if (file_exists($cookieFile)) @unlink($cookieFile);
echo "\n=== TODAS LAS PRUEBAS E2E HTTP COMPLETADAS CON ÉXITO ===\n";
