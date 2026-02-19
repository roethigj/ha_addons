ARG BUILD_FROM
FROM php:8.3-cli-alpine

RUN \
    apk add --no-cache \
    bash git python3 py3-pip \
    php83 php83-sqlite3 php83-openssl \
    busybox-suid curl php83-curl coreutils file iputils-ping py3-requests jq unzip

ENV GEN24_DIR=/opt/Gen24_Ladesteuerung

COPY rootfs/etc/cont-init.d/run.sh /
RUN url=$(curl -s https://api.github.com/repos/wiggal/GEN24_Ladesteuerung/releases/latest \
    | jq -r '[.assets[0].browser_download_url,.zipball_url] | map(select(.!=null and .!=""))[0]') && curl -L -N -o /tmp/gen24.zip "$url" \
    && unzip -o /tmp/gen24.zip -d /tmp \
    && rm -rf /opt/Gen24_Ladesteuerung && mv /tmp/wiggal-GEN24_Ladesteuerung-* /opt/Gen24_Ladesteuerung

COPY first_run.sh /opt/Gen24_Ladesteuerung/
COPY cron_draft /opt/Gen24_Ladesteuerung/
RUN rm -f /opt/Gen24_Ladesteuerung/html/6_tab_GEN24.php
COPY 6_tab_GEN24.php /opt/Gen24_Ladesteuerung/html/

RUN chmod a+x /opt/Gen24_Ladesteuerung/start_PythonScript.sh 
RUN chmod a+x /opt/Gen24_Ladesteuerung/first_run.sh 
# RUN chmod a+x /etc/cont-init.d/run.sh
RUN chmod a+x /run.sh

RUN ln -s /usr/bin/php83 /usr/bin/php

CMD ["run.sh"]


LABEL \    io.hass.name="GEN24 Ladesteuerung" \    io.hass.description="Wrapper-Add-on für wiggal/GEN24_Ladesteuerung" \    io.hass.type="addon" \    io.hass.arch="aarch64|amd64|armv7"



