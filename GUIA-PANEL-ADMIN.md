# Guía: cómo armar una base administrativa en PHP

Guía de referencia para construir un panel de administración en PHP plano
(sin framework), del tipo del que vive en [admin/](../admin/) en este proyecto.
No es un manual de uso: es el "cómo se hace y qué hay que tener en cuenta".

---

## 1. La decisión que define todo: schema-driven vs. a mano

Hay dos maneras de hacer un panel:

**A) A mano (lo que casi todos hacen primero).**
Un archivo `.php` por pantalla: `cursos_listar.php`, `cursos_editar.php`,
`docentes_listar.php`, `docentes_editar.php`… Cada uno con su HTML, su
validación y su `INSERT`/`UPDATE` copiados del anterior.
Funciona el primer mes. Al décimo módulo tenés 4.000 líneas duplicadas y
agregar un campo significa tocar 4 archivos.

**B) Schema-driven (lo recomendable).**
Un solo archivo declara *qué* se administra, y un motor genérico genera
dashboard, menús, listados y formularios a partir de eso.

```
schema.php   ← declarás:  "cursos" es una colección, tiene título, imagen, precio…
   ↓
motor        ← coleccion.php / editar.php / singleton.php leen el schema
   ↓
UI           ← el formulario, la tabla y el menú salen solos
```

Agregar un campo = 3 líneas en el schema. **No se toca código del motor.**
Ese es el criterio de éxito: si para agregar un campo tenés que abrir el
motor, el diseño está mal.

> En este proyecto: [admin/schema.php](../admin/schema.php) es la fuente de
> verdad; [admin/coleccion.php](../admin/coleccion.php),
> [admin/editar.php](../admin/editar.php) y
> [admin/singleton.php](../admin/singleton.php) son el motor.

---

## 2. Los dos tipos de contenido que necesitás

Casi todo contenido administrable cae en uno de dos moldes. Modelá solo estos
dos y cubrís el 95% de los casos:

| Tipo | Qué es | Ejemplo | Se guarda como |
|---|---|---|---|
| **singleton** | Un único registro editable | Home, Footer, Ajustes, Contacto | un objeto |
| **collection** | Lista de N registros con alta/baja/modificación | Cursos, Docentes, Noticias | un array de objetos |

Un singleton no necesita listado, ni IDs, ni botón "Nuevo". Una colección
necesita listado, orden, ID, borrado y (casi siempre) un flag `publicado`.
Si mezclás los dos en un mismo flujo, el código se llena de `if`.

---

## 3. Anatomía mínima de la carpeta

```
admin/
  config.php          ← credenciales y rutas (NUNCA al repo)
  config.example.php  ← plantilla que sí va al repo
  auth.php            ← sesión, login, CSRF
  storage.php         ← leer/escribir datos
  schema.php          ← LA declaración de todo
  index.php           ← dashboard
  login.php / logout.php
  coleccion.php       ← listado de una colección
  editar.php          ← alta/edición de un item
  eliminar.php        ← POST + CSRF
  toggle.php          ← publicar/despublicar (AJAX)
  reordenar.php       ← subir/bajar (AJAX)
  upload.php          ← subida de archivos (AJAX)
  singleton.php       ← edición de un singleton
  fields/             ← un archivo por tipo de campo
  views/layout.php    ← cabecera + sidebar + pie, una sola vez
  assets/             ← admin.css / admin.js propios del panel
```

Regla: **cada archivo raíz del panel es una acción, no una entidad.**
No hagas `cursos.php`, `docentes.php`, `noticias.php`. Hacé `coleccion.php?c=cursos`.

---

## 4. El sistema de campos (la pieza que más rinde)

Cada tipo de campo es un archivo con **dos funciones y siempre las mismas dos**:

```php
// admin/fields/text.php
function field_text_render(string $name_path, $value, array $config): string { … }
function field_text_parse($raw, array $config) { … }
```

