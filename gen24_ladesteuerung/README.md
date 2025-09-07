
# Home Assistant Add-on: GEN24 Ladesteuerung

**Inoffizielles Wrapper-Add-on** für das Open-Source-Projekt [wiggal/GEN24_Ladesteuerung](https://github.com/wiggal/GEN24_Ladesteuerung).
Das Add-on klont das Original-Repository zur Laufzeit in den Container, schreibt minimale `*_priv.ini`-Dateien und startet
die Steuerung zyklisch. Optional stellt es die WebUI über Port `2424` bereit (PHP built-in server).

> Hinweis: Das Projekt selbst steht unter GPL-3.0; dieses Add-on verteilt keinen Quellcode des Projekts, sondern klont es zur Laufzeit.

## Installation (lokales Repository)
1. Im Add-on-Store rechts oben **⋮ → Repositories** wählen und dieses Verzeichnis als lokales Repository hinzufügen (ZIP entpacken nach `/addons/gen24_ladesteuerung/`).
2. Add-on installieren und konfigurieren.
3. Starten. WebUI: `http://homeassistant:2424/`

## Optionen
- `fronius.host`, `fronius.user`, `fronius.password` – Zugangsdaten zum GEN24.
- `php_webserver` (bool) – Einfachen PHP-Server für die WebUI starten (Default: true).
- `scheduler_interval_minutes` – Intervall für die Ladesteuerung (Default: 5).
- `dynamic_price_check` – Stündliche Ausführung von `DynamicPriceCheck.py` (Default: false).
- `repo_url`, `repo_branch` – Quelle des Projekts (zum Pinnen einer Version/Branch).

## Ports
- 2424/tcp – WebUI (falls aktiviert).

## Datenpersistenz
- Add-on verwendet `/data` (Container) und mappt **config/share**. Die Projektdateien werden nach `/opt/GEN24_Ladesteuerung` geklont.

## Haftungsausschluss
Dieses Add-on ist eine Community-Hülse ohne Garantie. Für Funktionsumfang, Stabilität und Konfiguration verweisen wir auf das Originalprojekt und dessen Wiki.
