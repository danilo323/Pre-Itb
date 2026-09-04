<?php
// admin/singleton.php
require_once __DIR__ . '/views/layout.php';
require_once __DIR__ . '/fields/_loader.php';

// Simulamos que cargamos el schema (Persona 3 hará esto)
$schema = require __DIR__ . '/schema_mock.php';

// Obtener qué sección estamos editando (ej: ?c=faq)
$section = $_GET['c'] ?? 'faq';

// SEGURIDAD: Whitelist — solo aceptar claves que existan en el schema
$secciones_validas = array_keys($schema);
if (!in_array($section, $secciones_validas, true) || $schema[$section]['type'] !== 'singleton') {
    die("Sección no encontrada o no es un singleton.");
}

$config = $schema[$section];

// Si es un POST (el usuario le dio a guardar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // SEGURIDAD: Verificar token CSRF antes de procesar cualquier dato
    // La Persona 3 creará la función csrf_verify() en auth.php
    // Por ahora lo dejamos preparado:
    // if (!csrf_verify($_POST['csrf_token'] ?? '')) {
    //     die('Token CSRF inválido. Recargá la página.');
    // }
    
    $data_to_save = [];
    foreach ($config['fields'] as $key => $field_config) {
        $raw_value = $_POST[$key] ?? null;
        $data_to_save[$key] = field_parse($field_config['type'], $raw_value, $field_config);
    }
    
    // Aquí la Persona 3 usaría storage_save()
    // Por ahora solo simulamos que guardamos
    echo "<div style='background:green; color:white; padding:10px;'>Guardado exitosamente. (Data simulada)</div>";
    
    // Para depurar en consola
    if (php_sapi_name() === 'cli') {
        print_r($data_to_save);
    }
}

// Simulamos obtener los datos actuales (Persona 3 usaría storage_get())
$current_data = [
    'titulo' => 'Preguntas Frecuentes',
    'preguntas' => [
        [
            'pregunta' => '¿Tienen clases presenciales?',
            'respuesta' => 'Sí, en modalidad híbrida.'
        ]
    ]
];

// Comienza el HTML
echo layout_start("Editando: " . $config['label']);
?>

<form method="POST" action="">
    <!-- SEGURIDAD: Token CSRF — Persona 3 lo generará en auth.php, nosotros lo incluimos -->
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    
    <?php
    // Bucle mágico: iteramos sobre el schema y dejamos que el dispatcher haga el trabajo
    foreach ($config['fields'] as $key => $field_config) {
        $value = $current_data[$key] ?? null;
        echo field_render($key, $value, $field_config);
    }
    ?>
    
    <div style="margin-top: 20px;">
        <button type="submit">Guardar Cambios</button>
    </div>
</form>

<?php
echo layout_end();
?>
