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
- **Panel de Administración** — motor genérico y agnóstico para editar el contenido de la landing sin tocar código. Toda la configuración de campos vive en un único archivo (`admin/schema_mock.php`); el motor (`admin/coleccion.php`, `admin/editar.php`, `admin/singleton.php`) nunca se modifica para agregar o quitar un campo.

## 🛠️ Tecnologías

| Capa | Tecnología |
|---|---|
| Backend / Panel | PHP puro (sin frameworks), arquitectura schema-driven |
| Frontend público | HTML5 modular (includes PHP), CSS puro (variables HSL, glassmorphism), JavaScript vanilla |
| Panel admin (UI) | CSS/JS propios y aislados (`admin/assets/`) + Bootstrap Icons |
| Persistencia | `$_SESSION` (en memoria) — la conexión a base de datos real es un paso pendiente |

## 📂 Estructura del proyecto

```
├── index.php              # Ensambla la landing page pública
├── includes/              # Vistas parciales de la landing (hero, trayectoria, footer, etc.)
├── css/                   # Estilos de la landing pública
├── js/                    # Interacciones JS de la landing pública
├── img/ · svg/            # Recursos gráficos
└── admin/                 # Panel de Administración
    ├── schema_mock.php    # Fuente de verdad: define cada campo/colección/singleton
    ├── coleccion.php, editar.php, singleton.php   # Motor genérico (no tocar para agregar campos)
    ├── fields/            # Renderizado y parseo de cada tipo de campo (texto, imagen, repeater, etc.)
    ├── views/layout.php   # Esqueleto visual del panel
    └── assets/            # CSS/JS exclusivos del panel (aislados del frontend público)
```

## 🚀 Cómo correr el proyecto

Hay dos formas de levantarlo — usa la que te resulte más cómoda, las dos funcionan igual:

### Opción A: Terminal con PHP (rápida, sin instalar nada)

Abre tu terminal en la carpeta del proyecto y ejecuta:

```
php -S localhost:8000 router.php
```

El `router.php` al final es importante: habilita las rutas "limpias" del panel (`/admin`, `/admin/login`) y la página 404 personalizada en este servidor. Si lo olvidas, el sitio sigue funcionando pero esas dos cosas no. Verás una salida en la terminal indicando que el servidor se inició en `http://localhost:8000`.

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

## 🗺️ Próximos pasos

- Conectar una base de datos real (persistencia hoy en `$_SESSION`).
- Rate limiting / bloqueo de intentos en el login.
- Optimizar carga y rendimiento SEO.
