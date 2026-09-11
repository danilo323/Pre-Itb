# Plan de Implementación: Creador Dinámico de Páginas en el Panel de Administración

Este plan describe la arquitectura y los pasos para permitir que el administrador cree nuevas páginas personalizadas desde el panel, heredando secciones/componentes existentes (como Misión, Módulos, Noticias, Autoridades, Hero, etc.), asignando una URL personalizada (slug), eligiendo un ícono de Bootstrap Icons y definiendo su ubicación en el menú navegable (Padre/Hijo).

---

## 1. Resumen de Requerimientos Entendidos

1. **Ubicación en el Menú Lateral (`PÁGINAS`)**:
   - Bajo la sección **PÁGINAS** del panel, se mostrarán:
     - `Inicio`
     - `Sobre Nosotros`
     - *(Todas las páginas personalizadas creadas por el admin)*
     - **Crear Página** (Opción ubicada siempre **al final** del bloque PÁGINAS, con el ícono por defecto `bi-plus-circle-fill`).

2. **Formulario de Creación y Edición**:
   - **Nombre de la Página**: Título visible en el panel y en la pestaña del navegador (ej: "Academia ITB").
   - **URL / Slug**: Ruta limpia amigable (ej: `web-itb-academia`).
   - **Selector de Ícono (Bootstrap Icons)**: Catálogo/modal interactivo visual para seleccionar el ícono que aparecerá en el menú lateral del panel.
   - **Ubicación en el Menú Navegable Público (Padre / Hijo)**:
     - Campo desplegable para elegir dónde aparecerá en el menú de la web pública:
       1. *No agregar al menú público* (solo por URL directa).
       2. *Menú Principal (Padre)* (ej. al lado de Instituto, Oferta Académica, Admisiones).
       3. *Submenú (Hijo) de...* (elige el menú padre existente de quien va a colgar, ej: dentro de *Instituto* o *Nuestra Institución 1*).
     - Al guardar, el sistema inyecta automáticamente la página en el menú navegable público en la posición y nivel elegidos.
   - **Selección de Secciones / Componentes Heredados**:
     - Interfaz inspirada en la captura del compañero: un selector/checklist categorizado con encabezados en negrita (**Sobre Nosotros**, **Modelo de Gestión**, **Otras Secciones**) donde el administrador elige qué bloques de la plantilla incluir en esta nueva página (ej: Hero, Misión, Autoridades, Formulario de Admisión, etc.).
   - **Edición Continua**: Las páginas creadas se pueden volver a editar (título, slug, ícono, ubicación en menú o secciones heredadas) o eliminar en cualquier momento.

3. **Renderizado Público (Frontend)**:
   - Al visitar la URL creada (`/web-itb-academia` o mediante `router.php`), la página pública se armará automáticamente incluyendo solo las secciones elegidas por el administrador en el orden correspondiente.

---

## 2. Alineación con `GUIA-PANEL-ADMIN.md` y `GEMINI.md`

- **Paleta de Colores Oficial**: Interfaz administrativa con Azul Marino (`#1A3B70`), Naranja (`#F15A24`), Azul Medianoche (`#0F2243`), Gris Hielo (`#F4F6F9`) y Blanco Puro.
- **Almacenamiento en `data/content.json`**: Se guardará la colección de `paginas_personalizadas` manteniendo la persistencia atómica con `LOCK_EX` y copias de seguridad rotativas (`storage_save`).
- **Nombres de Íconos**: Uso exclusivo de la librería oficial Bootstrap Icons ya integrada en el proyecto.

---

## 3. Preguntas Abiertas y Aclaraciones para el Usuario

1. **Formato de URL en la Web Pública**:
   - ¿Prefieres que las URLs se accedan directamente como `http://localhost/web-itb-academia` (manejadas transparentemente por `router.php` y `.htaccess`), o como `http://localhost/pagina.php?p=web-itb-academia`?
   - *Recomendación*: Usar rutas limpias vía `router.php` (ej. `http://localhost/web-itb-academia`).

2. **Categorización de Secciones Heredables**:
   - Para el selector de secciones con negritas estilo la foto de tu compañero, hemos agrupado las secciones del sitio en 3 grandes bloques:
     - **Sobre Nosotros**: `#about` (Acerca de), `#mision` (Misión y Visión), `#valores` (Valores), `#equipo` (Autoridades), `#himno` (Himno e Identidad).
     - **Modelo de Gestión / Formación**: `#areas` (Áreas de Formación), `#programas` (Carreras/Programas), `#servicios` (Servicios e Instalaciones).
     - **Otras Secciones**: `#hero` (Portada Hero), `#noticias` (Noticias y Eventos), `#transparencia` (Transparencia / Leyes), `#contacto` (Formulario de Admisión), `#alianzas` (Alianzas y Convenios).
   - ¿Te parece bien este agrupamiento o quieres agregar algún encabezado adicional?

---

## 4. Cambios Propuestos por Componente

### [Panel de Administración (Backend)]

#### [MODIFY] [`layout.php`](file:///c:/Users/coddy/Desktop/Pre-Itb-feat-admin-motor/admin/views/layout.php)
- Inyectar dinámicamente las páginas personalizadas creadas dentro del grupo `paginas` en el menú lateral.
- Añadir el elemento fijo **Crear Página** al final del grupo `paginas`.

#### [NEW] [`paginas_crear.php`](file:///c:/Users/coddy/Desktop/Pre-Itb-feat-admin-motor/admin/paginas_crear.php)
- Formulario intuitivo para definir Nombre, Slug, Ícono Bootstrap, Ubicación en Menú Navegable (Padre/Hijo) e inspeccionar las Secciones Heredables categorizadas.

#### [NEW] [`paginas_gestionar.php`](file:///c:/Users/coddy/Desktop/Pre-Itb-feat-admin-motor/admin/paginas_gestionar.php)
- Procesador de guardado, actualización y eliminación de páginas dinámicas en `data/content.json` y sincronización automática con el `menu_builder`.

### [Sitio Público (Frontend)]

#### [MODIFY] [`router.php`](file:///c:/Users/coddy/Desktop/Pre-Itb-feat-admin-motor/router.php)
- Capturar la ruta amigable solicitada y renderizar la plantilla dinámica incluyendo las secciones elegidas.

#### [NEW] [`pagina_dinamica.php`](file:///c:/Users/coddy/Desktop/Pre-Itb-feat-admin-motor/pagina_dinamica.php)
- Plantilla maestra genérica que carga las secciones seleccionadas por el administrador conservando el header, footer y estilos oficiales.

---

## 5. Plan de Verificación

### Pruebas Manuales
1. Entrar al panel en la sección **PÁGINAS** y verificar que aparezca la opción **Crear Página** abajo de "Inicio" y "Sobre Nosotros".
2. Crear una nueva página llamada "Academia ITB" con el slug `web-itb-academia`, seleccionar el ícono `bi-mortarboard-fill`, ubicarla como submenú (Hijo) de "Instituto" y marcar las secciones `#hero`, `#mision` y `#programas`.
3. Guardar y verificar que "Academia ITB" aparezca en el menú lateral del panel justo arriba de "Crear Página" y en la barra de navegación del sitio público desplegando "Instituto".
4. Hacer clic en "Ver sitio" e ir a `http://localhost/web-itb-academia` para verificar que la página cargue correctamente con las secciones seleccionadas.
5. Volver al panel y editar la página para cambiarle el ícono o agregar otra sección y verificar la actualización.
