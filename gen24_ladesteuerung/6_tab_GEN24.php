<?php
require_once "config_parser.php";

$file = $PythonDIR.'/CONFIG/default.ini';
if (file_exists($PythonDIR.'/CONFIG/default_priv.ini')) {
    $file = $PythonDIR.'/CONFIG/default_priv.ini';
}

$nachricht = $_GET["nachricht"] ?? '';
if ($nachricht !== '') {
    echo htmlspecialchars($nachricht) . "<br><br>";
}

$host = '';

$myfile = fopen($file, "r") or die("Kann Datei $file nicht öffnen!");
while (!feof($myfile)) {
    $zeile = fgets($myfile);
    if (strpos($zeile, 'hostNameOrIp') !== false && strpos($zeile, '=') !== false) {
        [$key, $value] = explode("=", $zeile, 2);
        $host = trim($value);
        break;
    }
}
fclose($myfile);

if ($host === '') {
    die("hostNameOrIp nicht gefunden");
}
<?php

// Zielserver
$target = $host;

// Angeforderter Pfad weiterreichen
$requestUri = $_SERVER['REQUEST_URI'];
$url = rtrim($target, '/') . $requestUri;

// cURL initialisieren
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Request-Methode übernehmen (GET, POST, etc.)
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);

// POST-Daten weiterreichen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}

// Response holen
$response = curl_exec($ch);

if ($response === false) {
    http_response_code(502);
    echo "Proxy Fehler: " . curl_error($ch);
    exit;
}

$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
http_response_code($httpCode);

curl_close($ch);

// Header filtern (iframe-blockierende Header entfernen)
$headerLines = explode("\r\n", $headers);

foreach ($headerLines as $header) {
    if (
        stripos($header, 'X-Frame-Options:') === false &&
        stripos($header, 'Content-Security-Policy:') === false &&
        stripos($header, 'Content-Length:') === false
    ) {
        header($header);
    }
}

// Body ausgeben
echo $body;

