ARG BUILD_FROM
FROM ${BUILD_FROM}

RUN \
    apk add --no-cache \
    bash git python3 py3-pip \
    php83 php83-sqlite3 \
    busybox-suid curl coreutils file iputils-ping py3-requests nginx

ENV GEN24_DIR=/opt/Gen24_Ladesteuerung

COPY rootfs /
RUN url=$(curl -s https://api.github.com/repos/wiggal/GEN24_Ladesteuerung/releases/latest \
    | jq -r '[.assets[0].browser_download_url,.zipball_url] | map(select(.!=null and .!=""))[0]') && curl -L -N -o /tmp/gen24.zip "$url" \
    && unzip -o /tmp/gen24.zip -d /tmp \
    && rm -rf /opt/Gen24_Ladesteuerung && mv /tmp/wiggal-GEN24_Ladesteuerung-* /opt/Gen24_Ladesteuerung

COPY first_run.sh /opt/Gen24_Ladesteuerung/
COPY cron_draft /opt/Gen24_Ladesteuerung/
COPY run_nginx.sh /etc/cont-init.d/
RUN rm -f /opt/Gen24_Ladesteuerung/html/6_tab_GEN24.php
COPY 6_tab_GEN24.php /opt/Gen24_Ladesteuerung/html/
COPY nginx.conf.template /etc/nginx/http.d/gen24.conf

RUN chmod a+x /opt/Gen24_Ladesteuerung/start_PythonScript.sh 
RUN chmod a+x /opt/Gen24_Ladesteuerung/first_run.sh 
RUN chmod a+x /etc/cont-init.d/run.sh
RUN chmod a+x /etc/cont-init.d/run_nginx.sh

RUN ln -s /usr/bin/php83 /usr/bin/php


LABEL \    io.hass.name="GEN24 Ladesteuerung" \    io.hass.description="Wrapper-Add-on für wiggal/GEN24_Ladesteuerung" \    io.hass.type="addon" \    io.hass.arch="aarch64|amd64|armv7"



