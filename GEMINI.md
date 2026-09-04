# Objetivo del Proyecto

Desarrollar una landing page integrada con un panel de administración en PHP puro (schema-driven) y base de datos SQL Server.
Mi rol (Persona 2) es desarrollar el **Motor del Panel de Administración**, que incluye:

- El dispatcher.
- El motor de campos (`fields/`), incluyendo campos complejos y repetibles (como el "repeater" para FAQs o galerías).
- Las vistas genéricas para editar configuraciones únicas (`singleton.php`) y listas de elementos (`coleccion.php`).

El objetivo es lograr un panel altamente modular, donde el contenido (textos, imágenes, preguntas frecuentes, etc.) sea editable automáticamente a partir del `schema.php`, sin necesidad de crear vistas por cada tabla o sección.

## Historial de Cambios

- 2026-09-03: Se crearon `test_conexion.php` y `.gemini/config_local.json`.

hola mundo

## estructura del proyecto

mi-proyecto-landing/
│
├── admin/ <-- [Trabajo de Persona 2 y Persona 3]
│ ├── assets/ <-- CSS y JS propios e independientes del Admin
│ ├── fields/ <-- Componentes del motor (text.php, image.php, etc.)
│ ├── views/ <-- Layout del panel (layout.php)
│ ├── auth.php <-- Manejo de sesiones y seguridad
│ ├── coleccion.php <-- Motor para listar colecciones
│ ├── config.example.php <-- Plantilla de configuración
│ ├── editar.php <-- Motor para formularios
│ ├── index.php <-- Dashboard del Admin
│ ├── schema.php <-- Definición de campos/secciones
│ ├── singleton.php <-- Motor para registros únicos
│ └── storage.php <-- Conexión a la BD / persistencia
│
├── css/ <-- [TUS ARCHIVOS - Persona 1]
│ └── estilos.css <-- Hoja de estilos de la Landing Page pública
│
├── js/ <-- [TUS ARCHIVOS - Persona 1]
│ └── main.js <-- Interacciones, animaciones y comportamiento
│
├── img/ <-- [TUS ARCHIVOS - Persona 1]
│ └── (logos, héroe, etc.) <-- Imágenes optimizadas extraídas del diseño
│
├── includes/ <-- [Compartido: Persona 1 y Persona 3]
│ ├── db.php <-- Conexión a la base de datos
│ ├── header.php <-- Encabezado/Navegación reusable
│ └── footer.php <-- Pie de página reusable
│
└── index.php <-- [TU ARCHIVO PRINCIPAL - Persona 1]

Punto de la guía ¿Tuyo? Estado Detalle
Layout único layout_start()/layout_end() ✅ ✅ Hecho
layout.php
Sidebar generado del schema ✅ ✅ Arreglado Ahora lee schema_mock.php y genera el menú solo
help por campo ✅ ✅ Arreglado Los 7 campos ahora soportan help
Flash messages ✅ ✅ Arreglado flash_set() + layout_flash() en el layout
Preview de imagen al subir ✅ ✅ Hecho image.php ya muestra preview
CSS propio en assets/ ✅ ⏸️ Esperando Cuando Persona 1 tenga el frontend
Confirmación antes de eliminar ✅ ⏸️ Esperando Cuando Persona 3 cree eliminar.php
Toggle inline publicado (AJAX) Compartido ⏸️ Esperando Cuando Persona 3 cree storage.php
Reordenar con flechas Compartido ⏸️ Esperando Cuando Persona 3 cree storage.php
Buscador JS en tabla ✅ ⏸️ Esperando Cuando coleccion.php tenga datos reales

---

### Estado Actual del Motor del Panel de Administración (Persona 2)

**✅ Lo que ya está construido:**
- **Layout base:** `admin/views/layout.php` (Genera el menú lateral leyendo el schema y maneja alertas flash).
- **Dispatcher:** `admin/fields/_loader.php` (Lógica para decidir qué campo pintar).
- **Campos (Fields):** Se crearon los 7 campos necesarios para toda la landing (`text`, `textarea`, `repeater`, `image`, `bool`, `select`, `date`). Incluyen funciones de renderizado (HTML) y validación estricta/limpieza (`parse`).
- **Vista Singleton:** `admin/singleton.php` (Genera el formulario dinámico con protección CSRF y whitelist de sección).
- **Integración Frontend-Backend (Temporales/Mock):** Se logró integrar al 100% las secciones de la landing (`hero.php`, `servicios.php`, `autoridades.php`, `testimonios.php`, `experiencia.php`, `programas.php`, `areas.php`) usando el motor. Se reescribió el `schema_mock.php` para que todas estas secciones operen como `singleton` (asegurando un orden visual exacto) y se habilitó la subida real de imágenes guardando rutas temporales en `$_SESSION`.