- `render` → devuelve el HTML del input.
- `parse` → toma lo que llegó por `$_POST` y devuelve el valor limpio a guardar.

Un dispatcher las invoca por convención de nombre:

```php
function field_render(string $name_path, $value, array $config): string {
    $fn = "field_{$config['type']}_render";
    return function_exists($fn) ? $fn($name_path, $value, $config)
                                : '<div class="field-error">Tipo no soportado</div>';
}
```

**Agregar un tipo nuevo = crear un archivo e incluirlo en el loader.** Nada más.

Tipos que en la práctica vas a necesitar sí o sí:

- Básicos: `text`, `textarea`, `number`, `bool`, `email`, `url`, `date`, `select`
- Derivados: `slug` (se genera del título), `hidden`
- Compuestos: `group` (agrupa visualmente), `repeater` (N filas de subcampos)
- Archivos: `image`, `gallery`, `file`, `video`
- Relacionales: `select_from` (elegí un item de otra colección por su ID)
- Texto rico: `richtext`

El **repeater** es el que convierte un panel rígido en uno realmente
administrable (listas de beneficios, materias, pasos, FAQ…). Costoso de
implementar bien —requiere clonar plantillas en JS y nombres tipo
`campo[0][sub]`— pero es lo que evita volver a pedirte cambios de código.

> En este proyecto: [admin/fields/](../admin/fields/) — 20 tipos, uno por archivo.

---

## 5. Dónde guardar los datos: JSON vs. MySQL

| | JSON en `data/` | MySQL |
|---|---|---|
| Instalación | cero | crear BD, usuario, esquema |
| Backup | copiar carpeta / `git` | dump |
| Migraciones | no existen (cambiás el schema y ya) | `ALTER TABLE` por cada cambio |
| Búsqueda/filtros | en PHP, sobre todo el array | SQL, con índices |
| Concurrencia | hay que hacer lock manual | la maneja el motor |
| Techo razonable | ~cientos de items por archivo | millones |

Para un sitio institucional (cursos, docentes, noticias, páginas) **JSON
alcanza y sobra**, y te ahorra media capa de infraestructura. Para un sistema
con usuarios finales, transacciones o miles de registros, MySQL.

Si elegís JSON, tu `storage.php` **debe** tener:

1. **Lock exclusivo al escribir** (`LOCK_EX`) — dos guardados simultáneos
   corrompen el archivo.
2. **Escritura atómica**: escribir a un `.tmp` y `rename()` encima. Un corte
   a mitad de `file_put_contents` te deja el JSON roto y el sitio caído.
3. **Backup rotativo** antes de sobrescribir (últimos N). Es tu "deshacer".
4. **ID autoincremental** para colecciones (nunca reutilizar IDs borrados).
5. `JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES`
   — para que los acentos y las URLs se lean y el diff en git sirva.
6. **Semilla**: `data/seed/` versionado y `data/` ignorado, así el primer
   deploy arranca con contenido y las ediciones del cliente no chocan con git.

> En este proyecto: [admin/storage.php](../admin/storage.php).

---

## 6. Seguridad: la lista que no se negocia

Un panel es la puerta de entrada al sitio. Esto no es opcional:

- **Contraseña hasheada**, nunca en claro:
  `password_hash($p, PASSWORD_DEFAULT)` + `password_verify()`.
  El hash va en `config.php`, fuera del repo.
- **CSRF en TODO formulario que escribe**: token aleatorio en sesión,
  campo oculto, y `hash_equals()` al recibir. Sin excepciones —también en
  los endpoints AJAX (`toggle`, `reordenar`, `upload`, `eliminar`).
- **Nunca borrar/modificar por GET.** Un `eliminar.php?id=5` lo dispara
  cualquier prefetch del navegador. Solo POST.
- **`session_regenerate_id(true)` al loguear** (previene session fixation).
- **Cookie de sesión** con `httponly => true`, `samesite => 'Lax'`,
  `secure => true` si hay HTTPS. Nombre de sesión propio, no `PHPSESSID`.
