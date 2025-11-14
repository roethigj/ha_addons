# ===== Stage 1: Go Builder =====
FROM --platform=$BUILDPLATFORM golang:1.25-alpine AS builder

RUN apk update && apk add --no-cache git make

WORKDIR /src

# Clone repository
RUN git clone https://github.com/roethigj/eebus2mqtt .

# Install dependencies
#COPY go.mod .
#COPY go.sum .
RUN go mod download

# Build binary
RUN go build -o /bin/hems-device ./devices/hems/main.go


# ===== Stage 2: Home Assistant Runtime =====
FROM alpine:3.22

RUN addgroup -S app && adduser -S app -G app

# Persistent storage (HA)
RUN mkdir -p /app && chown -R app:app /app

WORKDIR /app

COPY --from=builder /bin/hems-device /usr/local/bin/hems-device

COPY run.sh /app/run.sh
RUN chmod a+x /app/run.sh && chown app:app /app/run.sh

#USER app

RUN chmod a+x /app/run.sh
ENTRYPOINT ["/app/run.sh"]


LABEL \   io.hass.type="addon" \    io.hass.arch="aarch64|amd64|armv7"

