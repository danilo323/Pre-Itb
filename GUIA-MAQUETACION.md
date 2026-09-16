# Guía de maquetación ITB

Reglas para que las secciones se puedan reutilizar entre páginas desde el panel.
Una sección debe verse **igual la pongas donde la pongas**.

Todo lo que se describe aquí ya está disponible: vive en [css/sistema.css](css/sistema.css),
que se carga en todas las páginas desde `css/styles.css`.

---

## 1. Ancho del contenido: usa `.container`

```html
<div class="mi-seccion__container container">                    <!-- 1280px, el estándar -->
<div class="mi-seccion__container container container--wide">    <!-- 1440px, para secciones anchas -->
<div class="mi-seccion__container container container--narrow">  <!-- 900px, para texto largo -->
```

Conserva tu clase propia y **añade** `container`. Luego borra de tu CSS el `max-width`,
el `margin: 0 auto` y el `padding: 0 24px`.

**Por qué:** había 21 contenedores duplicados con seis anchos distintos (950, 1280, 1350,
1400, 1440 y 1500). Los bordes del contenido no quedaban alineados entre una sección y la
siguiente.

## 2. Espaciado vertical: usa `.section`

```html
<section class="mi-seccion section">              <!-- 96px arriba y abajo -->
<section class="mi-seccion section section--md">  <!-- 80px -->
<section class="mi-seccion section section--sm">  <!-- 48px -->
```

**No escribas media queries para reducir el espaciado en móvil.** Ya se reduce solo:
96 → 72 → 56 → 44 según el ancho de pantalla.

Si necesitas quitar el espaciado por un lado, `section--flush-top` o `section--flush-bottom`.

**Por qué:** había once valores distintos de relleno vertical, y ocho de las once secciones
de la portada conservaban sus 100px en un teléfono de 360px.

## 3. Separaciones: solo valores de la escala

| Token | Valor |
|---|---|
| `--space-2xs` | 8px |
| `--space-xs` | 12px |
| `--space-sm` | 16px |
| `--space-md` | 24px |
| `--space-lg` | 32px |
| `--space-xl` | 48px |
| `--space-2xl` | 64px |
| `--space-3xl` | 80px |

```css
.mi-seccion__grid { gap: var(--gap-md); }
.mi-seccion__titulo { margin-bottom: var(--space-lg); }
```

Nada de 13px, 18px ni 22px. Si dudas, redondea al múltiplo de 8 más cercano.

## 4. Nunca prefijes con la página

```css
.page-oferta .mi-tarjeta { padding: 20px; }   /* ❌ la sección ya no sirve en otra página */
.mi-tarjeta { padding: var(--space-sm); }     /* ✅ */
```

Si una página necesita otro ritmo, cambia **una variable**, no un selector:

```html
<body class="densidad-compacta">   <!-- o densidad-amplia -->
```

**Por qué:** cuatro secciones solo funcionan hoy si el cuerpo lleva la clase de Sobre
Nosotros. Por eso las páginas creadas desde el panel se ven obligadas a declarar una clase
que no les corresponde, y heredan un espaciado que nadie pidió para ellas.

## 5. Puntos de ruptura: solo estos cuatro

```
1200px   992px   768px   480px
```

Los de 1380 y 1150 son **exclusivos de la cabecera**. No los uses fuera de `header.css`
y `responsive.css`. No inventes 1024, 900 ni 640.

**Por qué:** hay doce puntos de ruptura distintos en dos convenciones enfrentadas, así que
entre 1151 y 1200 el diseño se rompe a trozos.

## 6. Nada de `!important`

Si lo necesitas, o el selector está mal o estás peleando con `hover-theme.css`. Avísalo
en vez de forzarlo.

## 7. Textos y enlaces editables

Todo texto visible sale del panel:

```php
<?= content_get($seccion, 'titulo', 'Título por defecto') ?>
```

`content_get()` **ya escapa**. No lo envuelvas en `htmlspecialchars()`: el texto saldría
corrupto en cuanto alguien escriba un ampersand.

Para direcciones, dentro de `href` o `src`, usa siempre:

```php
<a href="<?= content_url($seccion, 'cta_url') ?>">
```

Nunca `content_raw()` dentro de un atributo: permite inyectar código desde el panel.

**Convención:** cada texto editable `foo_texto` lleva su pareja `foo_url`. Si la dirección
está vacía, no pintes el botón. Hoy hay 37 enlaces fijos que el administrador no puede
cambiar.

## 8. Tu sección nueva, para que el panel pueda reutilizarla

Cuatro piezas:

1. `includes/<nombre>.php` con el HTML.
2. `css/<nombre>.css` con sus estilos, e importado desde `css/styles.css`.
3. La sección declarada en `admin/schema_mock.php` con sus campos.
4. Una línea en el mapa de secciones de `pagina_dinamica.php`.

**Si olvidas la cuarta, la sección se puede marcar y editar en el panel pero no aparece en
la web, sin ningún aviso.**

En la primera línea del componente, consulta la visibilidad con la clave de la sección:

```php
<?php
if (!function_exists('is_visible')) require_once __DIR__ . '/content_helper.php';
if (!is_visible('mi_seccion')) return;
?>
```

## 9. Variables nuevas van a `css/sistema.css`

El bloque de variables de `css/base.css` está congelado: es de los archivos que más gente
toca y cada cambio ahí genera conflictos. Los tokens nuevos van en `sistema.css`.
