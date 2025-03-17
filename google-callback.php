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

    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->execute(['id' => $user_info->id]);
    $user = $stmt->fetch();

    if (!$user) {
        // Generování náhodného hesla
        $randomPassword = bin2hex(random_bytes(8)); // 16 znaků dlouhé heslo
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO users (id, email, username, password_hash) VALUES (:id, :email, :username, :password_hash)');
        $stmt->execute([
            'id' => $user_info->id,
            'email' => $user_info->email,
            'username' => $user_info->name,
            'password_hash' => $hashedPassword
        ]);
        
        // Opětovné načtení uživatele z databáze
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $user_info->id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Uložení do session a přesměrování
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['isGoogle'] = true;
    header('Location: indexmain.php');
    exit();
} else {
    echo "Přihlášení přes Google selhalo!";
}