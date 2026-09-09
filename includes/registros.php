<?php
// includes/registros.php
//
// Almacén de los registros que llegan del formulario de admisión de la landing.
//
// Se guardan en data/registros.json, que la carpeta data/ ya protege con su
// .htaccess (nadie puede abrirlo desde el navegador). Si además hay MySQL
// conectado se escribe una copia en la tabla form_registros, pero el JSON es el
// que manda al leer y al exportar: así no hay dudas sobre cuál de los dos tiene
// la verdad si uno de los dos falla.
//
// Aquí también viven las reglas de validación. Son LAS MISMAS que aplica
// js/main.js en el navegador, repetidas a propósito: la validación del
// navegador se salta desactivando JavaScript, así que la de verdad es esta.

require_once dirname(__DIR__) . '/includes/db.php';

/** Límite de caracteres de nombres y apellidos (también en el HTML y en el JS). */
const REGISTROS_MAX_NOMBRE = 25;

function registros_archivo(): string {
    $dir = dirname(__DIR__) . '/data';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    return $dir . '/registros.json';
}

/**
 * Devuelve todos los registros, del más reciente al más antiguo.
 */
function registros_leer(): array {
    $archivo = registros_archivo();
    if (!file_exists($archivo)) {
        return [];
    }
    $crudo = @file_get_contents($archivo);
    if ($crudo === false || $crudo === '') {
        return [];
    }
    $datos = json_decode($crudo, true);
    return is_array($datos) ? $datos : [];
}

/**
 * Escribe la lista completa. Escritura atómica (a un temporal y luego rename)
 * para que un corte a media escritura no deje el archivo a medias.
 */
function registros_escribir(array $lista): bool {
    $archivo = registros_archivo();
    $dir = dirname($archivo);

    $json = json_encode(array_values($lista), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    $tmp = $dir . '/registros.json.tmp_' . bin2hex(random_bytes(8));
    if (@file_put_contents($tmp, $json, LOCK_EX) === false) {
        if (file_exists($tmp)) @unlink($tmp);
        return false;
    }

    // En Windows rename() no sobrescribe, hay que quitar el destino antes.
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' && file_exists($archivo)) {
        @unlink($archivo);
    }
    if (!@rename($tmp, $archivo)) {
        @file_put_contents($archivo, $json, LOCK_EX);
        if (file_exists($tmp)) @unlink($tmp);
    }
    return true;
}

/**
 * Añade un registro ya validado y devuelve su id.
 */
function registros_agregar(array $datos): int {
    $lista = registros_leer();

    $ultimo = 0;
    foreach ($lista as $r) {
        $ultimo = max($ultimo, (int)($r['id'] ?? 0));
    }
    $id = $ultimo + 1;

    $fila = [
        'id'           => $id,
        'fecha'        => date('Y-m-d H:i:s'),
        'nombre'       => $datos['nombre'],
        'apellido'     => $datos['apellido'],
        'email'        => $datos['email'],
        'telefono'     => $datos['telefono'],
        'cedula'       => $datos['cedula'],
        'nacionalidad' => $datos['nacionalidad'],
        'bachiller'    => $datos['bachiller'],
        'carrera'      => $datos['carrera'],
        'modalidad'    => $datos['modalidad'],
        'mensaje'      => $datos['mensaje'],
    ];

    // Los más nuevos primero: es el orden en el que se quieren ver en el panel.
    array_unshift($lista, $fila);
    registros_escribir($lista);

    registros_copiar_a_mysql($fila);

    return $id;
}

function registros_eliminar(int $id): bool {
    $lista = registros_leer();
    $antes = count($lista);
    $lista = array_values(array_filter($lista, function ($r) use ($id) {
        return (int)($r['id'] ?? 0) !== $id;
    }));
    if (count($lista) === $antes) {
        return false;
    }
    registros_escribir($lista);

    $pdo = db();
    if ($pdo) {
        try {
            $pdo->prepare("DELETE FROM form_registros WHERE id = ?")->execute([$id]);
        } catch (Exception $e) {
            error_log('registros: no se pudo borrar en MySQL: ' . $e->getMessage());
        }
    }
    return true;
}

/**
 * Copia de seguridad en MySQL. Si no hay base de datos no pasa nada: el JSON
 * ya guardó el registro y es el que se lee.
 */
function registros_copiar_a_mysql(array $fila): void {
    $pdo = db();
    if (!$pdo) {
        return;
    }
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `form_registros` (
                `id`           INT           NOT NULL,
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
        $sql = "INSERT INTO form_registros
                (id, fecha, nombre, apellido, email, telefono, cedula, nacionalidad, bachiller, carrera, modalidad, mensaje)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE fecha = VALUES(fecha)";
        $pdo->prepare($sql)->execute([
            $fila['id'], $fila['fecha'], $fila['nombre'], $fila['apellido'],
            $fila['email'], $fila['telefono'], $fila['cedula'], $fila['nacionalidad'],
            $fila['bachiller'], $fila['carrera'], $fila['modalidad'], $fila['mensaje'],
        ]);
    } catch (Exception $e) {
        error_log('registros: no se pudo copiar a MySQL: ' . $e->getMessage());
    }
}

