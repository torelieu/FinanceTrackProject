<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'vendor/autoload.php';
require_once 'db.php';

// Nastavení Google API Client

$clientid = getenv('GOOGLE_CLIENT_ID');
$clientsecret = getenv('GOOGLE_CLIENT_SECRET');

$client = new Google_Client();
$client->setClientId($clientid);
$client->setClientSecret($clientsecret);
$client->setRedirectUri('https://finance-track-uxdj.onrender.com/google-callback.php');

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
        // Generování random hesla
        $randomPassword = bin2hex(random_bytes(8));
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

        // Vložení nového uživatele BEZ ID (ID se vygeneruje automaticky)
        $db->insertGoogleUser($user_info->email, $user_info->name, $hashedPassword);

        $user = $db->findUserByEmail($user_info->email);
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['isGoogle'] = true;
    header('Location: /indexmain.php');
    exit();
} else {
    echo "Přihlášení přes Google selhalo!";
}
?>