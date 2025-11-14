
# Achtung!!!!
** frühes Entwicklungsstadium, nur für Testzwecke. Vor Version 0.1 nicht installieren!


# Home Assistant Add-on: eebus2mqtt

**Erster Entwurf, weiter Informationen folgen.
Ziel: Die eebus-Signale einer Steuerbox verarbeiten und über MQTT bereitstellen.

> Hinweis: Das Projekt selbst steht unter GPL-3.0; dieses Add-on verteilt keinen Quellcode des Projekts, sondern klont es zur Laufzeit.

## Installation (lokales Repository)
1. Im Add-on-Store rechts oben **⋮ → Repositories** wählen und dieses Verzeichnis Repository hinzufügen (https://github.com/roethigj/ha_addons.git).
2. Add-on installieren und konfigurieren.

## Optionen
- ski der Steuerbox
- MQTT broker, Port, User und Passwort
- Nennleistung des Wechselrichters (Nominal Max, z.Bsp. 10000 für 10 kW)

## über MQTT werden ausgegeben:
- Status (init, limited, unlimitedcontrolled, failsafe)
- erlaubte Leistung am Netzanschlusspunkt (limited oder failsafe)
- Zeit seit letztem Kontakt zur Steuerbox (heartbeat)
- Restdauer failsafe

## Datenpersistenz
- Add-on verwendet `/data` (Container) und mappt **config/share**. 

## Haftungsausschluss
Dieses Add-on ist eine Community-Projekt ohne Garantie.
