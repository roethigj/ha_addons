#!/usr/bin/with-contenv bashio
set -e
Gen24_Path="/opt/Gen24_Ladesteuerung"
CONF_FILE="/etc/nginx/http.d/gen24.conf"

CONFIG="$Gen24_Path/CONFIG/default.ini"
PRIV="$Gen24_Path/CONFIG/default_priv.ini"

FILE="$CONFIG"
[ -f "$PRIV" ] && FILE="$PRIV"

TARGET_HOST=$(grep -E '^hostNameOrIp=' "$FILE" | cut -d= -f2 | tr -d ' \r\n')

if [ -z "$TARGET_HOST" ]; then
  echo "hostNameOrIp nicht gefunden"
  exit 1
fi

sed -i "s|__TARGET_HOST__|$TARGET_HOST|g" "$CONF_FILE"

exec nginx

exit 0
