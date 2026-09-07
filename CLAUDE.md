# CLAUDE.md — Proyecto ITB

> Este archivo es la fuente única de reglas e instrucciones del proyecto para la IA. Reemplaza a `.agents/AGENTS.md`, `.agents/rules/*.md` y a los antiguos `GEMINI.md` / `.gemini/memoria.md`. Se lee automáticamente en cada sesión de Claude Code.

## Datos generales del proyecto

- **Nombre:** Instituto Superior Tecnológico Bolivariano de Tecnología (ITB) - Landing Page & Panel de Administración
- **Repositorio Git:** `https://github.com/danilo323/Pre-Itb.git`
- **Tecnologías:**
  - **Frontend:** Vanilla CSS (variables HSL, glassmorphism), JS puro, HTML5 modular (inclusiones PHP en `includes/`).
  - **Backend / Panel:** PHP puro, arquitectura **schema-driven** (motor en `admin/`, sin vistas específicas por tabla).
  - **Base de datos:** Actualmente en memoria (`$_SESSION`), futura conexión a SQL Server.

## Estructura del código

- `index.php`: ensambla la landing page pública.
- `includes/`: vistas parciales de la landing (`hero.php`, `trayectoria.php`, `header.php`, etc.).
- `css/` y `js/`: estilos e interacciones de la página pública (landing).
- `admin/`: todo el código del Panel de Administración.
  - `schema.php` (o `schema_mock.php` temporalmente): **fuente de verdad única**. Todo campo, colección o singleton se define aquí.
  - `coleccion.php`, `editar.php`, `singleton.php`: el **motor genérico**. Nunca deben modificarse para añadir/quitar campos.
  - `assets/admin.css` y `admin.js`: JS/CSS **aislados y exclusivos** para el panel.
  - `fields/`: archivos de renderizado y parseo de campos (image, text, repeater, etc.).

## Reglas de operación (obligatorias)

1. **Arquitectura Schema-Driven:** para agregar/quitar/modificar un campo administrable, el único archivo que se toca es `admin/schema.php`. NUNCA modifiques `admin/coleccion.php`, `admin/editar.php` o `admin/singleton.php` para eso. Si para agregar un campo tienes que tocar el motor, estás violando la regla. Si tienes dudas de implementación, lee `GUIA-PANEL-ADMIN.md` antes de escribir código.
2. **Motor 100% agnóstico:** ningún archivo dentro de `admin/fields/` debe contener lógica específica del negocio (ej. "si el campo es trayectoria hacer X").
3. **Aislamiento de CSS/JS:** el CSS y JS del panel (`admin/assets/admin.css`, `admin.js`) son exclusivos del panel y no deben mezclarse con el código del frontend público (`js/main.js`, `css/estilos.css`), ni viceversa.
4. **Seguridad y normalización:** respeta la lógica de CSRF y la normalización de arrays de archivos.
5. **Manejo de imágenes en el admin:** las imágenes deben previsualizarse por defecto apuntando a los archivos que ya existen en el frontend (ej. `img/enfermeria.jpg`). Si un administrador cambia o elimina esa imagen (campo vacío o ruta inexistente), NO debe mostrarse un error rojo ni la ruta técnica esperada — mostrar el placeholder estándar ("Ninguna imagen seleccionada") sin alertas de archivo faltante.
6. **Persistencia (tarea pendiente):** los datos se guardan en sesión. La integración final con SQL Server debe usar `storage_save()` y `storage_get()`.

## Historial de logros

- Diseño frontend terminado al 100% (13 secciones).
- Panel de Administración (v1) funcional con almacenamiento temporal.
- Generación dinámica de formularios (textos, textarea, imágenes, repeaters) conectada a la landing (`content_get()` y `content_raw()`).
- Motor de subida de imágenes con validación de existencia en disco (se evita el icono de imagen rota al quitar fotos).

## Próximos pasos

- Conectar backend y seguridad real (SQL Server, Auth).
- Optimizar carga y rendimiento SEO.
