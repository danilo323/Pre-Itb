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
- **Pruebas (CLI):** `admin/test_motor.php` (Script de pruebas por consola que verifica el renderizado y parse de los campos exitosamente).

**🚀 Lo que falta programar (Trabajo pendiente de Persona 2):**
- **Vista Colección:** Programar `admin/coleccion.php` (La tabla que lista registros).
- **Vista Edición:** Programar `admin/editar.php` (El formulario para editar un ítem de la colección).
- **Javascript del Repeater:** Lógica en `admin/assets/admin.js` para clonar campos al presionar "+ Añadir".

**⏳ Dependencias (Bloqueado esperando a Persona 1 y 3):**
- **Backend (P3):** Funciones `storage_save()` y `storage_get()` para persistir datos reales en SQL Server.
- **Backend (P3):** El archivo `schema.php` real y la autenticación `auth.php` (Login/CSRF).
- **Frontend (P1):** Paleta de colores/CSS público para crear los estilos del admin (`admin.css`) de forma armónica.
