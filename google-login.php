<?php
require_once 'vendor/autoload.php'; // Načtení Google API knihovny
session_start();

// Nastavení Google API Client
$client = new Google_Client();
$client->setClientId('699961376156-vgbq2pahgb84jon13vr2rq7lbrd7ambp.apps.googleusercontent.com'); // Nahraďte svým Client ID
$client->setClientSecret('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'); // Nahraďte svým Client Secret
$client->setRedirectUri('http://localhost/financetrack/google-callback.php'); // Nahraďte svou redirect URI
$client->addScope('email');
$client->addScope('profile');

// Vytvoříme URL pro přihlášení
$login_url = $client->createAuthUrl();
?>

<!-- HTML pro přihlášení -->
<a href="<?php echo $login_url; ?>">Click here to login with google</a>