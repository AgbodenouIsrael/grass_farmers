<?php

// Démarrer la session pour les messages
session_start();

// Inclure la connexion à la base de données
require_once '../database/db.php';

// Configuration
$message = '';
$messageType = 'info';
$debugError = '';

// TRAITEMENT DU FORMULAIRE DE CONTACT

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'envoyer_message') {

    try {
        // Valider les données du formulaire
        $erreurs = validerFormulaireContact();

        if (!empty($erreurs)) {
            $message = 'Veuillez corriger les erreurs dans le formulaire.';
            $messageType = 'error';
        } else {
            // Préparer les données pour la sauvegarde
            $donneesFormulaire = preparerDonneesContact();

            // Sauvegarder dans la base de données
            $result = sauvegarderMessageContact($donneesFormulaire);

            if ($result) {
                $message = 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.';
                $messageType = 'success';
            } else {
                $message = 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer.';
                $messageType = 'error';
            }
        }

    } catch (Exception $e) {
        error_log('Erreur lors du traitement du message de contact: ' . $e->getMessage());
        $message = 'Une erreur inattendue est survenue. Veuillez réessayer.';
        $messageType = 'error';
    }
} else {
    // Accès direct au fichier sans POST
    $message = 'Accès non autorisé.';
    $messageType = 'error';
}

// FONCTIONS DE VALIDATION

function validerFormulaireContact()
{
    $erreurs = [];

    // Règles de validation
    $reglesValidation = [
        'nom' => [
            'required' => true,
            'minLength' => 2,
            'message' => 'Le nom doit contenir au moins 2 caractères.'
        ],
        'email' => [
            'required' => true,
            'message' => 'Veuillez saisir une adresse email valide.'
        ],
        'telephone' => [
            'pattern' => '/^[0-9\s\+\-\(\)]{8,}$/',
            'message' => 'Veuillez saisir un numéro de téléphone valide (au moins 8 chiffres).'
        ],
        'sujet' => [
            'required' => true,
            'message' => 'Veuillez sélectionner un sujet.'
        ],
        'message' => [
            'required' => true,
            'minLength' => 10,
            'message' => 'Le message doit contenir au moins 10 caractères.'
        ]
    ];

    foreach ($reglesValidation as $champ => $regle) {
        $valeur = isset($_POST[$champ]) ? trim($_POST[$champ]) : '';

        // Vérifier si le champ est requis
        if ($regle['required'] && empty($valeur)) {
            $erreurs[$champ] = $regle['message'];
            continue;
        }

        // Vérifier la longueur minimale
        if (!empty($valeur) && isset($regle['minLength']) && strlen($valeur) < $regle['minLength']) {
            $erreurs[$champ] = $regle['message'];
            continue;
        }

    }

    return $erreurs;
}

// FONCTIONS DE PRÉPARATION DES DONNÉES

function preparerDonneesContact()
{
    return [
        'nom' => $_POST['nom'] ?? '',
        'email' => $_POST['email'] ?? '',
        'telephone' => $_POST['telephone'] ?? '',
        'sujet' => $_POST['sujet'] ?? '',
        'message' => $_POST['message'] ?? '',
        'statut' => 'nouveau'
    ];
}

// FONCTIONS DE SAUVEGARDE EN BASE

function sauvegarderMessageContact($donnees)
{
    global $bdd;

    try {
        // Préparer la requête d'insertion
        $sql = "INSERT INTO messages_contact (
            nom, email, telephone, sujet, message, statut
        ) VALUES (
            ?, ?, ?, ?, ?, ?
        )";

        $stmt = $bdd->prepare($sql);

        // Exécuter la requête
        $result = $stmt->execute([
            $donnees['nom'],
            $donnees['email'],
            $donnees['telephone'],
            $donnees['sujet'],
            $donnees['message'],
            $donnees['statut']
            ]);

        return $result;

    } catch (PDOException $e) {
        global $debugError;
        $debugError = $e->getMessage();
        error_log('Erreur lors de la sauvegarde du message de contact: ' . $e->getMessage());
        return false;
    }
}

// AFFICHAGE DU RÉSULTAT

// Stocker le message dans la session pour l'afficher sur la page suivante
$_SESSION['message'] = $message;
$_SESSION['messageType'] = $messageType;

// Rediriger vers la page du formulaire avec le message
header('Location: ../../pages/contact.php');
exit;

?>
