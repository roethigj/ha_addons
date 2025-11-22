ARG BUILD_FROM="alpine"
FROM $BUILD_FROM
# ===== Stage 1: Go Builder =====
FROM --platform=$BUILDPLATFORM alpine:3.22 AS builder

#RUN echo $BUILD_FROM
#RUN echo $BUILDPLATFORM
#RUN echo $BUILD_ARCH

#RUN apk update && apk add --no-cache git make

WORKDIR /src
# Clone repository
#RUN git clone https://github.com/roethigj/eebus2mqtt .
#RUN rm -rf /src/*
#RUN git clone --no-hardlinks https://github.com/roethigj/eebus2mqtt .
#RUN git pull

# Install dependencies
#COPY go.mod .
#COPY go.sum .RUN git pull https://github.com/roethigj/eebus2mqtt

#RUN go mod download

# Build binary

#RUN go build -o /bin/hems-device ./devices/hems/main.go

RUN echo "$BUILD_ARCH"
RUN \
  #if [ "${BUILD_ARCH}" = "arm64" ]; then  \
  wget https://github.com/roethigj/eebus2mqtt/tree/main/build/hems_aarch64 && \
  cp hems_aarch64 /bin/hems-device;
  #\
  #elif [ "${BUILD_ARCH}" = "amd64" ]; then  \
  #wget https://github.com/roethigj/eebus2mqtt/tree/main/build/hems_amd64 && \
  #cp hems_amd64 /bin/hems-device; \
  #fi

# ===== Stage 2: Home Assistant Runtime =====
FROM ghcr.io/hassio-addons/base:18.1.4

RUN addgroup -S app && adduser -S app -G app


# Persistent storage (HA)
RUN mkdir -p /app && chown -R app:app /app
RUN mkdir -p /config && chown -R app:app /config
# VOLUME [ "/data", "/config" ]
WORKDIR /app

COPY --from=builder /bin/hems-device /usr/local/bin/hems-device

COPY run.sh /
RUN chmod a+x /run.sh

COPY config.draft /config.draft
# EEBus
EXPOSE 4713/tcp

CMD ["/run.sh"]

LABEL \   io.hass.type="addon" \    io.hass.arch="aarch64|amd64|armv7"
