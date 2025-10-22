<?php

session_start();
include '../database/db.php';
include '../cart_functions.php';

// Vérifier si le panier n'est pas vide
$panier = getPanier();
if (empty($panier)) {
    // Rediriger vers la boutique avec un message d'erreur
    header('Location: ../../pages/boutique.php?error=panier_vide');
    exit;
}

// Calculer le total
$total = calculerTotalPanier();

// Construire le message WhatsApp formaté
$message = " Nouvelle commande depuis AYOUBDECOR :\n\n";

foreach ($panier as $item) {
    $message .= "• " . $item['nom'] . " x" . $item['quantite'] . " → " . formatPrix($item['sous_total']) . "\n";
}

$message .= "\n Total : " . formatPrix($total) . "\n\n";
$message .= "Merci de me contacter pour finaliser le paiement.\n\n";
$message .= "Cordialement";

$message_encode = rawurlencode($message);

$whatsapp_url = "https://wa.me/+22891810232?text=" . $message_encode;

// Rediriger vers WhatsApp
header("Location: $whatsapp_url");
exit;
?>
