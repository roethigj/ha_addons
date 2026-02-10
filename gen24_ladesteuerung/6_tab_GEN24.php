<?php
require_once "config_parser.php";

// Config-Datei wählen
$file = $PythonDIR.'/CONFIG/default.ini';
if(file_exists($PythonDIR.'/CONFIG/default_priv.ini')){
    $file = $PythonDIR.'/CONFIG/default_priv.ini';
}

// Nachricht ausgeben, falls vorhanden
$nachricht = $_GET["nachricht"] ?? '';
if ($nachricht != '') echo $nachricht . "<br><br>";

// hostNameOrIp auslesen
$host = '';
$fh = fopen($file, "r") or die("Kann Datei ".$file." nicht öffnen!");
while(!feof($fh)) {
    $line = fgets($fh);
    if (strpos($line, 'hostNameOrIp') !== false && strpos($line, '=') !== false) {
        [, $value] = explode('=', $line, 2);
        $host = trim($value);
        break;
    }
}
fclose($fh);

if ($host === '') die("hostNameOrIp nicht gefunden");

// Ziel-URL: intern HTTP, extern als HTTPS
$target_url = "http://$host/";

// Browser Redirect
// Hier trick: wir nutzen Home Assistant Ingress HTTPS URL
// In HA-Ingress-iframe ist der HTTPS-Pfad immer korrekt
header("Content-Security-Policy: upgrade-insecure-requests"); // optional, erzwingt HTTPS
header("Location: http://$host");
exit;
