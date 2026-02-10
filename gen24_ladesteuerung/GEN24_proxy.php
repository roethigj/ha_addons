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

/* Ziel-URL (NUR http) */
$targetUrl = "http://$host/";

/* HTTP-Context */
$context = stream_context_create([
    'http' => [
        'method'  => 'GET',
        'timeout' => 10,
        'header'  => "User-Agent: HA-Ingress-Proxy\r\n"
    ]
]);

/* Anfrage */
$response = @file_get_contents($targetUrl, false, $context);
if ($response === false) {
    die('Ziel nicht erreichbar');
}

/* Content-Type übernehmen */
if (isset($http_response_header)) {
    foreach ($http_response_header as $h) {
        if (stripos($h, 'Content-Type:') === 0) {
            header($h);
            break;
        }
    }
}

echo $response;
