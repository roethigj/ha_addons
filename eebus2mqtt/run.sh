#!/usr/bin/with-contenv bashio
DATA_PATH="/config"

remote_SKI=$(bashio::config 'SKI')
mqttBroker=$(bashio::config 'MQTT.host')
mqttUser=$(bashio::config 'MQTT.user')
mqttPassword=$(bashio::config 'MQTT.password')
mqttPort=$(bashio::config 'MQTT.port')
pv_max=$(bashio::config 'pv_max')
# copy config, if nothing there
cp -n /config.draft $DATA_PATH/config.json

# CONFIG mit Benutzereingaben erzeugen
if [ -f "$DATA_PATH/config.json" ]; then
  sed -i \
    -e "s/\"remoteSki\": *\"[^\"]*\"/\"remoteSki\": \"$remote_SKI\"/" \
    -e "s/\"pv_max\": *[0-9]*/\"pv_max\": \"$pv_max\"/" \
    -e "s/\"mqttBroker\": *\"[^\"]*\"/\"mqttBroker\": \"$mqttBroker\"/" \
    -e "s/\"mqttPort\": *[0-9]*/\"mqttPort\": $mqttPort/" \
    -e "s/\"mqttUsername\": *\"[^\"]*\"/\"mqttUsername\": \"$mqttUser\"/" \
    -e "s/\"mqttPassword\": *\"[^\"]*\"/\"mqttPassword\": \"$mqttPassword\"/" \
    "$DATA_PATH/config.json"
fi

# Start Go application
/usr/local/bin/hems-device
