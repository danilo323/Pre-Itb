#!/bin/sh
# Entrypoint del contenedor (Railway / Docker local).
# 1) Hace que Apache escuche en el puerto que inyecta Railway ($PORT).
# 2) Si hay un volume montado, mueve data/, img/, docs/ y audio/ dentro de el
#    y deja symlinks en su lugar para que las subidas y el JSON sobrevivan
#    a los redeploys. Las rutas de escritura del codigo estan fijas
#    (dirname(__DIR__,2) . '/img' etc.), por eso se resuelve aqui y no en PHP.
set -eu

APP=/var/www/html
PORT="${PORT:-8080}"

# ---- 1) Puerto dinamico ----------------------------------------------------
sed -i "s/^Listen 80$/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# ---- 2) Volume persistente -------------------------------------------------
# Railway exporta RAILWAY_VOLUME_MOUNT_PATH; en Docker local se puede pasar
# ITB_VOLUME_PATH. Si ninguna existe, el sitio corre en modo efimero.
VOL="${ITB_VOLUME_PATH:-${RAILWAY_VOLUME_MOUNT_PATH:-}}"

if [ -n "$VOL" ] && [ -d "$VOL" ]; then
  for d in data img docs audio; do
    src="$APP/$d"
    dst="$VOL/$d"

    # Contenedor reiniciado sin recrear: ya esta enlazado.
    if [ -L "$src" ]; then
      continue
    fi

    mkdir -p "$dst"
    if [ -d "$src" ]; then
      # Copia SIN sobrescribir (-n): la primera vez siembra el volume con lo
      # versionado (imagenes, PDFs, content.json, .htaccess); en deploys
      # siguientes solo aporta archivos nuevos del repo y nunca pisa lo que
      # el panel guardo o subio.
      cp -an "$src/." "$dst/" 2>/dev/null || true
      rm -rf "$src"
    fi
    ln -s "$dst" "$src"
    # El symlink debe pertenecer a www-data: con fs.protected_symlinks=1 el
    # kernel no deja seguir enlaces de otro dueño en directorios sticky.
    chown -h www-data:www-data "$src"
  done
  chown -R www-data:www-data "$VOL"
  echo "[itb] volume activo en $VOL (data img docs audio)"
else
  echo "[itb] sin volume: data/ img/ docs/ audio/ son efimeros (se pierden al redeployar)"
  chown -R www-data:www-data "$APP/data" "$APP/img" "$APP/docs" "$APP/audio"
fi

exec "$@"
