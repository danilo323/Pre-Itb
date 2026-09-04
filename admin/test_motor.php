<?php
// admin/test_motor.php
// Script de prueba por consola (CLI) - Prueba TODOS los campos

require_once __DIR__ . '/fields/_loader.php';
$schema = require __DIR__ . '/schema_mock.php';

echo "=== PRUEBA DEL MOTOR DE CAMPOS (CLI) ===\n";
echo "=== 7 campos disponibles ===\n\n";

// -------------------------------------------------------
// 1. TEXT
// -------------------------------------------------------
echo "--- 1. TEXT ---\n";
echo field_render('titulo', 'Construye tu Futuro', ['type' => 'text', 'label' => 'Título Hero']);
echo "\n\n";

// -------------------------------------------------------
// 2. TEXTAREA
// -------------------------------------------------------
echo "--- 2. TEXTAREA ---\n";
echo field_render('descripcion', 'Formamos profesionales de alto nivel...', ['type' => 'textarea', 'label' => 'Descripción']);
echo "\n\n";

// -------------------------------------------------------
// 3. IMAGE
// -------------------------------------------------------
echo "--- 3. IMAGE ---\n";
echo field_render('foto_canciller', 'uploads/canciller.jpg', ['type' => 'image', 'label' => 'Foto del Canciller', 'help' => 'Tamaño recomendado: 400x400px']);
echo "\n\n";

// -------------------------------------------------------
// 4. BOOL
// -------------------------------------------------------
echo "--- 4. BOOL ---\n";
echo field_render('publicado', true, ['type' => 'bool', 'label' => 'Publicado', 'help' => 'Si está activo, se muestra en la landing']);
echo "\n\n";

// -------------------------------------------------------
// 5. SELECT
// -------------------------------------------------------
echo "--- 5. SELECT ---\n";
echo field_render('modalidad', 'hibrida', [
    'type' => 'select',
    'label' => 'Modalidad',
    'options' => [
        'presencial' => 'Presencial',
        'hibrida' => 'Presencial / Híbrida',
        'online' => 'Online',
    ]
]);
echo "\n\n";

// -------------------------------------------------------
// 6. DATE
// -------------------------------------------------------
echo "--- 6. DATE ---\n";
echo field_render('fecha', '2026-08-20', ['type' => 'date', 'label' => 'Fecha del Evento']);
echo "\n\n";

// -------------------------------------------------------
// 7. REPEATER (con subcampos)
// -------------------------------------------------------
echo "--- 7. REPEATER ---\n";
$faq_config = $schema['faq']['fields'];
$data_faq = [
    ['pregunta' => '¿Tienen clases presenciales?', 'respuesta' => 'Sí, en modalidad híbrida.'],
    ['pregunta' => '¿Cuánto dura la carrera?', 'respuesta' => '2 años (4 semestres).']
];
echo field_render('preguntas', $data_faq, $faq_config['preguntas']);
echo "\n\n";

// -------------------------------------------------------
// PRUEBAS DE PARSE (limpieza de datos)
// -------------------------------------------------------
echo "========================================\n";
echo "=== PRUEBAS DE PARSE (Guardado) ===\n";
echo "========================================\n\n";

echo "--- Parse TEXT (con espacios) ---\n";
$r = field_parse('text', '   Título con espacios   ', []);
echo "Resultado: '{$r}'\n\n";

echo "--- Parse BOOL ---\n";
$r = field_parse('bool', 1, []);
echo "Resultado: " . var_export($r, true) . "\n\n";

echo "--- Parse SELECT (valor válido) ---\n";
$r = field_parse('select', 'hibrida', ['options' => ['presencial' => 'P', 'hibrida' => 'H', 'online' => 'O']]);
echo "Resultado: '{$r}'\n";

echo "--- Parse SELECT (valor INVÁLIDO / ataque) ---\n";
$r = field_parse('select', 'HACKEADO', ['options' => ['presencial' => 'P', 'hibrida' => 'H']]);
echo "Resultado: '{$r}' (vacío = bloqueado)\n\n";

echo "--- Parse DATE (formato válido) ---\n";
$r = field_parse('date', '2026-08-20', []);
echo "Resultado: '{$r}'\n";

echo "--- Parse DATE (formato INVÁLIDO) ---\n";
$r = field_parse('date', 'no-soy-fecha', []);
echo "Resultado: '{$r}' (vacío = rechazado)\n\n";

echo "=== TODAS LAS PRUEBAS COMPLETADAS ===\n";
