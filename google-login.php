<?php
//Session musí být jako první
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'vendor/autoload.php'; // Načtení Google API knihovny

// Nastavení Google API Client

$clientid = getenv('GOOGLE_CLIENT_ID');
$clientsecret = getenv('GOOGLE_CLIENT_SECRET');

$client = new Google_Client();
$client->setClientId($clientid); // Nahraďte svým Client ID
$client->setClientSecret($clientsecret); // Nahraďte svým Client Secret
$client->setRedirectUri('https://finance-track-uxdj.onrender.com/google-callback.php'); // Nahraďte svou redirect URI
$client->addScope('email');
$client->addScope('profile');

// Vytvoříme URL pro přihlášení
$login_url = $client->createAuthUrl();
header('Location: ' . filter_var($login_url, FILTER_SANITIZE_URL));
?>