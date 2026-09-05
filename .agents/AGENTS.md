# Regla para Manejo de Imágenes en el Admin

Al trabajar con campos de imágenes para el panel administrativo, recuerda:
- Las imágenes **DEBEN previsualizarse por defecto** apuntando a los archivos que ya existen en el frontend (ej. `img/enfermeria.jpg`, etc.).
- Sin embargo, si un administrador **CAMBIA o ELIMINA** esa imagen (es decir, el campo queda vacío o apunta a una ruta inexistente), **NO debe mostrarse un error rojo ni la ruta técnica esperada**.
- En caso de no encontrar la imagen, simplemente debe mostrarse el placeholder estándar ("Ninguna imagen seleccionada") con el diseño por defecto, sin alertas de archivo faltante.
