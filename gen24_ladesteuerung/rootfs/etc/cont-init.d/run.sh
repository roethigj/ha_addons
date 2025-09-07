#!/usr/bin/with-contenv bashio
Gen24_Path="/opt/Gen24_Ladesteuerung"

ip_adresse=$(bashio::config 'fronius.host')
kennwort=$(bashio::config 'fronius.password')
user=$(bashio::config 'fronius.user')
battery_capacity_wh=$(bashio::config 'fronius.battery_capacity_wh')

if $(bashio::config 'php_webserver'); then
    webserver=1
else
    webserver=0
fi
    
# Test erster start
CHECKFILE="/data/first.flag"
if [ ! -f "$CHECKFILE" ]; then
    echo "Datei '$CHECKFILE' nicht gefunden. Starte first_run.sh..."
    $Gen24_Path/first_run.sh
    touch "$CHECKFILE"
    echo "Datei '$CHECKFILE' wurde erstellt."
else
    echo "Datei '$CHECKFILE' existiert bereits. Keine Aktion erforderlich."
fi

# Pfade für ingress anpassen
if [ -f "$Gen24_Path/html/config.php" ]; then
    sed -i -e "s#^\$PythonDIR *= *.*#\$PythonDIR = '$Gen24_Path';#" \
    $Gen24_Path/html/config.php
fi

find "$Gen24_Path/html" -type f -name "*.php" -print0 | while IFS= read -r -d '' file; do
    sed -i 's#'\''.\$_SERVER\["PHP_SELF"\].'\''##g' "$file"
    sed -i "s#urlencode(\$_SERVER\['REQUEST_URI'\])#substr(\$_SERVER['REQUEST_URI'], 1)#g" "$file"
done

# Import für python anpassen
find "$Gen24_Path/FUNCTIONS" -type f -name "*.py" -print0 | while IFS= read -r -d '' file; do
    sed -i 's#'\''FUNCTIONS.'\''##g' "$file"
done
find "$Gen24_Path/ADDONS" -type f -name "*.py" -print0 | while IFS= read -r -d '' file; do
    sed -i 's#'\''FUNCTIONS.'\''##g' "$file"
done

# Verlinke Datenbanken und configs
if [ ! -f /data/PV_Daten.sqlite ]; then
    mv $Gen24_Path/PV_Daten.sqlite /data
    ln -s /data/PV_Daten.sqlite $Gen24_Path/
else
    ln -s /data/PV_Daten.sqlite $Gen24_Path/
fi
if [ ! -f /data/weatherData.sqlite ]; then
    mv $Gen24_Path/weatherData.sqlite /data
    ln -s /data/weatherData.sqlite $Gen24_Path/
else
    ln -s /data/weatherData.sqlite $Gen24_Path/
fi
if [ ! -f /data/Prog_Steuerung.sqlite ]; then
    mv $Gen24_Path/CONFIG/Prog_Steuerung.sqlite /data
    ln -s /data/Prog_Steuerung.sqlite $Gen24_Path/CONFIG/
else
    ln -s /data/Prog_Steuerung.sqlite $Gen24_Path/CONFIG/
fi

if [ $(bashio::config 'dynamic_price_check')=true ]; then
    if [ ! -f /data/dynprice_priv.ini ]; then
        if [ ! -f $Gen24_Path/CONFIG/dynprice_priv.ini ]; then
            cp $Gen24_Path/CONFIG/dynprice.ini /data/dynprice_priv.ini
            ln -s /data/dynprice_priv.ini $Gen24_Path/CONFIG/dynprice_priv.ini
        else
            mv $Gen24_Path/CONFIG/dynprice_priv.ini /data/dynprice_priv.ini
            ln -s /data/dynprice_priv.ini $Gen24_Path/CONFIG/dynprice_priv.ini
        fi
    else
        if [ ! -f $Gen24_Path/CONFIG/dynprice_priv.ini ]; then
            ln -s /data/dynprice_priv.ini $Gen24_Path/CONFIG/dynprice_priv.ini
        else
            rm $Gen24_Path/CONFIG/dynprice_priv.ini 
            ln -s /data/dynprice_priv.ini $Gen24_Path/CONFIG/dynprice_priv.ini
        fi
    fi
