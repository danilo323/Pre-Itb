# Landing Page ITB & Motor de Administración

Este proyecto consiste en el desarrollo de una **Landing Page** moderna e interactiva para el Instituto Universitario ITB, conectada a un **Panel de Administración (CMS propio)** altamente modular basado en PHP puro (`schema-driven`) con persistencia en **MySQL**.

## Características Principales
- **Landing Page Dinámica:** 13 secciones modulares (Cabecera, Hero, Áreas, Programas, Noticias, Autoridades, Testimonios, Experiencia, Admisiones, etc.) que leen su contenido directamente del panel mediante `content_get()` y `content_raw()`.
- **Motor de Administración:** Genera toda la interfaz de edición, formularios y menús laterales automáticamente leyendo el archivo de configuración (`admin/schema_mock.php`).
- **Campos Inteligentes Soportados:** Texto, Área de texto, Imagen (con previsualización en vivo, arrastrar y soltar), Select, Fecha, Booleano, Repetidor y Constructor de Menú (`menu_builder`).
- **Seguridad y Persistencia:** Autenticación con PDO (MySQL), contraseñas hasheadas con bcrypt, protección contra fuerza bruta (Rate Limiting), tokens CSRF timing-safe y eliminación automática de imágenes reemplazadas.

---

## Instrucciones de Instalación y Ejecución Local

### Paso 1: Configurar la Base de Datos
Asegúrate de tener MySQL corriendo localmente y ejecuta el instalador automático:
```bash
php database/setup_db.php
```

### Paso 2: Iniciar el servidor local de PHP
Abre tu terminal en la carpeta del proyecto y ejecuta:
```bash
php -S localhost:8000
```
Verás una salida indicando que el servidor se inició correctamente en `http://localhost:8000`.

### Paso 3: Abrir las páginas en tu navegador
- 🌍 **Web Pública (Landing Page):** 👉 [http://localhost:8000/](http://localhost:8000/)
- 🔐 **Login Panel Admin:** 👉 [http://localhost:8000/admin/login.php](http://localhost:8000/admin/login.php)
  - **Usuario:** `admin@itb.edu.ec`
  - **Contraseña:** `Admin123*`
- ⚙️ **Panel de Administración (Singleton):** 👉 [http://localhost:8000/admin/singleton.php](http://localhost:8000/admin/singleton.php)
- 🧪 **Pruebas de Seguridad y Auth:** 👉 `php test_auth.php`
