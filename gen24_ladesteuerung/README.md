
# Home Assistant Add-on: GEN24 Ladesteuerung

**Inoffizielles Wrapper-Add-on** für das Open-Source-Projekt [wiggal/GEN24_Ladesteuerung](https://github.com/wiggal/GEN24_Ladesteuerung).
Das Add-on klont das Original-Repository zur Laufzeit in den Container, schreibt minimale `*_priv.ini`-Dateien und startet
die Steuerung zyklisch. Optional stellt es die WebUI über Port `2424` bereit (PHP built-in server).

> Hinweis: Das Projekt selbst steht unter GPL-3.0; dieses Add-on verteilt keinen Quellcode des Projekts, sondern klont es zur Laufzeit.

## Installation (lokales Repository)
1. Im Add-on-Store rechts oben **⋮ → Repositories** wählen und dieses Verzeichnis Repository hinzufügen (https://github.com/roethigj/ha_addons.git).
2. Add-on installieren und konfigurieren. Hier hier auch die Hinweise von Wiggal beachten (z.B. falls % im Passwort enthalten ist, %% eingeben).
3. Die Konfiguration des Addons überschreibt die Konfigurationen in den _priv.ini Dateien.
4. Die Deitails in den  _priv. ini Dateien einstellen.
5. Starten.
6. WebUI: `http://homeassistant:2424/`, oder zu Seitenleiste hinzufügen.

## Optionen
- `fronius.host`, `fronius.user`, `fronius.password` – Zugangsdaten zum GEN24.
- `php_webserver` (bool) – Einfachen PHP-Server für die WebUI starten (Default: true).
- `scheduler_interval_minutes` – Intervall für die Ladesteuerung (Default: 5).
- `dynamic_price_check` – Stündliche Ausführung von `DynamicPriceCheck.py` (Default: false).
- Auswahl der Wetterdaten-Anbieter

## Ports
- 2424/tcp – WebUI (falls aktiviert).

## Datenpersistenz
- Add-on verwendet `/data` (Container) und mappt **config/share**. Die Projektdateien werden nach `/opt/GEN24_Ladesteuerung` geklont.

## Haftungsausschluss
Dieses Add-on ist eine Community-Hülse ohne Garantie. Für Funktionsumfang, Stabilität und Konfiguration verweisen wir auf das Originalprojekt und dessen Wiki.
