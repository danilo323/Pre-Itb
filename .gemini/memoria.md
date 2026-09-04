# Memoria General del Proyecto ITB

> **Propósito:** Este archivo sirve como memoria persistente para mantener el contexto completo del desarrollo entre sesiones de chat.

---

## 📌 Datos Generales del Proyecto

- **Nombre:** Instituto Superior Tecnológico Bolivariano de Tecnología (ITB) - Landing Page & Sitio Web
- **Repositorio Git:** `https://github.com/danilo323/Pre-Itb.git`
- **Rama Actual:** `feat/frontend-landing`
- **Tecnologías Utilizadas:**
  - **Backend/Templating:** PHP modular (inclusión de secciones mediante `include 'includes/*.php'`)
  - **Estilos:** Vanilla CSS (`css/styles.css`) con variables HSL, fuentes de Google Fonts (Inter y Playfair Display), responsive design y efectos glassmorphism/micro-animaciones.
  - **Iconos:** FontAwesome 6.5.1 CDN
  - **Lógica Frontend:** JavaScript (`js/main.js`)
  - **Estructura HTML:** HTML5 semántico en `index.php`

---

## 🛠️ Estructura del Código

- **`index.php`**: Archivo principal que ensambla la landing page.
- **`includes/`**: Componentes modulares reutilizables:
  - `header.php`: Barra de navegación superior, logotipo ITB, menú principal y responsive hamburger menu.
  - `hero.php`: Sección principal banner con CTA, título destacado y métricas/rating.
  - `trayectoria.php`: Cifras institucionales y trayectoria.
  - `areas.php`: Áreas de estudio/carreras.
  - `programas.php`: Oferta académica detallada.
  - `experiencia.php`: Metodología, campus y tecnología.
  - `testimonios.php`: Reseñas de estudiantes y egresados.
  - `autoridades.php`: Liderazgo y cuerpo directivo.
  - `servicios.php`: Servicios estudiantiles y bienestar.
  - `admision.php`: Pasos de admisión y llamada a la acción.
  - `noticias.php`: Sección de novedades y blog.
  - `footer.php`: Pie de página con enlaces y derechos.
- **`css/styles.css`**: Sistema de diseño completo con paleta institucional, tipografías y animación.
- **`js/main.js`**: Interacciones JavaScript.

---

## 📜 Historial de Avances y Conversaciones

### 1. Desarrollo del Frontend de la Landing Page
- Creación de la estructura modular PHP (`index.php` + 12 vistas parciales en `includes/`).
- Creación y afinación del diseño CSS (`css/styles.css`) moderno y dinámico.
- Inserción de assets e imágenes (`img/hero-bg.jpg`).

### 2. Control de Versiones (Git)
- Todos los cambios de la landing page fueron añadidos (`git add .`), confirmados (`git commit`) y subidos exitosamente a GitHub (`git push origin feat/frontend-landing`).
- Commit hash: `f596e80` ("Implementación del diseño landing page ITB con componentes php y estilos css").

### 3. Configuración del Sistema de Memoria (.gemini)
- Creación del directorio `.gemini/` para guardar el historial de contexto y memoria persistente.
- Creación de `GEMINI.md` y `.agents/rules/memoria.md` para garantizar que en cada nueva sesión el agente lea automáticamente este archivo y no olvide las conversaciones.

### 4. Integración de Imagen de Fondo Hero y Corrección de Texto Circular
- Vinculación de la nueva imagen institucional `img/salud.jpg` en la sección Hero (`includes/hero.php`).
- Restauración de los contenedores `.hero__bg` y `.hero__overlay` para solucionar la pantalla blanca.
- Ajuste del badge de video con la frase única `• EST. 1995 • ITB INSTITUTO UNIVERSITARIO ` distribuida a lo largo de un radio SVG optimizado (`r=55px`, `textLength="345"`) para que dé la vuelta de 360° sin necesidad de repetirla y se lea con total claridad.

### 5. Ajustes de UI en Navbar y Logo (Estructura Unificada)
- Reestructuración completa del HTML en `header.php` para envolver la `.top-bar` y `.navbar` en un contenedor maestro `.header-main`.
- La cabecera ahora es un único "card" flotante blanco con bordes inferiores redondeados (`border-radius: 25px`), tal como el diseño de referencia.
- El logo (`.header-main__logo`) ocupa todo el alto de la izquierda, y las barras de navegación se apilan a la derecha.
- Incremento del tamaño del logo (`height: 85px`) y ajuste de la altura de la cabecera (`--header-height: 105px`).

### 6. Slideshow Dinámico y Video Modal
- Se reemplazó el fondo estático del hero por un slideshow de 3 imágenes rotativas (`salud.jpg`, `student.jpg`, `student 2.jpg`).
- Se añadió efecto **Ken Burns** con transiciones CSS "slide-up" (desde abajo hacia arriba) automáticas de 7 segundos.
- Se implementó una **Ventana Modal** en `hero.php` y `main.js` para visualizar un video de YouTube sin salir de la página principal.

---

## 🎯 Próximos Pasos y Tareas Pendientes

- [ ] Continuar refinando o agregando nuevas secciones según solicitud del usuario.
- [ ] Conectar formularios de contacto/admisión a backend/base de datos si es necesario.
- [ ] Optimizar imágenes y rendimiento SEO adicional.
