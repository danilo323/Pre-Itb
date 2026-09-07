# Informe QA — Panel de Administración ITB

**Fecha:** 2026-09-06
**Rama:** `feat/admin-motor`
**Cómo se probó:** servidor PHP de pruebas aislado (puerto 8899, ya apagado), login con `admin` / `1234`,
recorriendo cada campo del panel: guardar → releer el formulario → verificar que el cambio llegue a la landing.
No se modificó ningún archivo del proyecto durante las pruebas.

**Alcance:** todo el panel. La sección **Admisión** quedó fuera de la auditoría inicial (estaba a medio hacer) y se resolvió después — ver punto 7.

---

## ✅ Lo que funciona correctamente

- **Login** y protección de acceso: `singleton.php`, `coleccion.php` y `editar.php` redirigen al login sin sesión.
- **171 campos** de texto y textarea guardan y releen bien en Ajustes, Menú, Footer y las 10 secciones de Inicio.
- **Acentos, ñ, comillas y `<>`**: estables tras 3 guardados consecutivos, sin doble escapado.
- **XSS**: el HTML introducido sale escapado en la landing.
- **Imágenes** (cumple la regla 5 de CLAUDE.md):
  - Subida OK, el archivo queda en `img/` y la landing lo usa.
  - Al quitarla muestra "Ninguna imagen seleccionada".
  - Con ruta inexistente cae al placeholder **sin error rojo ni ruta técnica**.
- **Repeaters**: añadir, editar y eliminar items funciona. Un item con valor `0` no se descarta por error.
- **Visibilidad** de secciones: ocultar/mostrar funciona y el checkbox recuerda su estado.
- **Menú**: renombrar un item se refleja en el header de la landing.
- **Ajustes**: `site_name`, `description` y `keywords` llegan al `<title>` y a los `<meta>`.
- **Equipo**: listar, crear, editar, reordenar (arrastrar) y los checkboxes bool funcionan.

---

## ❌ Pendientes de arreglar

### ~~1. GRAVE — "Eliminar" en Equipo borra el registro equivocado~~ ✅ ARREGLADO (2026-09-06)

**Archivo:** `admin/coleccion.php`

Comprobado en vivo: se creó un registro con id=5, se pidió borrar el **5**, y desapareció el **4**.

**Causa:** `$idx` provenía del array local `$items`, que ya fue reindexado por `usort` (claves 0,1,2,3),
pero se usaba como clave del array de sesión, cuyas claves son otras (1,2,3,4):

```
foreach ($items as $idx => $item) {                                // lista de PANTALLA
    if ($item['id'] === $id_to_delete) {
        unset($_SESSION['admin_data'][$section]['items'][$idx]);   // <-- clave equivocada
```

**Cómo se arregló:** el borrado ahora recorre el array real de la sesión y compara por `id`,
que es el mismo patrón que ya usaba el bloque `save_order` unas líneas más arriba:

```
foreach (($_SESSION['admin_data'][$section]['items'] ?? []) as $sess_idx => $sess_item) {
    if ((int)($sess_item['id'] ?? 0) === $id_to_delete) {
        unset($_SESSION['admin_data'][$section]['items'][$sess_idx]);
```

El `(int)` en la comparación es a propósito: al conectar SQL Server los `id` llegarán como texto
desde PDO, y sin el casteo el borrado dejaría de encontrar el registro.

**Verificado con 6 pruebas automáticas:** borrar un registro recién creado; reordenar arrastrando
y luego borrar (el caso que más fallaba); tres borrados consecutivos; y pedir un id inexistente
(no borra nada). Todas pasan.

**No se tocó nada más:** es una corrección del motor, no un cambio de campos, así que no viola
la regla 1 de CLAUDE.md. El arreglo sirve para cualquier colección futura, no solo Equipo.

---

### ~~2. La sección Autoridades ignora por completo la colección Equipo~~ ✅ ARREGLADO (2026-09-06)

**Archivo:** `includes/autoridades.php`

**Corrección al diagnóstico original:** dije que las 4 personas "están escritas a mano". Es impreciso:
el include sí llamaba a `content_raw('autoridades', 'lista_autoridades', [...4 personas...])`, es decir,
sí le preguntaba al panel. El problema real es que el campo `lista_autoridades` **nunca existió en el
schema** — nadie lo creó — así que la pregunta siempre volvía vacía y siempre ganaba el respaldo fijo.

