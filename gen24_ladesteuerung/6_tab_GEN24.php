<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// ======= KONFIG =======
$targetBase = "http://192.168.178.106";  // <- anpassen
// =======================

// Request-Daten
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$targetUrl = $targetBase . $requestUri;

// Header manuell aufbauen (HA-kompatibel)
$headers = [];
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0) {
        $header = str_replace('_', '-', substr($key, 5));
        if (strtolower($header) !== 'host') {
            $headers[] = "$header: $value";
        }
    }
}

// Body holen
$body = file_get_contents("php://input");

// cURL
$ch = curl_init($targetUrl);

curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST  => $method,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HEADER         => true,
    CURLOPT_HTTPHEADER     => $headers,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_TIMEOUT        => 30,
]);

if (!empty($body)) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
}

$response = curl_exec($ch);

if ($response === false) {
    http_response_code(502);
    echo "cURL Error: " . curl_error($ch);
    curl_close($ch);
    exit;
}

$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

$responseHeaders = substr($response, 0, $headerSize);
$responseBody    = substr($response, $headerSize);

curl_close($ch);

// Status setzen
http_response_code($statusCode);

// Header weitergeben
foreach (explode("\r\n", $responseHeaders) as $headerLine) {
    if (
        stripos($headerLine, 'Transfer-Encoding:') === false &&
        stripos($headerLine, 'Content-Length:') === false &&
        stripos($headerLine, 'HTTP/') === false &&
        !empty($headerLine)
    ) {
        header($headerLine, false);
    }
}

echo $responseBody;