- **Rate limit en el login**: N intentos y bloqueo por X minutos, por IP.
- **Expiración por inactividad**: guardá `last_activity` y destruí la sesión
  pasado el timeout.
- **Escapar SIEMPRE al imprimir**: `htmlspecialchars($v, ENT_QUOTES, 'UTF-8')`.
  Definí un helper `$h = fn($s) => htmlspecialchars(...)` y usalo en cada eco.
- **Whitelist, nunca blacklist**, para todo lo que venga del cliente:
  claves del schema, carpetas de upload, extensiones, nombres de colección.
  `$_GET['c']` se valida contra el schema *antes* de tocar nada.
- **Path traversal**: jamás concatenes input del usuario en una ruta.
  `data/{$_GET['c']}.json` con `c=../../config` te vacía el servidor.
- Si usás MySQL: **PDO con sentencias preparadas**. Sin excepción.

### Uploads (donde más se rompe todo)

1. Requiere sesión + CSRF.
2. Whitelist de extensiones **y** verificación del MIME real con `finfo`
   (la extensión miente).
3. **Renombrar a un hash** + extensión. Nunca uses el nombre original.
4. Whitelist estricta de carpeta destino.
5. Tamaño máximo validado en PHP (no confíes solo en `php.ini`).
6. En el `.htaccess` de `uploads/`: **desactivar la ejecución de PHP**.
   Un `.jpg` que en realidad es PHP deja de ser problema si el directorio
   no ejecuta nada.
7. Respondé **JSON limpio**: `ini_set('display_errors','0')` en el endpoint,
   porque un warning impreso rompe el `JSON.parse()` del front y el usuario
   ve "error desconocido".

> En este proyecto: [admin/auth.php](../admin/auth.php) y
> [admin/upload.php](../admin/upload.php).

---

## 7. Configuración y despliegue

```php
// config.example.php  → SÍ va al repo
return [
    'admin_email'  => 'admin@ejemplo.com',
    'admin_hash'   => '$2y$10$...',   // generar con password_hash()
    'session_name' => 'mi_admin_sess',
    'session_timeout' => 3600,
    'login_max_attempts' => 5,
    'login_lockout_seconds' => 900,
    'data_path'    => __DIR__ . '/../data',
    'uploads_path' => __DIR__ . '/../uploads',
    'max_upload_mb'=> 5,
];
```

- `config.php` en `.gitignore`; `config.example.php` versionado.
- Rutas **absolutas** derivadas de `__DIR__`, nunca relativas: el panel se
  incluye desde distintos niveles y en hosting compartido el cwd no es el que creés.
- Permisos: `data/` y `uploads/` escribibles (755 y dueño correcto en cPanel;
  777 casi nunca hace falta y es un riesgo).
- Pantalla de **cambiar contraseña** dentro del panel: si el cliente no puede
  cambiarla solo, la vas a terminar cambiando vos por teléfono.

---

## 8. UX del panel (esto decide si el cliente lo usa o te llama)

- **Un layout único** (`views/layout.php`) con `layout_start()` / `layout_end()`.
  Cada pantalla solo imprime su contenido.
- **Sidebar generado del schema**, agrupado (`Globales`, `Páginas`, `Contenido`)
  y ordenado con un `order`. Sin menús escritos a mano.
- **`help` por campo.** Una línea explicando qué se ve en el sitio y qué medida
  tiene la imagen ahorra el 80% de las consultas.
- **Flash messages** por sesión: guardado / error / eliminado.
- **Confirmación antes de eliminar**, y mejor aún: *despublicar* en vez de borrar.
- **Toggle inline de publicado/destacado** en el listado (AJAX) — es la acción
  más frecuente y no debería exigir entrar a editar.
- **Reordenar con flechas** (guardando un campo `orden`). Drag & drop es lindo
  pero frágil en móvil; las flechas nunca fallan.
