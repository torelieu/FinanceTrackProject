<?php
require_once 'vendor/autoload.php';
include('db.php');
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

    // Získání instance DB
    $db = Database::getInstance();

    // Hledání uživatele podle e-mailu
    $user = $db->findUserByEmail($user_info->email);

    if (!$user) {
        // Generování náhodného hesla
        $randomPassword = bin2hex(random_bytes(8)); // 16 znaků dlouhé heslo
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

        // Vložení nového uživatele BEZ ID (ID se vygeneruje automaticky)
        $db->insertGoogleUser($user_info->email, $user_info->name, $hashedPassword);

        // Získání nově vloženého uživatele
        $user = $db->findUserByEmail($user_info->email);
    }

    // Uložení do session a přesměrování
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['isGoogle'] = true;
    header('Location: indexmain.php');
    exit();
} else {
    echo "Přihlášení přes Google selhalo!";
}
?>