**Además era peor de lo que parecía a primera vista:** la propia sección Autoridades del panel tiene un
aviso azul que le dice al administrador _"para agregar/editar/eliminar estas personas, ve a CONTENIDO →
Equipo y marca 'Mostrar en Home'"_. Esa instrucción era **falsa** — el admin podía seguirla al pie de la
letra, ver el mensaje "Registro creado exitosamente", y la web seguía sin cambiar. Confundía al usuario
en vez de a un desarrollador.

**Cómo se arregló:** `includes/autoridades.php` ahora llama a `collection_items('equipo')` en vez del
campo inexistente, y:

- filtra solo personas con `publicado` y `mostrar_en_home` activos,
- respeta el `orden` que se define al arrastrar en el listado de Equipo,
- traduce los nombres de campo de la colección (`nombre_completo`, `foto`) a los que ya usaba la tarjeta
  (`nombre`, `imagen`),
- conserva el bloque de las 4 personas como **respaldo solo si la colección queda vacía** (nadie publicado),
  para que la sección nunca se quede en blanco.

**Bug adicional descubierto al probar:** `admin/fields/bool.php` guarda las casillas ("Publicado",
"Mostrar en Home") como `true`/`false` reales de PHP al editar desde el panel, pero los 4 registros de
ejemplo usan el texto `'1'`/`'0'`. Un filtro ingenuo (`=== '1'`) deja fuera a todo lo guardado desde el
panel. Se resolvió aceptando ambos formatos en el filtro. **Ojo con esto en cualquier otro sitio que
lea un campo `bool` del schema** — no es exclusivo de esta sección.

**Verificado con 25 pruebas automáticas** cubriendo: estado por defecto sin tocar nada; crear a alguien
y verlo aparecer; editar su nombre; desmarcar "Mostrar en Home" (desaparece); despublicar (sigue oculto
aunque se vuelva a marcar "Mostrar en Home"); republicar (reaparece); reordenar arrastrando (el orden de
la web cambia); borrar (desaparece sin afectar a los demás); y que el botón "Ver Directorio" y los textos
generales de la sección sigan intactos. Todas pasan.

**No se tocó el schema ni el motor genérico**, solo `includes/autoridades.php`. El typo `btn_directorio`
vs `boton_directorio` (punto 4 de este informe) sigue pendiente por separado.

---

**REVISIÓN**

### 3. El Footer está desincronizado entre schema y frontend

`admin/schema_mock.php` y `includes/footer.php` usan nombres de campo distintos.

**Campos en el panel que NO hacen nada (15):**
`descripcion`, `telefono`, `email`, `direccion`, `horario_semana`, `horario_sabado`,
`prefooter_titulo`, `prefooter_texto`, `prefooter_btn_1`, `prefooter_btn_2`, `prefooter_btn_3`,
`col1_titulo`, `col2_titulo`, `col3_titulo`, `enlaces_rapidos`

**Textos de la landing que NO se pueden editar (16):**
`cta_titulo`, `cta_desc`, `cta_btn_1`, `cta_btn_2`, `cta_btn_3`, `contacto_1`, `contacto_2`,
`enlaces_columna_1`, `enlaces_columna_2`, `lista_campus`, `mapa_img`, `direccion_mapa`,
`social_texto`, `gplus_url`, `vimeo_url`, `copyright`

**Único bloque que sí coincide:** las 5 URLs de redes sociales.

**Arreglo sugerido:** renombrar las claves en `admin/schema_mock.php` para que coincidan con las que
lee `includes/footer.php`. Es un cambio **solo de schema**, respeta la regla schema-driven.

---

### ~~4. Typo que deja un campo muerto~~ ✅ ARREGLADO (2026-09-06)

El panel guardaba `autoridades.btn_directorio`, pero `includes/autoridades.php:16` leía `boton_directorio`.
Es el botón "Ver Directorio →" que aparece junto al título de la sección "Nuestras Autoridades" — un
error puramente de texto, no afectaba nada más de la sección ni del sitio.

**Cómo se arregló:** se renombró la clave en `admin/schema_mock.php` de `btn_directorio` a
`boton_directorio`, para que coincida exactamente con lo que lee el frontend. Cambio de una sola línea,
solo en el schema — no se tocó el motor ni `includes/`.