fi


find "$Gen24_Path/CONFIG" -type f -name "*_priv.ini" -print0 | while IFS= read -r -d '' filepath; do
    file=$(basename "$filepath")   # nur Dateiname ohne Pfad
    if [ ! -f "/data/$file" ]; then
        mv "$filepath" "/data/"
    else
        rm "$filepath" 
    fi
done

find "/data/" -type f -name "*_priv.ini" -print0 | while IFS= read -r -d '' filepath; do
    file=$(basename "$filepath")   # nur Dateiname ohne Pfad
    ln -s "/data/$file" "$Gen24_Path/CONFIG/"
done

# CONFIG/default_priv.ini mit Benutzereingaben erzeugen

if [ -f "$Gen24_Path/CONFIG/default_priv.ini" ]; then
    sed -e "s/^hostNameOrIp *= *.*/hostNameOrIp = $ip_adresse/" \
        -e "s/^password *= *.*/password = '$kennwort'/" \
        -e "s/^user *= *.*/user = $user/" \
        -e "s/^battery_capacity_Wh *= *.*/battery_capacity_Wh = $battery_capacity_wh/" \
        -e "s/^Einfacher_PHP_Webserver *= *.*/Einfacher_PHP_Webserver = $webserver/" \
        $Gen24_Path/CONFIG/default.ini > $Gen24_Path/CONFIG/default_priv.ini
fi

if [ ! -f /data/Crontab.log ]; then
    touch /data/Crontab.log
    ln -s /data/Crontab.log $Gen24_Path/
else
    ln -s /data/Crontab.log $Gen24_Path/
fi

#Crontab
forecast_solar=$(bashio::config 'forecast_solar')
solcast=$(bashio::config 'solcast')

c_int=$(bashio::config 'scheduler_interval_minutes')
if [ -f "$Gen24_Path/cron_draft" ]; then
    if [ -f "$Gen24_Path/cron_file" ]; then
        rm $Gen24_Path/cron_file
        cp $Gen24_Path/cron_draft $Gen24_Path/cron_file
    else
        cp $Gen24_Path/cron_draft $Gen24_Path/cron_file
    fi
    if "$(bashio::config 'akkudoktor_weather')"; then
        sed -i "s*#akku**g" "$Gen24_Path/cron_file"
    fi
    if "$(bashio::config 'forecast_solar')"; then
        sed -i "s*#forecast**g" "$Gen24_Path/cron_file"
    fi
    if "$(bashio::config 'solcast')"; then
        sed -i "s*#solcast**g" "$Gen24_Path/cron_file"
    fi
    if "$(bashio::config 'solcast_homeassistant')"; then
        sed -i "s*#sol_ha**g" "$Gen24_Path/cron_file"
    fi
    if "$(bashio::config 'openmeteo')"; then
        sed -i "s*#open**g" "$Gen24_Path/cron_file"
    fi
    if "$(bashio::config 'solarprognose')"; then
        sed -i "s*#sol_prog**g" "$Gen24_Path/cron_file"
    fi
    if "$(bashio::config 'dynamic_price_check')"; then
        sed -i "s*#dyn_price**g" "$Gen24_Path/cron_file"
    fi
    sed -i "s#c_int#$c_int#g" "$Gen24_Path/cron_file"
    sed -i "s#REPO_DIR#$Gen24_Path#g" "$Gen24_Path/cron_file"

fi
crontab $Gen24_Path/cron_file

#if [ ! "$kennwort" = "password" ]; then
#    $Gen24_Path/start_PythonScript.sh http_SymoGen24Controller2.py
#fi

$Gen24_Path/start_PythonScript.sh http_SymoGen24Controller2.py 2>&1

crond

exit 0
