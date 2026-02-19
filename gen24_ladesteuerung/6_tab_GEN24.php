<?php
// Ziel-IP (interne Adresse im Docker/HA-Netzwerk)
$targetBase = "http:/192.168.178.106";  // <- ANPASSEN!

// Angeforderter Pfad + Query übernehmen
$requestUri = $_SERVER['REQUEST_URI'];
$targetUrl = $targetBase . $requestUri;

// HTTP-Methode übernehmen (GET, POST, PUT, DELETE ...)
$method = $_SERVER['REQUEST_METHOD'];

// Header übernehmen
$headers = [];
foreach (getallheaders() as $name => $value) {
    // Host-Header nicht weiterleiten (wird neu gesetzt)
    if (strtolower($name) !== 'host') {
        $headers[] = "$name: $value";
    }
}

// Body (z.B. bei POST/PUT)
$body = file_get_contents("php://input");

// cURL Initialisieren
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $targetUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

if (!empty($body)) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
}

// Anfrage ausführen
$response = curl_exec($ch);

if ($response === false) {
    http_response_code(502);
    echo "Proxy-Fehler: " . curl_error($ch);
    curl_close($ch);
    exit;
}

// Response trennen (Header + Body)
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$responseHeaders = substr($response, 0, $headerSize);
$responseBody = substr($response, $headerSize);

// HTTP-Statuscode übernehmen
$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
http_response_code($statusCode);

curl_close($ch);

// Response-Header weitergeben
$headerLines = explode("\r\n", $responseHeaders);
foreach ($headerLines as $headerLine) {
    if (stripos($headerLine, 'Transfer-Encoding:') === false &&
        stripos($headerLine, 'Content-Length:') === false &&
        stripos($headerLine, 'HTTP/') === false) {
        header($headerLine, false);
    }
}

// Body ausgeben
echo $responseBody;