**Verificado en vivo:** se cambió el texto del botón desde el panel a "Conoce a las Autoridades" y se
confirmó que aparece así en la landing; se restauró a "Ver Directorio" y también se reflejó. Se comprobó
además que la sección sigue visible y el resto de sus campos (título, descripción, etiqueta) no se vieron
afectados.

**Nota aparte, NO es un error:** ese mismo botón tiene `href="#"` en el HTML — al hacer clic no lleva a
ningún lado todavía. Es intencional: la landing sigue en construcción y ese `#` es un marcador de
posición hasta que exista la página de Directorio a la que debería apuntar. No tiene relación con el
desajuste de nombres que sí se arregló, y no requiere ninguna acción por ahora.

---

### ~~5. Campos que existen en el panel pero no tienen destino en la landing~~ ✅ RESUELTO (2026-09-06)

Eran dos casos distintos, cada uno con su propio arreglo:

**A) `programas.btn_ver_todos`** — el botón "Ver todos los programas" existía en la landing, pero su
texto estaba fijo en `includes/programas.php:73`, sin conectar al campo del panel. Se conectó con
`content_get('programas', 'btn_ver_todos', ...)`, igual que el resto de textos de esa sección.
**Verificado:** cambiar el texto desde el panel y ver el cambio reflejado en la landing; luego
restaurado al valor original.

**B) `experiencia.badge_numero` y `experiencia.badge_texto`** — antes de tocar nada se releyó
`includes/experiencia.php` completo (63 líneas) y se buscó "badge" en todos los `.css` del proyecto:
**no existe ningún elemento con ese propósito en el frontend**, ni en el HTML ni en el CSS — no es que
esté mal conectado, es que nunca se construyó. Como no hay nada que conectar y solo servían para
confundir al administrador (editar un campo que nunca cambia nada visible), se **quitaron del schema**
en `admin/schema_mock.php`. También se renombró el divisor de esa sección de "Imagen e Indicador
(Derecha)" a "Imagen (Derecha)", porque ya no había ningún indicador que ese título describiera.
**Verificado:** los dos campos ya no aparecen en el formulario del panel; el campo "Imagen" de esa misma
sección sigue intacto; el resto de la sección Experiencia se sigue viendo igual.

**Si en el futuro se quiere agregar un indicador real** (ej. un "98%" flotando sobre la imagen de la
sección Experiencia) habría que diseñarlo primero en el frontend (HTML + CSS) y recién ahí agregar sus
campos de vuelta al schema — eso es trabajo de diseño nuevo, no una corrección de un error existente.

---

### 6. Seguridad — 🚫 FUERA DE ALCANCE (no corresponde a este trabajo)

**Decisión del usuario (2026-09-06):** este punto pertenece a la integración de backend/base de datos
(SQL Server + Auth real), que según el propio `CLAUDE.md` del proyecto es un paso pendiente aparte
("Conectar backend y seguridad real") y que `admin/login.php` ya marca como tarea de otra persona
("Persona 3 reemplazará con hash real"). No se toca en esta auditoría.

Queda documentado para quien retome esa integración:

- **CSRF nunca se genera, y tampoco se valida.** Se buscó en todo `admin/` de dónde sale el valor de
  `csrf_token` que se imprime en los 4 formularios (`singleton.php`, `editar.php`, `coleccion.php`) y
  **no existe ningún lugar del código que lo genere** — el campo siempre viaja vacío. Y aunque se
  generara, tampoco hay ningún archivo que lo compare al recibir el POST. Confirmado en vivo: un POST
  sin token se guarda igual.
- **`guardar.php` no comprueba `admin_logged`.** Hoy no hay daño real porque escribe en la sesión del
  propio visitante, pero **se convierte en un agujero real al conectar SQL Server**, cuando ese archivo
  empiece a escribir en una base de datos compartida por todos los visitantes en vez de en la sesión de
  cada quien.

---

### ~~7. La sección Admisión no coincidía con el formulario real~~ ✅ RESUELTO (2026-09-06)

Esta sección quedó fuera de la auditoría original porque estaba a medio hacer. Al revisarla se encontró
el mismo patrón que en el Footer (punto 3): el panel y el frontend hablaban de cosas distintas.

**Campos del panel que no existían en el formulario** (se eliminaron): `etiqueta_superior`, `feature_1`,
`feature_2`, `feature_3`, `form_terminos`.

