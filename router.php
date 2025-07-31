<?php
$requestedPath = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$fullPath = $_SERVER["DOCUMENT_ROOT"] . $requestedPath;

if ($requestedPath === '/' || $requestedPath === '') {
    
    require_once 'hostpage.php';
    exit;
}

if (php_sapi_name() === 'cli-server' && file_exists($fullPath)) {
    return false;
}

// Vše ostatní fallbackne taky na hostpage.php
require_once 'hostpage.php';