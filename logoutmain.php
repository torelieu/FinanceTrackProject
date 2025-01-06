<?php
session_start();
session_unset(); // Vymaže všechny session proměnné
session_destroy(); // Zničí session
header('Location: loginpage.php'); // Přesměrování na přihlašovací stránku
exit();
?>