**Cosas del formulario que el panel no dejaba tocar** (se agregaron):
- `imagen_principal` — el frontend ya la leía, pero no había campo en el panel.
- `form_subtitulo` — la línea "Los campos marcados con un asterisco (*) son obligatorios", que estaba
  fija en el código. Ahora el administrador solo escribe un `*` y el asterisco se pinta en naranja solo,
  sin tener que escribir HTML.
- `lista_programas_interes` y `lista_modalidades` — las dos listas desplegables del formulario
  ("Programa o Área de Interés" y "Modalidad Preferida"), que estaban escritas a mano en el HTML.

**Mejora al motor genérico (beneficia a TODOS los repeaters, no solo a Admisión):** se agregaron botones
de flecha ↑ ↓ a cada item de cualquier repeater, para reordenarlos sin arrastrar. Se implementó en
`admin/fields/repeater.php` (los botones) y `admin/assets/admin.js` (el movimiento, por delegación de
eventos para que los items recién añadidos también funcionen). Al mover un item se renumeran sus campos
por posición, y el orden se guarda con el botón "Guardar cambios" normal de la página.
A diferencia del listado de Equipo, aquí **no se muestra ninguna columna de ID** — el administrador solo
ve el contenido y las flechas.

**Verificado con 22 pruebas automáticas:** que los campos correctos aparecen y los muertos ya no; que las
dos listas traen sus 7 y 3 opciones; que las flechas se pintan en todos los repeaters; que la web muestra
las opciones en el orden del panel; que reordenar cambia el orden en la web; que añadir y quitar opciones
funciona; y que los textos nuevos se reflejan. Además se probó por separado la lógica de reordenar
(subir, bajar, extremos, repeaters con imágenes y con varios subcampos): correcta en todos los casos.

**Nota:** las etiquetas de los campos del formulario (Nombres, Apellidos, Correo, Cédula, Nacionalidad,
Soy Bachiller, etc.) y las opciones de los dos grupos de radio siguen fijas en el código. No se hicieron
editables a propósito: son estructura del formulario, no contenido editorial, y sus nombres internos los
va a necesitar quien conecte el backend. Se pueden agregar después si se pide.

**Segunda pasada (mismo día): el botón circular de video.** El círculo naranja con el texto giratorio
"¿CÓMO INSCRIBIRSE? • HAZ CLIC AQUÍ" tampoco era administrable — solo bajaba hasta el formulario, sin
posibilidad de poner un video. Se agregaron dos campos:

- `video_url` — el administrador pega el link normal de YouTube (acepta tanto `youtu.be/xxx` como
  `youtube.com/watch?v=xxx`) y el video se abre en la ventana modal que ya existía para el Hero.
  Si se deja vacío, o si lo pegado no es un link de YouTube reconocible, el botón vuelve a su
  comportamiento anterior (bajar al formulario) en vez de abrir una ventana en negro.
- `circular_text` — el texto que gira alrededor del botón, que estaba fijo en el HTML.

**De paso se eliminó una duplicación:** el regex que convierte una URL de YouTube a formato "embed"
estaba escrito dentro de `includes/hero.php`. Copiarlo a `admision.php` habría creado dos copias que
después se desincronizan (justo el problema que documenta todo este informe). Se movió a una función
compartida `youtube_embed_url()` en `includes/content_helper.php`, y **`hero.php` ahora la usa también**.
No hizo falta tocar `js/main.js`: el modal de video ya funcionaba con cualquier elemento que tenga la
clase `js-video-modal-trigger`.

**Verificado con 19 pruebas automáticas**, incluyendo específicamente que **no se rompió el video del
Hero** (que también se modificó): que su botón sigue ahí, que sigue convirtiendo bien su URL, y que
cambiar su link desde el panel sigue funcionando. Más los dos formatos de link de YouTube, el texto
circular editable, el link inválido y el campo vacío.

---

### ~~8. La ventana de video no aparecía + dos mejoras de usabilidad~~ ✅ RESUELTO (2026-09-06)

**8.a — GRAVE: la ventana de video nunca se abría (ni en el Hero ni en Admisión).**
Al hacer clic en el botón circular no pasaba nada. La causa: **los estilos del modal no existían en el
CSS activo**. Cuando `styles.css` se dividió en archivos por sección, las reglas `.hero__video-modal*`
se quedaron en `css/styles.css.bak` — el archivo de respaldo, que no se carga. El JavaScript sí abría la
ventana (le ponía la clase `--active`), pero sin estilos no tenía ni posición, ni fondo, ni tamaño: era
invisible. Es el mismo tipo de desconexión que documenta todo este informe, pero en el CSS.
**Arreglo:** se recuperaron esas 83 líneas desde el `.bak` a `css/hero.css`, con un comentario que
explica de dónde vienen para que no se vuelvan a perder. Verificado con 11 comprobaciones sobre el CSS
que realmente llega al navegador.

