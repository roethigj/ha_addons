# ===== Stage 1: Build binary =====
ARG BUILD_FROM=golang:1.21-alpine

RUN apk add --no-cache git

WORKDIR /src

# Clone the repository
RUN git clone https://github.com/roethigj/eebus2mqtt .

# Download go dependencies
RUN go mod download

# Build the binary from devices/hems/main.go
RUN go build -o /bin/hems-device ./devices/hems/main.go


# ===== Stage 2: Home Assistant Runtime =====
FROM alpine:3.19
FROM ${BUILD_FROM}


# Create non-root user
RUN addgroup -S app && adduser -S app -G app

# Home Assistant persistent storage
RUN mkdir -p /data && chown -R app:app /data

# Use /data as working directory → relative paths work correctly
WORKDIR /data

# Copy the compiled executable
COPY --from=builder /bin/hems-device /usr/local/bin/hems-device

USER app

# Start application (which will create ./config.json & ./status.log)
ENTRYPOINT ["/run.sh"]
