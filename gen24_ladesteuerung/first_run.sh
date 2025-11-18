#!/usr/bin/with-contenv bashio
Gen24_Path="/opt/Gen24_Ladesteuerung"

export PYTHONPATH="$Gen24_Path"
# weatherData.sqlite
if [ ! -s "weatherData.sqlite" ]; then
    python3 -c "from FUNCTIONS.WeatherData import WeatherData; WeatherData().create_database('$Gen24_Path/weatherData.sqlite')"
fi
#PV_Daten.sqlite
if [ ! -s "PV_Daten.sqlite" ]; then
    python3 -c "from FUNCTIONS.SQLall import sqlall; sqlall().create_database_PVDaten('$Gen24_Path/PV_Daten.sqlite')"
fi
#CONFIG/Prog_Steuerung.sqlite
if [ ! -s "CONFIG/Prog_Steuerung.sqlite" ]; then
    python3 -c "from FUNCTIONS.SQLall import sqlall; sqlall().create_database_ProgSteuerung('$Gen24_Path/CONFIG/Prog_Steuerung.sqlite')"
fi

cp -n $Gen24_Path/CONFIG/charge.ini $Gen24_Path/CONFIG/charge_priv.ini
cp -n $Gen24_Path/CONFIG/default.ini $Gen24_Path/CONFIG/default_priv.ini
cp -n $Gen24_Path/CONFIG/weather.ini $Gen24_Path/CONFIG/weather_priv.ini
cp -n $Gen24_Path/CONFIG/dynprice.ini $Gen24_Path/CONFIG/dynprice_priv.ini
cp -n $Gen24_Path/html/config.ini $Gen24_Path/CONFIG/config_priv.ini