**Nota sobre el sonido:** los navegadores **bloquean que un video arranque solo con sonido**; es una
regla del navegador, no un error del código. En Chrome suele funcionar tras el clic; en Firefox
normalmente hay que darle play una vez. La única forma de garantizar que arranque siempre es agregar
`mute=1` (empezaría sin sonido). Queda a decisión del equipo.

**8.b — El texto de la descripción se salía hacia la derecha.**
El navegador solo corta el texto en los espacios, así que una palabra larguísima —o una URL pegada— se
desbordaba del recuadro en vez de bajar. **Arreglo** en `css/admision.css`: `overflow-wrap: break-word`
en la descripción y `min-width: 0` en su columna (mismo patrón que ya se usaba unas líneas más arriba
para la marquesina del título). No se alteró el diseño.

**8.c — Los puntitos (•) del texto circular ya no se escriben a mano.**
Antes el administrador tenía que copiar y pegar el carácter `•` y acordarse de ponerlo también al final;
si lo olvidaba, al dar la vuelta al círculo la última frase se pegaba con la primera. De hecho
`hero.php` tenía un parche a la fuerza para corregir un caso así que había quedado mal guardado.
**Arreglo:** los campos `circular_text` (Hero y Admisión) pasaron de `text` a `textarea`, y ahora el
administrador escribe **una frase por línea, sin símbolos**. Una función compartida
`content_circular()` en `includes/content_helper.php` las une con ` • ` y agrega el separador final.
El parche del `hero.php` se pudo eliminar.
Acepta también el formato antiguo (todo en un renglón con los `•` a mano), así que el contenido ya
guardado se sigue viendo igual, y de paso normaliza el espaciado irregular.
**Verificado con 16 pruebas unitarias** (formato nuevo, formato viejo, una sola frase, líneas en blanco,
campo vacío, escapado de HTML) **+ 12 pruebas end-to-end** sobre el panel y la web reales.

**Precedente respetado:** "una cosa por línea" ya se usa en `includes/footer.php` para los enlaces de
columnas y la lista de campus, así que no se inventó un patrón nuevo.

---

## 📌 Observaciones menores

- `includes/alianzas.php` se renderiza en la landing pero **no tiene ninguna entrada en el panel**.
- Al guardar una sección se reescriben todos sus campos: si un campo se deja vacío se guarda vacío
  y la landing muestra vacío (ya no vuelve al texto por defecto). Es comportamiento normal de CMS,
  pero conviene tenerlo presente.

---

## Orden sugerido para atacarlo

| #     | Tarea                                                                                                   | Dónde                      | Respeta schema-driven      |
| ----- | ------------------------------------------------------------------------------------------------------- | -------------------------- | -------------------------- |
| ~~1~~ | ~~Borrado del registro equivocado en Equipo~~ ✅ hecho                                                  | `admin/coleccion.php`      | Sí (es fix del motor)      |
| 2     | ~~Validar CSRF + exigir login en `guardar.php`~~ 🚫 fuera de alcance — le toca a integración backend/BD | `admin/guardar.php`        | Sí (es fix del motor)      |
| 3     | Renombrar claves del Footer                                                                             | `admin/schema_mock.php`    | Sí, solo schema            |
| ~~4~~ | ~~Renombrar `btn_directorio` → `boton_directorio`~~ ✅ hecho                                            | `admin/schema_mock.php`    | Sí, solo schema            |
| ~~5~~ | ~~Conectar Autoridades a la colección Equipo~~ ✅ hecho                                                 | `includes/autoridades.php` | Tocó frontend, no el motor |
| ~~6~~ | ~~Campos muertos de Programas/Experiencia~~ ✅ hecho                                                    | schema + `includes/programas.php` | Sí, y un cambio menor en frontend |
| ~~7~~ | ~~Conectar la sección Admisión~~ ✅ hecho                                                               | schema + `includes/admision.php` | Sí, y un cambio menor en frontend |
| 8     | Revisar otros usos de campos `bool` del schema (mismo problema string vs. boolean que se encontró aquí) | por ubicar                 | —                          |
