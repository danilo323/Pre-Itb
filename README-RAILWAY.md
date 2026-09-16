# Entorno de prueba en Railway (rama `deploy/railway`)

Esta rama contiene todo lo necesario para levantar el sitio ITB en Railway como
**entorno de prueba**, sin tocar `main` ni las ramas `feat/*`.

## Qué añade esta rama respecto a `main`

| Archivo | Para qué |
|---|---|
| `Dockerfile`, `.dockerignore` | Imagen `php:8.2-apache` con `gd` y `pdo_mysql`. Excluye `test/`, `admin/test_motor.php`, `cookies.txt` y datos locales. |
| `docker/apache-vhost.conf` | `AllowOverride All` para que apliquen los `.htaccess` del proyecto (rutas limpias, 404, bloqueo de `data/`). |
| `docker/php.ini` | Subidas de 32 MB, errores al log en vez de a la página. |
| `docker/entrypoint.sh` | Usa el puerto que inyecta Railway y enlaza `data/`, `img/`, `docs/`, `audio/` al volume persistente. |
| `railway.json` | Builder Dockerfile, 1 réplica, healthcheck en `/`. |
| `admin/config.php` | Lee credenciales y MySQL desde variables de entorno, con fallback a los valores locales. **Este cambio sí es seguro de mergear a `main`**: en XAMPP funciona igual sin definir nada. |

El resto de archivos de esta rama son solo de despliegue; no hace falta mergearlos a `main`.

## Paso a paso en Railway

1. **Crear el proyecto**: Railway → *New Project* → *Deploy from GitHub repo* → `farevaloc/web_itb`.
2. **Apuntar a esta rama**: en el servicio web → *Settings* → *Source* → *Branch* → `deploy/railway`.
   Railway detecta `railway.json` y construye con el `Dockerfile`.
3. **Añadir MySQL**: en el proyecto → *+ New* → *Database* → *MySQL*.
4. **Variables del servicio web** (*Variables* → *Raw Editor*):

   ```
   MYSQLHOST=${{MySQL.MYSQLHOST}}
   MYSQLPORT=${{MySQL.MYSQLPORT}}
   MYSQLDATABASE=${{MySQL.MYSQLDATABASE}}
   MYSQLUSER=${{MySQL.MYSQLUSER}}
   MYSQLPASSWORD=${{MySQL.MYSQLPASSWORD}}
   ADMIN_USER=admin
   ADMIN_HASH=<hash bcrypt, ver abajo>
   PORT=8080
   ```

   `${{MySQL.X}}` son referencias al servicio MySQL; Railway las resuelve solo.
   Las tablas `site_content` y `form_registros` se crean solas en el primer uso.
5. **Generar `ADMIN_HASH`** (en cualquier máquina con PHP, o dentro del contenedor):

   ```
   php -r "echo password_hash('TuClaveSegura', PASSWORD_DEFAULT);"
   ```

   Pegar el resultado tal cual en Railway (empieza por `$2y$`). Si no se define,
   el panel usa la demo `admin` / `1234`. **No dejar la demo en un dominio público.**
6. **Volume**: servicio web → *Settings* → *Volumes* → *Add Volume* → mount path `/data-vol`.
   En el primer arranque el entrypoint copia ahí las imágenes, PDFs, audio y `data/content.json`.
7. **Dominio**: *Settings* → *Networking* → *Generate Domain* → puerto `8080`.
8. **Deploy** y revisar los logs: debe aparecer `[itb] volume activo en /data-vol`.

## Verificación tras el deploy

| URL | Esperado |
|---|---|
| `/` | 200, landing con imágenes |
| `/admin` | redirige al login |
| `/admin/login` | login con `ADMIN_USER` / la clave del hash |
| `/sobre-nosotros` | 200 |
| `/data/content.json` | **403** |
| `/test/test_session.php`, `/admin/test_motor.php`, `/cookies.txt` | **404** |
| `/pagina-que-no-existe` | 404 con estilos |
| Panel → subir imagen de 2 a 32 MB | se acepta y se ve en `/img/<hash>.jpg` |
| Panel → crear página con slug `prueba-railway` | `/prueba-railway` responde 200 |
| Formulario de admisión | aparece en `/admin/registros.php` |
| Cambiar algo en el panel → *Redeploy* | el cambio y las subidas siguen ahí |

## Probar la imagen en local con Docker

```bash
docker build -t itb-web .

# Solo sitio (sin BD ni volume, todo efímero)
docker run --rm -p 8080:8080 itb-web

# Con MySQL de XAMPP en el host y volume simulado
docker run --rm -p 8080:8080 \
  -v itb_vol:/data-vol -e ITB_VOLUME_PATH=/data-vol \
  -e MYSQLHOST=host.docker.internal -e MYSQLDATABASE=itb_admin \
  -e MYSQLUSER=root -e MYSQLPASSWORD=123456789 \
  itb-web
```

Abrir `http://localhost:8080`.

## Limitaciones conocidas (entorno de prueba)

- **Sesiones en el contenedor**: cada redeploy cierra la sesión del panel. Basta volver a entrar.
- **Una sola réplica**: el login y el CSRF usan sesiones en archivo. No escalar horizontalmente.
- **Imágenes versionadas con el mismo nombre**: el volume nunca se sobrescribe. Si en git se
  reemplaza `img/foto.jpg` por otra con el mismo nombre, Railway seguirá mostrando la antigua.
  Solución: subirla desde el panel o borrarla del volume.
- **Credenciales antiguas en el historial de git**: `admin/config.php`, `cookies.txt` y
  `data/registros.json` estuvieron versionados en `main`. Definir `ADMIN_HASH` y usar el
  MySQL de Railway evita usarlas aquí; limpiar el historial es una tarea aparte.
- **Merge de `main` → `deploy/railway`**: normalmente limpio. `cookies.txt` dejó de trackearse
  aquí; si `main` lo modifica, resolver con `git rm cookies.txt`.
