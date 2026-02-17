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
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Ingress Anzeige</title>
    <style>
        html, body, iframe {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
    </style>
</head>
<body>
    <iframe src="https://<?= htmlspecialchars($host) ?>"></iframe>
</body>
</html>