/* =============================================================
   VALIDACIÓN  (espejo exacto de la que hace js/main.js)
   ============================================================= */

/**
 * Cédula ecuatoriana: comprueba el dígito verificador con el algoritmo
 * módulo 10 del Registro Civil, no solo que sean 10 números.
 */
function registros_cedula_valida(string $ced): bool {
    if (!preg_match('/^\d{10}$/', $ced)) return false;
    $provincia = (int)substr($ced, 0, 2);
    if (($provincia < 1 || $provincia > 24) && $provincia !== 30) return false;
    if ((int)$ced[2] > 5) return false;
    $suma = 0;
    for ($i = 0; $i < 9; $i++) {
        $d = (int)$ced[$i] * ($i % 2 === 0 ? 2 : 1);
        if ($d > 9) $d -= 9;
        $suma += $d;
    }
    return (10 - ($suma % 10)) % 10 === (int)$ced[9];
}

/**
 * Valida lo recibido y devuelve ['datos' => [...limpios...], 'errores' => [campo => mensaje]].
 */
function registros_validar(array $post): array {
    $v = function ($k) use ($post) { return trim((string)($post[$k] ?? '')); };
    $digitos = function ($s) { return preg_replace('/\D/', '', $s); };

    $errores = [];

    // Letras (con tildes y ñ) unidas por un solo espacio, apóstrofo o guion.
    $RE_NOMBRE = '/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ]+(?:[ \'\-][A-Za-zÁÉÍÓÚÜÑáéíóúüñ]+)*$/u';

    $mb_len = function($s) {
        return function_exists('mb_strlen') ? mb_strlen($s) : strlen($s);
    };

    foreach (['nombre' => 'tus nombres', 'apellido' => 'tus apellidos'] as $campo => $etiqueta) {
        $val = $v($campo);
        if ($val === '') {
            $errores[$campo] = "Escribe $etiqueta.";
        } elseif ($mb_len($val) < 2) {
            $errores[$campo] = 'Debe tener al menos 2 letras.';
        } elseif ($mb_len($val) > REGISTROS_MAX_NOMBRE) {
            $errores[$campo] = 'No puede superar los ' . REGISTROS_MAX_NOMBRE . ' caracteres.';
        } elseif (!preg_match($RE_NOMBRE, $val)) {
            $errores[$campo] = 'Solo se permiten letras, espacios, apóstrofos y guiones.';
        }
    }

    $email = $v('email');
    if ($email === '') {
        $errores['email'] = 'Escribe tu correo electrónico.';
    } elseif ($mb_len($email) > 120) {
        $errores['email'] = 'El correo es demasiado largo.';
    } elseif (!preg_match('/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/', $email)) {
        $errores['email'] = 'El correo no tiene un formato válido (ejemplo@correo.com).';
    }

    $nacionalidad = $v('nacionalidad');
    if (!in_array($nacionalidad, ['ecuatoriano', 'extranjero'], true)) {
        $errores['nacionalidad'] = 'Selecciona tu nacionalidad.';
        $nacionalidad = 'ecuatoriano';
    }
    $extranjero = ($nacionalidad === 'extranjero');

    $telefono = $v('telefono');
    if ($telefono === '') {
        $errores['telefono'] = 'Escribe tu celular o WhatsApp.';
    } else {
        $d = $digitos($telefono);
        if (!$extranjero) {
            if (strpos($d, '593') === 0) $d = '0' . substr($d, 3);
            if (!preg_match('/^09\d{8}$/', $d)) {
                $errores['telefono'] = 'El celular debe tener 10 dígitos y empezar por 09 (ej. 0991234567).';
            }
        } elseif (strlen($d) < 7 || strlen($d) > 15) {
            $errores['telefono'] = 'Escribe un número válido de 7 a 15 dígitos, con el código del país.';
        }
    }

    $cedula = $v('cedula');
    if ($cedula === '') {
        $errores['cedula'] = $extranjero
            ? 'Escribe tu pasaporte o documento de identidad.'
            : 'Escribe tu número de cédula.';
    } elseif ($extranjero) {
        if (!preg_match('/^[A-Za-z0-9\-]{5,20}$/', $cedula)) {
            $errores['cedula'] = 'El documento debe tener de 5 a 20 caracteres (letras, números o guiones).';
        }
    } else {
        $d = $digitos($cedula);
        if (strlen($d) !== 10) {
            $errores['cedula'] = 'La cédula debe tener exactamente 10 dígitos.';
        } elseif (!registros_cedula_valida($d)) {
            $errores['cedula'] = 'La cédula no es válida. Revisa que no falte ni sobre un dígito.';
        }
    }

    if (!in_array($v('bachiller'), ['si', 'no'], true)) {
        $errores['bachiller'] = 'Indica si eres bachiller.';
    }

    // Carrera y modalidad se comprueban contra las opciones reales del panel:
    // así no se puede colar por POST un valor que no existe en el formulario.
    require_once __DIR__ . '/content_helper.php';
    $opciones = function (string $campo, array $porDefecto): array {
        $lista = content_raw('admision', $campo, []);
        $out = [];
        foreach ((array)$lista as $o) {
            $t = trim($o['texto'] ?? '');
            if ($t !== '') $out[] = $t;
        }
        return $out ?: $porDefecto;
    };
    $carreras = $opciones('lista_programas_interes', []);
    $modalidades = $opciones('lista_modalidades', []);

    $carrera = $v('carrera');
    if ($carrera === '') {
        $errores['carrera'] = 'Selecciona un programa o área de interés.';
    } elseif ($carreras && !in_array($carrera, $carreras, true)) {
        $errores['carrera'] = 'Esa opción no está en la lista.';
    }

    $modalidad = $v('modalidad');
    if ($modalidad === '') {
        $errores['modalidad'] = 'Selecciona la modalidad que prefieres.';
    } elseif ($modalidades && !in_array($modalidad, $modalidades, true)) {
        $errores['modalidad'] = 'Esa opción no está en la lista.';
    }

    $mensaje = $v('mensaje');
    if ($mb_len($mensaje) > 500) {
        $errores['mensaje'] = 'El comentario no puede superar los 500 caracteres.';
    }

    return [
        'errores' => $errores,
        'datos' => [
            'nombre'       => $v('nombre'),
            'apellido'     => $v('apellido'),
            'email'        => $email,
            'telefono'     => $telefono,
            'cedula'       => $cedula,
            'nacionalidad' => $nacionalidad,
            'bachiller'    => $v('bachiller'),
            'carrera'      => $carrera,
            'modalidad'    => $modalidad,
            'mensaje'      => $mensaje,
        ],
    ];
}

