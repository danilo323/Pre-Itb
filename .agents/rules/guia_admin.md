# Regla del Panel de Administración (Schema-Driven)

Para cualquier tarea, cambio o revisión relacionada con el Panel de Administración (archivos dentro de la carpeta `admin/`), debes OBLIGATORIAMENTE cumplir con las siguientes reglas arquitectónicas descritas en `GUIA-PANEL-ADMIN.md`:

1. **Arquitectura Schema-Driven:** NUNCA modifiques el código del motor (`admin/coleccion.php`, `admin/editar.php`, `admin/singleton.php`) para agregar o quitar campos. TODO cambio en los campos debe hacerse modificando el archivo `admin/schema.php`. Si para agregar un campo tienes que tocar el motor, estás violando la regla.
2. **Aislamiento de CSS/JS:** El CSS y JS del panel (`admin/assets/admin.css` y `admin/assets/admin.js`) son exclusivos del panel y no deben mezclarse con el código del frontend público (`js/main.js` o `css/estilos.css`), ni viceversa.
3. **No romper el motor genérico:** Ningún archivo dentro de `admin/fields/` debe contener lógica específica del negocio (ej. "si el campo es trayectoria hacer X"). El motor debe ser 100% agnóstico.
4. **Seguridad y Normalización:** Respeta la lógica de CSRF y la normalización de arrays de archivos.

Si tienes dudas de cómo implementar algo en el panel, debes leer el archivo `GUIA-PANEL-ADMIN.md` antes de escribir código.
