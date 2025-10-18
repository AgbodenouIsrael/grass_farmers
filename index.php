<?php
/**
 * Point d'entrée principal du projet AYOUBDECOR
 * Redirige vers la page d'accueil
 */

// Démarrer la session si nécessaire
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Rediriger vers la page d'accueil
header('Location: pages/acceuil.php');
exit;
?>
