<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'vendor/autoload.php'; // Načtení Google API knihovny

// Nastavení Google API Client

$clientid = getenv('GOOGLE_CLIENT_ID');
$clientsecret = getenv('GOOGLE_CLIENT_SECRET');

$client = new Google_Client();
$client->setClientId($clientid);
$client->setClientSecret($clientsecret);
$client->setRedirectUri('https://finance-track-uxdj.onrender.com/google-callback.php');
$client->addScope('email');
$client->addScope('profile');

// Vytvoří URL pro přihlášení
$login_url = $client->createAuthUrl();
header('Location: ' . filter_var($login_url, FILTER_SANITIZE_URL));
?>