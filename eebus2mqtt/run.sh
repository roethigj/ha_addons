#!/bin/sh
CONFIG_PATH="/data/options.json"
DATA_PATH="/data"

remote_SKI=$(bashio::config 'SKI')
mqttBroker=$(bashio::config 'MQTT.host')
mqttUser=$(bashio::config 'MQTT.user')
mqttPassword=$(bashio::config 'MQTT.password')
mqttPort=$(bashio::config 'MQTT.port')
inverter=$(bashio::config 'inverter_max')
echo “Testing this thing!”
# CONFIG mit Benutzereingaben erzeugen
if [ -f "$Data_Path/config.json" ]; then
  sed -i \
    -e "s/\"remoteSki\": *\"[^\"]*\"/\"remoteSki\": \"$remote_SKI\"/" \
    -e "s/\"inverter_max\": *\"[^\"]*\"/\"remoteSki\": \"$inverter\"/" \
    -e "s/\"mqttBroker\": *\"[^\"]*\"/\"mqttBroker\": \"$mqttBroker\"/" \
    -e "s/\"mqttPort\": *[0-9]*/\"mqttPort\": $mqttPort/" \
    -e "s/\"mqttUsername\": *\"[^\"]*\"/\"mqttUsername\": \"$mqttUser\"/" \
    -e "s/\"mqttPassword\": *\"[^\"]*\"/\"mqttPassword\": \"$mqttPassword\"/" \
    "$Data_Path/config.json"
fi

# Start Go application
/usr/local/bin/hems-device