**🚀 Lo que falta programar (Trabajo pendiente de Persona 2):**
- **Vista Colección:** Programar `admin/coleccion.php` (La tabla que lista registros). *(Opcional si al final todas las secciones del frontend se manejan como singleton por diseño)*.
- **Vista Edición:** Programar `admin/editar.php` (El formulario para editar un ítem de la colección).
- **Javascript del Repeater:** Lógica en `admin/assets/admin.js` para clonar campos al presionar "+ Añadir".

**⏳ Tareas Críticas para Persona 3 (Backend & Base de Datos):**
- **Persistencia Real (SQL Server):** Actualmente, `admin/guardar.php` guarda todo temporalmente en la memoria de la sesión (`$_SESSION`), lo que significa que los datos se pierden si se reinicia el servidor. Persona 3 **debe** crear las funciones `storage_save()` y `storage_get()` para que la data se guarde y lea permanentemente desde SQL Server.
- **Schema Real:** Reemplazar el archivo temporal `admin/schema_mock.php` por el `admin/schema.php` definitivo.
- **Seguridad (Auth):** Implementar el sistema de login real contra la base de datos en `auth.php`, reemplazando el usuario "admin/1234" temporal.

**🎨 Tareas para Persona 1 (Frontend):**
- **Cuidado con el Merge (includes/):** Persona 1 debe tener extremo cuidado al entregar sus archivos finales de `includes/*.php`. **NO debe sobreescribirlos a ciegas**, de lo contrario borrará las etiquetas PHP (`<?= content_get() ?>`) que Persona 2 ya conectó al panel. Lo ideal es que Persona 2 haga la fusión manual del HTML final.
- **Paleta de Colores:** Proveer los colores finales para estilizar el panel de administración (`admin.css`) de forma armónica.

---

## 🖥️ Desarrollo Local

### Iniciar servidor:
```bash
php -S localhost:8000 -t /home/daly/Documentos/Innotech/Proyecto_Frontend
```

### URLs:

| Página | URL |
|---|---|
| 🌍 Landing Page (pública) | `http://localhost:8000` |
| 🔐 Inicio de Sesión Admin | `http://localhost:8000/admin/login.php` |
| 🚪 Cerrar Sesión Admin | `http://localhost:8000/admin/logout.php` |

### 🔑 Credenciales temporales:
| Campo | Valor |
|---|---|
| Usuario | `admin` |
| Contraseña | `1234` |

> ⚠️ Estas credenciales son temporales para desarrollo. La Persona 3 implementará el sistema de autenticación real contra SQL Server.

---

# Reglas de Memoria y Contexto del Proyecto ITB (Generadas por P1)

Este proyecto cuenta con un sistema de almacenamiento de memoria persistente en la carpeta `.gemini/`.

## Instrucciones de Memoria Persistente

1. **Al iniciar cada conversación**:
   - Revisa el archivo [`.gemini/memoria.md`](file:///c:/Users/owner/Downloads/itb/.gemini/memoria.md) para recordar el contexto general del proyecto, las decisiones técnicas tomadas, las tareas completadas y las preferencias del usuario.
   - Si existen detalles de conversaciones pasadas, revisa los archivos en [`.gemini/conversaciones/`](file:///c:/Users/owner/Downloads/itb/.gemini/conversaciones/).

2. **Durante y al finalizar tareas importantes**:
   - Actualiza [`.gemini/memoria.md`](file:///c:/Users/owner/Downloads/itb/.gemini/memoria.md) con nuevos acuerdos, avances, estado del proyecto o comandos clave.
   - Si se completa una fase del proyecto o conversación extensa, registra un resumen breve en la carpeta `.gemini/conversaciones/`.

3. **Idioma**:
   - Responder siempre en español.
