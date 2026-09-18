#!/bin/sh
# Entrypoint del contenedor (Railway / Docker local).
# 1) Hace que Apache escuche en el puerto que inyecta Railway ($PORT) o 80 por defecto.
# 2) Si hay un volume montado en /data-vol, mueve data/, img/, docs/, audio/ y uploads/ dentro de el
#    y deja symlinks en su lugar para que las subidas sobrevivan a los redeploys.
set -eu

APP=/var/www/html
PORT="${PORT:-80}"

# ---- 0) Un solo MPM --------------------------------------------------------
for m in mpm_event mpm_worker; do
  if [ -e "/etc/apache2/mods-enabled/$m.load" ]; then
    a2dismod -q "$m" || rm -f "/etc/apache2/mods-enabled/$m.load" "/etc/apache2/mods-enabled/$m.conf"
  fi
done
[ -e /etc/apache2/mods-enabled/mpm_prefork.load ] || a2enmod -q mpm_prefork
echo "[itb] MPM habilitado: $(ls /etc/apache2/mods-enabled/ | grep '^mpm_.*\.load$' | tr '\n' ' ')"

# ---- 1) Puerto dinamico ----------------------------------------------------
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:.*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
echo "[itb] Apache escuchara en el puerto ${PORT}"

# ---- 2) Volume persistente (si existe en Railway /data-vol) -----------------
VOL="${ITB_VOLUME_PATH:-${RAILWAY_VOLUME_MOUNT_PATH:-}}"
if [ -z "$VOL" ] && [ -d /data-vol ]; then
  VOL=/data-vol
fi

if [ -n "$VOL" ] && [ -d "$VOL" ]; then
  for d in data img docs audio uploads; do
    src="$APP/$d"
    dst="$VOL/$d"

    if [ -L "$src" ]; then
      continue
    fi

    mkdir -p "$dst"
    if [ -d "$src" ]; then
      cp -an "$src/." "$dst/" 2>/dev/null || true
      rm -rf "$src"
    fi
    ln -s "$dst" "$src"
    chown -h www-data:www-data "$src"
  done
  chown -R www-data:www-data "$VOL"
  echo "[itb] volume activo en $VOL (data img docs audio uploads)"
else
  echo "[itb] ejecutando sin volume externo"
  for d in data img docs audio uploads; do
    if [ -d "$APP/$d" ]; then
      chown -R www-data:www-data "$APP/$d" 2>/dev/null || true
    fi
  done
fi

exec "$@"
