<?php
// procesar_registro.php
//
// Recibe el formulario de admisión de la landing y guarda el registro.
// Responde en JSON, porque js/main.js lo llama con fetch() y muestra el
// resultado sin recargar la página.
//
// Toda la validación se repite aquí aunque el navegador ya la haya hecho: la
// del navegador se salta desactivando JavaScript o mandando el POST a mano, así
// que la que protege de verdad es esta.

require_once __DIR__ . '/includes/registros.php';

header('Content-Type: application/json; charset=utf-8');

function responder(int $codigo, array $cuerpo): void {
    http_response_code($codigo);
    echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, ['ok' => false, 'mensaje' => 'Método no permitido.']);
}

// Trampa anti-spam: es un campo oculto que una persona nunca ve ni rellena,
// pero los robots que completan todo lo que encuentran sí. Si viene con algo,
// se descarta en silencio (se responde ok para no darle pistas al robot).
if (trim((string)($_POST['website'] ?? '')) !== '') {
    responder(200, ['ok' => true, 'mensaje' => 'Registro recibido.']);
}

$resultado = registros_validar($_POST);

if (!empty($resultado['errores'])) {
    responder(422, [
        'ok' => false,
        'mensaje' => 'Revisa los campos marcados.',
        'errores' => $resultado['errores'],
    ]);
}

$id = registros_agregar($resultado['datos']);

if ($id <= 0) {
    responder(500, ['ok' => false, 'mensaje' => 'No se pudo guardar el registro. Intenta de nuevo.']);
}

responder(200, ['ok' => true, 'id' => $id, 'mensaje' => 'Registro recibido.']);
