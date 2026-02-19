<?php
require_once "config_parser.php";

$file = $PythonDIR . '/CONFIG/default.ini';
if (file_exists($PythonDIR . '/CONFIG/default_priv.ini')) {
    $file = $PythonDIR . '/CONFIG/default_priv.ini';
}

$nachricht = $_GET["nachricht"] ?? '';
if ($nachricht !== '') {
    echo htmlspecialchars($nachricht) . "<br><br>";
}

$host = '';

$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $zeile) {
    if (strpos($zeile, 'hostNameOrIp') !== false && strpos($zeile, '=') !== false) {
        [, $value] = explode("=", $zeile, 2);
        $host = trim($value);
        break;
    }
}

if ($host === '') {
    die("hostNameOrIp nicht gefunden");
}

// Schema ergänzen falls fehlt
if (!preg_match('#^https?://#', $host)) {
    $host = 'http://' . $host;
}

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$url = rtrim($host, '/') . $requestUri;

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER => true,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_CUSTOMREQUEST => $_SERVER['REQUEST_METHOD'],
]);

// Request Body weiterleiten
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}

// Original-Header weiterleiten (außer Host)
$forwardHeaders = [];
foreach (getallheaders() as $key => $value) {
    if (strtolower($key) !== 'host') {
        $forwardHeaders[] = "$key: $value";
    }
}
curl_setopt($ch, CURLOPT_HTTPHEADER, $forwardHeaders);

// SSL (optional anpassbar)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

$response = curl_exec($ch);

if ($response === false) {
    http_response_code(502);
    echo "Proxy Fehler: " . curl_error($ch);
    exit;
}

$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headerBlock = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
http_response_code($httpCode);

curl_close($ch);

// Header filtern
foreach (explode("\r\n", $headerBlock) as $header) {
    if (
        stripos($header, 'X-Frame-Options:') === false &&
        stripos($header, 'Content-Security-Policy:') === false &&
        stripos($header, 'Content-Length:') === false &&
        stripos($header, 'Transfer-Encoding:') === false
    ) {
        if (trim($header) !== '') {
            header($header, false);
        }
    }
}

echo $body;
