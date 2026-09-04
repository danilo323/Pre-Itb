<?php
// admin/guardar.php
// Guarda temporalmente en $_SESSION hasta que Persona 3 conecte el storage.php real.

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

$section = $_POST['section'] ?? '';
$schema  = require __DIR__ . '/schema_mock.php';

// Validar que la sección existe en el schema (whitelist)
if (!array_key_exists($section, $schema)) {
    $_SESSION['flash_message'] = 'Sección no válida.';
    $_SESSION['flash_type']    = 'error';
    header('Location: /');
    exit;
}

// Guardar los campos en la sesión (temporal, sin BD)
if (!isset($_SESSION['admin_data'])) {
    $_SESSION['admin_data'] = [];
}

$campos_validos = array_keys($schema[$section]['fields']);
foreach ($campos_validos as $campo) {
    if (isset($_POST[$campo])) {
        $_SESSION['admin_data'][$section][$campo] = htmlspecialchars(trim($_POST[$campo]), ENT_QUOTES, 'UTF-8');
    }
}

$_SESSION['flash_message'] = '✅ Sección "' . htmlspecialchars($schema[$section]['label'], ENT_QUOTES, 'UTF-8') . '" guardada temporalmente.';
$_SESSION['flash_type']    = 'success';

// Volver a la landing con el panel abierto
header('Location: /');
exit;
