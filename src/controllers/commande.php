<?php
/**
 * Script de traitement du paiement - Redirection WhatsApp
 * Gère la récupération du panier et la génération du message WhatsApp
 */

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
$message = "🛒 Nouvelle commande depuis AYOUBDECOR :%0A%0A";

foreach ($panier as $item) {
    $message .= "• " . $item['nom'] . " x" . $item['quantite'] . " → " . formatPrix($item['sous_total']) . "%0A";
}

$message .= "%0A💰 Total : " . formatPrix($total) . "%0A%0A";
$message .= "Merci de me contacter pour finaliser le paiement.%0A%0A";
$message .= "Cordialement";

// Encoder le message pour l'URL (préserve les emojis UTF-8)
$message_encode = rawurlencode($message);

// Créer l'URL WhatsApp (remplacer par le numéro réel)
$whatsapp_url = "https://wa.me/+22891810232?text=" . $message_encode;

// Rediriger vers WhatsApp
header("Location: $whatsapp_url");
exit;
?>
