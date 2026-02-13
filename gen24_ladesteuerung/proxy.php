<?php
require_once "config_parser.php";

/* ---------------- Config laden ---------------- */

$file = $PythonDIR . '/CONFIG/default.ini';
if (file_exists($PythonDIR . '/CONFIG/default_priv.ini')) {
    $file = $PythonDIR . '/CONFIG/default_priv.ini';
}

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
    http_response_code(500);
    die('hostNameOrIp nicht gefunden');
}

/* ---------------- Zielpfad ---------------- */

$path = $_GET['p'] ?? '/';
if ($path === '') $path = '/';

$query = $_SERVER['QUERY_STRING'] ?? '';
$query = preg_replace('/(^|&)p=[^&]*/', '', $query);
$query = ltrim($query, '&');

$targetUrl = "http://$host$path";
if ($query !== '') {
    $targetUrl .= '?' . $query;
}

/* ---------------- Request ---------------- */

$context = stream_context_create([
    'http' => [
        'method'  => $_SERVER['REQUEST_METHOD'],
        'timeout' => 10,
        'header'  => "User-Agent: HA-Ingress-Proxy\r\n"
    ]
]);

$response = @file_get_contents($targetUrl, false, $context);
if ($response === false) {
    http_response_code(502);
    die("Ziel nicht erreichbar: $targetUrl");
}

/* ---------------- Header ---------------- */

$contentType = null;
if (isset($http_response_header)) {
    foreach ($http_response_header as $h) {
        if (stripos($h, 'Content-Type:') === 0) {
            $contentType = $h;
            header($h);
            break;
        }
    }
}

/* ---------------- Rewrite (entscheidend!) ---------------- */

if ($contentType && stripos($contentType, 'text/html') !== false) {

    // Root-Pfade umbiegen (/api/... /css/... etc.)
    $response = preg_replace(
        '/(href|src|action)=["\']\/([^"\']*)["\']/i',
        '$1="proxy.php?p=/$2"',
        $response
    );

    // fetch("/api/...") umschreiben
    $response = preg_replace(
        '/fetch\(["\']\/([^"\']*)["\']\)/i',
        'fetch("proxy.php?p=/$1")',
        $response
    );

    // axios("/api/...") umschreiben
    $response = preg_replace(
        '/axios\(["\']\/([^"\']*)["\']\)/i',
        'axios("proxy.php?p=/$1")',
        $response
    );
}

echo $response;

