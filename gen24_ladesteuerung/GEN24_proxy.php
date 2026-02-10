<?php
require_once "config_parser.php";

/* Config-Datei bestimmen */
$file = $PythonDIR . '/CONFIG/default.ini';
if (file_exists($PythonDIR . '/CONFIG/default_priv.ini')) {
    $file = $PythonDIR . '/CONFIG/default_priv.ini';
}

/* hostNameOrIp auslesen */
$host = '';

$fh = fopen($file, 'r') or die("Kann Datei $file nicht öffnen!");
while (!feof($fh)) {
    $line = fgets($fh);
    if (strpos($line, 'hostNameOrIp') !== false && strpos($line, '=') !== false) {
        [, $value] = explode('=', $line, 2);
        $host = trim($value);
        break;
    }
}
fclose($fh);

if ($host === '') {
    die('hostNameOrIp nicht gefunden');
}

/* Ziel-URL (bewusst nur HTTP!) */
$targetUrl = "http://$host/";

/* Minimaler Proxy */
$ch = curl_init($targetUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT        => 10,
]);

$response = curl_exec($ch);
$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);

/* Content-Type durchreichen */
if ($contentType) {
    header("Content-Type: $contentType");
}

echo $response;
