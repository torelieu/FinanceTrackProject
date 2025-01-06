<?php
require_once 'vendor/autoload.php';
session_start();

// Nastavení Google API Client
$client = new Google_Client();
$client->setClientId('699961376156-vgbq2pahgb84jon13vr2rq7lbrd7ambp.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/financetrack/google-callback.php');

// Získání ověřovacího kódu
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    // Získání informací o uživateli
    $google_service = new Google_Service_Oauth2($client);
    $user_info = $google_service->userinfo->get();

    // Uložení dat uživatele do session
    $_SESSION['user_id'] = $user_info->id;
    $_SESSION['user_email'] = $user_info->email;
    $_SESSION['user_name'] = $user_info->name;

    // Přesměrování na hlavní stránku
    header('Location: indexmain.php');
    exit();
} else {
    echo "Přihlášení přes Google selhalo!";
}