# Landing Page ITB & Motor de Administración

Este proyecto consiste en el desarrollo de una **Landing Page** moderna e interactiva para el Instituto Universitario ITB, conectada a un **Panel de Administración (CMS propio)** altamente modular basado en PHP puro (`schema-driven`).

## Estado Actual: Versión 1 (Prototipo sin Base de Datos)
El proyecto actualmente cuenta con todo el motor Frontend y Backend visual completamente finalizado y fusionado en la rama `main`. Todos los textos, imágenes, videos y configuraciones se almacenan temporalmente en la sesión del navegador (`$_SESSION`), listos para ser conectados por la Persona 3 a SQL Server.

### Características Principales
- **Landing Page Dinámica:** 13 secciones modulares (Cabecera, Hero, Áreas, Programas, Noticias, Autoridades, Testimonios, Experiencia, Admisiones, etc.) que leen su contenido directamente del panel mediante `content_get()` y `content_raw()`.
- **Motor de Administración:** Genera toda la interfaz de edición, formularios y menús laterales automáticamente leyendo un solo archivo de configuración (`admin/schema_mock.php`).
- **7 Campos Inteligentes Soportados:** Texto, Área de texto, Imagen (con previsualización en vivo, arrastrar y soltar, y botón de eliminar), Select, Fecha, Booleano y Repetidor (para crear galerías o preguntas frecuentes infinitas).

## Desarrollo Local (Instrucciones)
Para levantar el servidor de pruebas local correctamente, abre tu terminal y ejecuta:

```bash
cd /home/daly/Documentos/Innotech/Proyecto_Frontend
php -S localhost:8000
```

### Accesos Rápidos
Una vez que el servidor esté corriendo, haz clic en estos enlaces:
- 🌍 **Web Pública:** [http://localhost:8000/](http://localhost:8000/)
- 🔐 **Login Panel Admin:** [http://localhost:8000/admin/login.php](http://localhost:8000/admin/login.php)

> **Credenciales temporales de desarrollo:**
> Usuario: `admin` | Contraseña: `1234`
