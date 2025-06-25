<?php
// Pokud požadovaný soubor existuje, vrať ho přímo (např. loginpage.php)
$requested = $_SERVER["DOCUMENT_ROOT"] . parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

if (php_sapi_name() === 'cli-server' && file_exists($requested)) {
    return false; // PHP built-in server obslouží soubor sám
}

// Jinak vrať hostpage jako výchozí (např. nepřihlášený uživatel)
require_once 'hostpage.php';