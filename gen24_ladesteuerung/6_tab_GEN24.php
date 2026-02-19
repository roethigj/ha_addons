<?php
require_once "config_parser.php";

$file = $PythonDIR . '/CONFIG/default.ini';
if (file_exists($PythonDIR . '/CONFIG/default_priv.ini')) {
    $file = $PythonDIR . '/CONFIG/default_priv.ini';
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

// Schema ergänzen
if (!preg_match('#^https?://#', $host)) {
    $host = 'http://' . $host;
}

// -------------------- Frontend-Modus → HTML mit iframe --------------------
if (!isset($_GET['proxy'])) {
    $self = strtok($_SERVER["REQUEST_URI"], '?');

    echo '<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Proxy Anzeige</title>
<style>
html, body { margin:0; padding:0; height:100%; }
iframe { width:100%; height:100%; border:none; }
</style>
</head>
<body>
<iframe src="' . htmlspecialchars($self) . '?proxy=1&tab=Gen24"></iframe>
</body>
</html>';

    exit;
}

// -------------------- Proxy-Modus → Nur Body --------------------
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// query-Parameter proxy=1 entfernen, tab=Gen24 bleibt erhalten
$queryString = $_SERVER['QUERY_STRING'] ?? '';
parse_str($queryString, $queryParams);
unset($queryParams['proxy']);
$qs = http_build_query($queryParams);
if ($qs !== '') {
    $requestUri = strtok($_SERVER['REQUEST_URI'], '?') . '?' . $qs;
} else {
    $requestUri = strtok($_SERVER['REQUEST_URI'], '?');
}

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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}

// Header weiterleiten
$forwardHeaders = [];
foreach (getallheaders() as $key => $value) {
    if (strtolower($key) !== 'host') {
        $forwardHeaders[] = "$key: $value";
    }
}
curl_setopt($ch, CURLOPT_HTTPHEADER, $forwardHeaders);

// SSL optional (Self-Signed)
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
http
