## Paso 1: Iniciar el servidor local de PHP

## Abre tu terminal en la carpeta de tu proyecto y ejecuta este comando:

php -S localhost:8000

Verás una salida en la terminal indicando que el servidor se inició correctamente en http://localhost:8000

Paso 2: Abrir las páginas en tu navegador
Abre Chrome, Firefox o cualquier navegador y entra a las siguientes direcciones:

## a Landing Page (El trabajo de tu compañero): 👉 http://localhost:8000

## Tu Panel de Administración (Tu singleton con los campos): 👉 http://localhost:8000/admin/singleton.php

## Tus pruebas del motor de campos: 👉 http://localhost:8000/admin/test_motor.php

---

## ⚠️ NOTA IMPORTANTE PARA USUARIOS DE XAMPP ⚠️

Si estás ejecutando el proyecto desde XAMPP en una subcarpeta (por ejemplo: `C:\xampp\htdocs\itb`), las rutas absolutas (`localhost:8000`) de arriba no funcionarán igual. 

**Tus rutas correctas en XAMPP son:**
- **Página principal:** `http://localhost/itb/`
- **Panel de Administración:** `http://localhost/itb/admin/` (Asegúrate de incluir la barra `/` al final para que cargue correctamente).

### Arreglo de rutas (Si falla el CSS o el botón "Ver Sitio"):
Si el diseño del admin se rompe o el botón "Ver sitio" te lleva al dashboard de XAMPP:
1. Abre `admin/views/layout.php`.
2. Busca la línea del CSS y cámbiala a ruta relativa: `<link rel="stylesheet" href="assets/admin.css">`
3. Busca el botón "Ver sitio" y cámbialo a ruta relativa: `<a href="../" target="_blank" class="topbar-link">`