/* =============================================================
   EXPORTACIÓN A CSV
   ============================================================= */

function registros_columnas(): array {
    return [
        'id'           => 'ID',
        'fecha'        => 'Fecha de registro',
        'nombre'       => 'Nombres',
        'apellido'     => 'Apellidos',
        'email'        => 'Correo electrónico',
        'telefono'     => 'Celular / WhatsApp',
        'cedula'       => 'Cédula o documento',
        'nacionalidad' => 'Nacionalidad',
        'bachiller'    => 'Es bachiller',
        'carrera'      => 'Programa de interés',
        'modalidad'    => 'Modalidad preferida',
        'mensaje'      => 'Comentarios',
    ];
}

/**
 * Devuelve el contenido del CSV listo para descargar.
 *
 * Dos detalles pensados para que Excel lo abra bien de una vez:
 *   - Separador ';' en vez de ',': es lo que espera Excel en la configuración
 *     regional de España y Latinoamérica. Con ',' mete toda la fila en una
 *     sola celda.
 *   - BOM de UTF-8 al principio: sin él, Excel abre el archivo como ANSI y las
 *     tildes y la ñ salen como símbolos raros.
 */
function registros_a_csv(array $lista): string {
    $columnas = registros_columnas();

    $salida = fopen('php://temp', 'r+');

    // Los cuatro argumentos van SIEMPRE explicitos. Desde PHP 8.4 fputcsv()
    // avisa (Deprecated) si se omite $escape, y ese aviso se imprimiria dentro
    // del propio CSV, dejando el archivo inservible. Ademas '' desactiva el
    // escapado con barra invertida, que no es CSV estandar y confunde a Excel.
    $escribir = function ($fila) use ($salida) {
        fputcsv($salida, $fila, ';', '"', '');
    };

    $escribir(array_values($columnas));

    foreach ($lista as $r) {
        $fila = [];
        foreach (array_keys($columnas) as $k) {
            $valor = (string)($r[$k] ?? '');
            if ($k === 'bachiller') {
                $valor = ($valor === 'si') ? 'Sí' : (($valor === 'no') ? 'No' : $valor);
            }
            if ($k === 'nacionalidad') {
                $valor = ucfirst($valor);
            }
            // Un valor que empieza por = + - @ lo interpreta Excel como formula.
            // Se le antepone un apostrofo para que lo trate como texto.
            if ($valor !== '' && strpos("=+-@", $valor[0]) !== false) {
                $valor = "'" . $valor;
            }
            $fila[] = $valor;
        }
        $escribir($fila);
    }

    rewind($salida);
    $csv = stream_get_contents($salida);
    fclose($salida);

    return "\xEF\xBB\xBF" . $csv;   // BOM de UTF-8
}