- **Buscador de listado en JS** sobre la tabla ya renderizada: 20 líneas y
  suficiente hasta varios cientos de filas.
- **Preview de imagen** al subir, con la URL final visible.
- El CSS del panel es **propio y aislado** (`admin/assets/`). No lo mezcles
  con el del sitio público ni al revés.

---

## 9. Errores típicos (y cómo evitarlos)

| Error | Consecuencia | Prevención |
|---|---|---|
| Lógica de negocio en el motor genérico | El motor deja de ser genérico | Todo lo específico va al schema |
| Guardar sin backup | Un guardado malo pierde el contenido | Backup rotativo en `storage_save()` |
| Borrar por GET | Borrados fantasma | POST + CSRF |
| Confiar en la extensión del archivo | RCE por upload | `finfo` + rename a hash + no ejecutar PHP en `uploads/` |
| `echo $valor` sin escapar | XSS almacenado | Helper `$h()` en cada salida |
| `config.php` commiteado | Credenciales expuestas | `.gitignore` + `.example` |
| Rutas relativas | Rompe en producción | `__DIR__` siempre |
| Un `.php` por entidad | Duplicación exponencial | `coleccion.php?c=…` |
| Sin `help` en los campos | Soporte eterno | Un `help` por campo |
| Campos que el cliente no debe tocar, editables | Contenido roto | `readonly` en el schema |

---

## 10. Orden de construcción sugerido

Construilo en este orden; cada paso deja algo usable:

1. `config.php` + `auth.php` (login, logout, CSRF, timeout).
2. `views/layout.php` + `index.php` con el sidebar vacío.
3. `storage.php` (load/save/backup/next_id) — probalo con un JSON de juguete.
4. `fields/` con solo `text` y `textarea` + el dispatcher.
5. `schema.php` con **un** singleton real y `singleton.php`. Guardá y verificá.
6. `coleccion.php` + `editar.php` + `eliminar.php` con **una** colección real.
7. El resto de los tipos de campo, por demanda: primero `image` y `bool`,
   después `repeater`, `select_from`, `richtext`.
8. `toggle.php`, `reordenar.php`, buscador.
9. Conectar el sitio público a los mismos JSON.
10. Manual del cliente + pantalla de cambio de contraseña.

No arranques por el repeater ni por el richtext: son los más caros y los que
menos se necesitan al principio.

---

## 11. Checklist antes de entregar

- [ ] `config.php` fuera del repo y con hash real (no el de ejemplo).
- [ ] Contraseña cambiada desde el propio panel.
- [ ] HTTPS forzado y cookie `secure`.
- [ ] `uploads/` no ejecuta PHP.
- [ ] `data/` no accesible por URL (o los JSON no exponen nada sensible).
- [ ] CSRF verificado en todos los POST y endpoints AJAX.
- [ ] Backups de `data/` (rotación local + copia externa).
- [ ] Todo campo del schema tiene `label` y, si no es obvio, `help`.
- [ ] Probado el flujo completo: crear, editar, subir imagen, reordenar,
      despublicar, eliminar — y ver el resultado en el sitio público.
- [ ] Manual corto para el cliente, con capturas.

---

## Referencia rápida de este proyecto

| Pieza | Archivo |
|---|---|
| Declaración de contenido | [admin/schema.php](../admin/schema.php) |
| Sesión, login, CSRF | [admin/auth.php](../admin/auth.php) |
| Lectura/escritura JSON | [admin/storage.php](../admin/storage.php) |
| Motor de campos | [admin/fields/_loader.php](../admin/fields/_loader.php) |
| Listado de colección | [admin/coleccion.php](../admin/coleccion.php) |
| Formulario de item | [admin/editar.php](../admin/editar.php) |
| Subida de archivos | [admin/upload.php](../admin/upload.php) |
| Datos vivos | [data/](../data/) |
| Manual del cliente | [MANUAL.md](MANUAL.md) |
| Despliegue | [DEPLOY.md](DEPLOY.md) |
