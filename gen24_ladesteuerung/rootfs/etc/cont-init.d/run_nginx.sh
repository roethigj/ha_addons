#!/usr/bin/with-contenv bashio
set -e
Gen24_Path="/opt/Gen24_Ladesteuerung"

CONFIG="$Gen24_Path/CONFIG/default.ini"
PRIV="$Gen24_Path/CONFIG/default_priv.ini"

FILE="$CONFIG"
[ -f "$PRIV" ] && FILE="$PRIV"

TARGET_HOST=$(grep -E '^hostNameOrIp=' "$FILE" | cut -d= -f2 | tr -d ' \r\n')

if [ -z "$TARGET_HOST" ]; then
  echo "hostNameOrIp nicht gefunden"
  exit 0
fi

sed "s|__TARGET_HOST__|$TARGET_HOST|g" \
    /etc/nginx/nginx.conf.template \
    > /etc/nginx/nginx.conf

exec nginx -g "daemon off;"

exit 0
