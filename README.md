# Instituto Superior Tecnológico Bolivariano (ITB)

Landing page institucional y Panel de Administración con arquitectura **schema-driven**, desarrollado para el Instituto Superior Tecnológico Bolivariano de Tecnología.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![Bootstrap Icons](https://img.shields.io/badge/Bootstrap%20Icons-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

---

## 📋 Sobre el proyecto

El proyecto está dividido en dos partes independientes:

- **Landing page pública** — sitio institucional del ITB con 13 secciones (hero, trayectoria, áreas de formación, programas, experiencia, testimonios, autoridades, servicios, admisión, noticias, alianzas, footer, etc.), construido con HTML modular vía includes de PHP, CSS puro (variables HSL, glassmorphism) y JavaScript vanilla.
- **Página "Sobre Nosotros"** (`sobre-nosotros.php`) — historia institucional, misión y visión, valores, autoridades, co-gobierno, himno institucional (letra, audio y foto editables desde el panel) y Transparencia/Leyes (documentos PDF descargables, subidos desde el panel).
- **Panel de Administración** — motor genérico y agnóstico para editar el contenido de la landing sin tocar código. Toda la configuración de campos vive en un único archivo (`admin/schema_mock.php`); el motor (`admin/coleccion.php`, `admin/editar.php`, `admin/singleton.php`) nunca se modifica para agregar o quitar un campo.

## 🛠️ Tecnologías

| Capa | Tecnología |
|---|---|
| Backend / Panel | PHP puro (sin frameworks), arquitectura schema-driven |
| Frontend público | HTML5 modular (includes PHP), CSS puro (variables HSL, glassmorphism), JavaScript vanilla |
| Panel admin (UI) | CSS/JS propios y aislados (`admin/assets/`) + Bootstrap Icons |
| Persistencia | JSON en disco (`data/content.json`), con soporte opcional para sincronizar a MySQL |

## 📂 Estructura del proyecto

```
├── index.php              # Ensambla la landing page pública
├── sobre-nosotros.php     # Página interna: historia, misión/visión, autoridades, himno, transparencia
├── includes/              # Vistas parciales de la landing (hero, trayectoria, footer, etc.)
├── css/ · js/             # Estilos e interacciones de la landing pública
├── img/ · svg/ · audio/   # Recursos gráficos y de audio (himno institucional)
├── docs/                  # PDFs públicos subidos desde el panel (Transparencia/Leyes)
└── admin/                 # Panel de Administración
    ├── schema_mock.php    # Fuente de verdad: define cada campo/colección/singleton
    ├── coleccion.php, editar.php, singleton.php   # Motor genérico (no tocar para agregar campos)
    ├── fields/            # Renderizado y parseo de cada tipo de campo (texto, imagen, audio, archivo, repeater, etc.)
    ├── views/layout.php   # Esqueleto visual del panel
    └── assets/            # CSS/JS exclusivos del panel (aislados del frontend público)
```

## 💾 Dónde vive el contenido

El contenido que se edita desde el panel **no está en la base de datos por defecto**: vive en archivos dentro de `data/`. MySQL es opcional.

Al **leer**, el orden de preferencia es: MySQL (tabla `site_content`) si está conectada y tiene filas → si no, `data/content.json` → si tampoco, la semilla `data/seed/content.json`.
Al **guardar**, siempre se escribe el JSON y, además, MySQL si hay conexión.

> Ojo con ese orden: **MySQL gana al leer**. Dos personas con el mismo código pueden ver contenidos distintos según lo que tenga cada una en su MySQL local. No es un fallo, es el diseño — pero conviene saberlo.

Las tablas no hay que crearlas a mano: `includes/db.php` ejecuta un `CREATE TABLE IF NOT EXISTS` en cuanto hay conexión.

### Qué archivos de `data/` viajan en el repositorio

| Archivo | ¿Va en el repo? | Qué guarda |
|---|---|---|
| `content.json` | **Sí** | El contenido del sitio: textos, imágenes, programas, menú |
| `seed/content.json` | **Sí** | Semilla de arranque para una instalación nueva |
| `content.json.bak_1..5` | No | Respaldos rotativos automáticos (las últimas 5 versiones) |
| `rate_limits.json` | No | Contador de intentos fallidos de acceso al panel |
| `registros.json` | **Sí, por ahora** | Solicitudes del formulario de admisión ⚠️ |

Los dos ignorados son estado local de cada máquina y se regeneran solos; no sirve de nada que viajen.

> ⚠️ **Pendiente con `registros.json`.** Guarda las solicitudes del formulario de admisión: nombre, apellido, email, teléfono y **cédula**. Hoy solo tiene registros de prueba, por eso se dejó dentro del repositorio. **Antes de que el formulario reciba solicitudes reales hay que sacarlo**, o se van a subir datos personales de gente real en cada commit.
>
> La regla ya está puesta en el `.gitignore`, pero eso no basta con un archivo que git ya venía siguiendo: hay que ejecutar además `git rm --cached data/registros.json`. Y avisar antes al equipo, porque a quien haga `pull` se le borrará su copia local.

### Editar contenido entre varias personas

`content.json` se reescribe **entero** en cada guardado del panel. Si dos personas editan a la vez, ese archivo choca en git y resolverlo a mano es muy incómodo.

Mientras no haya una MySQL compartida, la regla es simple: **que edite contenido una persona a la vez**, con `pull` antes y `push` después.

## 🚀 Cómo correr el proyecto

Hay dos formas de levantarlo — usa la que te resulte más cómoda, las dos funcionan igual:

### Opción A: Terminal con PHP (rápida, sin instalar nada)

Abre tu terminal en la carpeta del proyecto y ejecuta:

```
php -S localhost:8000 router.php
```

El `router.php` al final es importante: habilita las rutas "limpias" del panel (`/admin`, `/admin/login`) y la página 404 personalizada en este servidor. Si lo olvidas, el sitio sigue funcionando pero esas dos cosas no. Verás una salida en la terminal indicando que el servidor se inició en `http://localhost:8000`

### Opción B: XAMPP / WAMP / Laragon (Apache)

1. Copia la carpeta del proyecto dentro de `htdocs` (ej. `C:\xampp\htdocs\itb`).
2. Prende **Apache** desde el panel de control (no hace falta MySQL).
3. Entra por `http://localhost/itb` (o el nombre de carpeta que hayas usado) en vez de `localhost:8000`.

Aquí no hace falta el `router.php` — el `.htaccess` de la raíz ya trae la misma lógica (rutas limpias + 404 personalizada) para que Apache la use directamente.

### Paso 2: Abrir las páginas en tu navegador

Abre Chrome, Firefox o cualquier navegador y entra a las siguientes direcciones:

- 🌐 **Landing Page**: [http://localhost:8000](http://localhost:8000)
- 🔐 **Panel de Administración**: [http://localhost:8000/admin](http://localhost:8000/admin) — si no has iniciado sesión te manda al login; si ya la iniciaste, entras directo.
- 🔑 **Login**: [http://localhost:8000/admin/login](http://localhost:8000/admin/login) — si ya tienes sesión abierta, te redirige solo al panel.
- ⚙️ **Ejemplo de singleton** (Ajustes generales): [http://localhost:8000/admin/singleton.php?c=ajustes](http://localhost:8000/admin/singleton.php?c=ajustes)

> En un hosting real (Apache) no hace falta el `router.php`: el `.htaccess` de la raíz ya define las mismas rutas limpias.

### Credenciales de acceso al panel (demo)

```
Usuario:     admin
Contraseña:  1234
```

> ⚠️ Son credenciales de **demo/desarrollo**, pensadas para trabajar en local. La contraseña sí está hasheada (`password_hash`) y protegida con CSRF en cada formulario — pero antes de exponer el panel en un entorno real hay que cambiarla por una propia en `admin/config.php`.

## 📐 Reglas de arquitectura

1. **Schema-driven:** para agregar/quitar/modificar un campo administrable, el único archivo que se toca es `admin/schema_mock.php`.
2. **Motor agnóstico:** ningún archivo en `admin/fields/` debe contener lógica específica de negocio.
3. **Aislamiento de CSS/JS:** el CSS/JS del panel nunca se mezcla con el del frontend público, ni viceversa.

## 🔒 Seguridad implementada

- Contraseña del panel con `password_hash` / `password_verify` (no en texto plano).
- Token CSRF en todos los formularios que escriben (guardar, borrar, reordenar).
- `session_regenerate_id()` al loguear, para evitar session fixation.
- Rate limiting en el login (bloqueo temporal tras varios intentos fallidos) y expiración de sesión por inactividad.
- Subida de archivos (imágenes, audio, PDFs) con whitelist de extensión + verificación real del tipo MIME, y nombre renombrado a hash aleatorio.

## 🗺️ Próximos pasos

- Probar y afinar la sincronización con MySQL en un entorno real (hoy corre sobre JSON en disco).
- Sacar `data/registros.json` del repositorio antes de que el formulario reciba solicitudes reales (ver *Dónde vive el contenido*).
- Registrar la ruta limpia `/transparencia-leyes` en `router.php` y `.htaccess`, y enlazarla desde el menú (hoy solo responde por `/transparencia-leyes.php`).
- Optimizar carga y rendimiento SEO.
