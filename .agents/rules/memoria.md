# Memoria General y Contexto del Proyecto ITB

> **Propósito:** Este archivo unifica el contexto del proyecto y las instrucciones operativas. Reemplaza a todos los antiguos archivos "GEMINI.md" y ".gemini/memoria.md". Siendo una "System Rule", la IA lo lee obligatoria y automáticamente en cada interacción.

---

## Datos Generales del Proyecto

- **Nombre:** Instituto Superior Tecnológico Bolivariano de Tecnología (ITB) - Landing Page & Panel de Administración
- **Repositorio Git:** `https://github.com/danilo323/Pre-Itb.git`
- **Rama Actual:** `feat/frontend-landing`
- **Tecnologías:**
  - **Frontend:** Vanilla CSS (variables HSL, glassmorphism), JS puro, HTML5 modular (inclusiones PHP en `includes/`).
  - **Backend / Panel:** PHP puro, arquitectura **schema-driven** (motor en `admin/`, sin vistas específicas por tabla).
  - **Base de Datos:** Inicialmente guardado en memoria (`$_SESSION`), futura conexión a SQL Server.

## Estructura del Código

- **`index.php`**: Ensambla la landing page pública.
- **`includes/`**: Vistas parciales de la landing (`hero.php`, `trayectoria.php`, `header.php`, etc.).
- **`css/`** y **`js/`**: Estilos e interacciones de la página pública (Landing).
- **`admin/`**: Todo el código del Panel de Administración.
  - **`schema.php`** (o `schema_mock.php` temporalmente): **Fuente de verdad única.** Todo campo, colección o singleton se define aquí.
  - **`coleccion.php`**, **`editar.php`**, **`singleton.php`**: El **Motor Genérico**. Nunca deben modificarse para añadir/quitar campos.
  - **`assets/admin.css`** y **`admin.js`**: JS/CSS **aislados y exclusivos** para el panel.
  - **`fields/`**: Archivos de renderizado y parseo de campos (image, text, repeater, etc.).

## Reglas de Operación (Para la IA)

1. **Mantener la arquitectura Schema-Driven:** Para agregar un campo administrable, modifica `admin/schema.php`. Si para agregar un campo modificas un archivo en `admin/fields/` o el motor principal, estás violando las directrices del proyecto.
2. **Aislamiento de código:** El Frontend de la Landing y el Backend del Panel no comparten JS ni CSS. Sus dependencias están aisladas.
3. **Persistencia (Tarea pendiente Persona 3):** Los datos actualmente se guardan en la sesión. La integración final de persistencia SQL Server deberá usar `storage_save()` y `storage_get()`.

## Historial de Logros Principales

- Diseño frontend terminado al 100% (13 secciones) por Persona 1.
- Panel de Administración (Versión 1) funcional con almacenamiento temporal por Persona 2.
- Generación dinámica de formularios (textos, textarea, imágenes, repeaters) conectada exitosamente a la Landing (`content_get()` y `content_raw()`).
- Motor de subida de imágenes con validación de existencia en disco e interfaz con UI mejorada (se evita el icono de imagen rota al quitar fotos).

## Próximos Pasos

- Conectar backend y seguridad real (SQL Server, Auth) por Persona 3.
- Optimizar carga y rendimiento SEO.
