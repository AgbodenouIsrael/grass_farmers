<?php

// Démarrer la session pour les messages
session_start();

// Inclure la connexion à la base de données
require_once '../database/db.php';

// Configuration
$message = '';
$messageType = 'info';
$debugError = '';

// TRAITEMENT DU FORMULAIRE DE DEVIS

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'soumettre_devis') {

    try {
        // Traiter les fichiers uploadés
        $imagesUploades = traiterFichiersUpload();

        // Valider les données du formulaire
        $erreurs = validerFormulaireDevis();

        if (!empty($erreurs)) {
            $message = 'Veuillez corriger les erreurs dans le formulaire.';
            $messageType = 'error';
        } else {
            // Préparer les données pour la sauvegarde
            $donneesFormulaire = preparerDonneesFormulaire($imagesUploades);

            // Sauvegarder dans la base de données
            $result = sauvegarderDevis($donneesFormulaire);

            if ($result) {
                $message = 'Votre demande de devis a été reçue avec succès ! Nous vous contacterons dans les plus brefs délais.';
                $messageType = 'success';

            } else {
                $message = 'Une erreur est survenue lors de l\'envoi de votre demande. Veuillez réessayer.';
                $messageType = 'error';
            }
        }

    } catch (Exception $e) {
        error_log('Erreur lors du traitement du devis: ' . $e->getMessage());
        $message = 'Une erreur inattendue est survenue. Veuillez réessayer.';
        $messageType = 'error';
    }
} else {
    // Accès direct au fichier sans POST
    $message = 'Accès non autorisé.';
    $messageType = 'error';
}

//Traiter les fichiers uploadés et les sauvegarder

function traiterFichiersUpload()
{
    global $message, $messageType;

    $imagesUploades = [];

    if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        return $imagesUploades;
    }

    $fichiers = $_FILES['images'];

    // Créer le dossier d'upload s'il n'existe pas
    $dossierUpload = 'uploads/';
    if (!is_dir($dossierUpload)) {
        mkdir($dossierUpload, 0755, true);
    }

    // Gérer le cas où un seul fichier est uploadé
    if (!is_array($fichiers['name'])) {
        $fichiers = [
            'name' => [$fichiers['name']],
            'type' => [$fichiers['type']],
            'tmp_name' => [$fichiers['tmp_name']],
            'error' => [$fichiers['error']],
            'size' => [$fichiers['size']]
        ];
    }

    // Vérifier le nombre maximum d'images
    if (count($fichiers['name']) > 5) {
        $message = 'Vous ne pouvez pas uploader plus de 5 images.';
        $messageType = 'error';
        return $imagesUploades;
    }

    // Traiter chaque fichier
    foreach ($fichiers['name'] as $index => $nomFichier) {
        if ($fichiers['error'][$index] === UPLOAD_ERR_OK) {
            $imageUpload = traiterFichierUpload($fichiers, $index);
            if ($imageUpload) {
                $imagesUploades[] = $imageUpload;
            }
        }
    }

    return $imagesUploades;
}

//  * Traiter un fichier individuel

function traiterFichierUpload($fichiers, $index)
{
    global $message, $messageType;

    $nomFichier = $fichiers['name'][$index];
    $typeFichier = $fichiers['type'][$index];
    $tmpName = $fichiers['tmp_name'][$index];
    $tailleFichier = $fichiers['size'][$index];

    // Vérifier le type de fichier
    $typesImagesAutorises = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!in_array($typeFichier, $typesImagesAutorises)) {
        $message = "Le fichier {$nomFichier} n'est pas un format d'image supporté.";
        $messageType = 'error';
        return false;
    }

    // Vérifier la taille (5MB max)
    $tailleMax = 5 * 1024 * 1024;
    if ($tailleFichier > $tailleMax) {
        $message = "Le fichier {$nomFichier} est trop volumineux (max 5MB).";
        $messageType = 'error';
        return false;
    }

    // Générer un nom unique pour le fichier
    $extension = pathinfo($nomFichier, PATHINFO_EXTENSION);
    $nomUnique = uniqid('devis_', true) . '.' . $extension;
    $cheminDestination = 'uploads/' . $nomUnique;

    // Déplacer le fichier
    if (move_uploaded_file($tmpName, $cheminDestination)) {
        return $nomUnique; // Retourner le nom du fichier uploadé
    } else {
        $message = "Erreur lors de l'upload du fichier {$nomFichier}.";
        $messageType = 'error';
        return false;
    }
}

// FONCTIONS DE VALIDATION

// Valider les données du formulaire

function validerFormulaireDevis()
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
            'pattern' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
            'message' => 'Veuillez saisir une adresse email valide.'
        ],
        'telephone' => [
            'required' => true,
            'pattern' => '/^[0-9\s\+\-\(\)]{8,}$/',
            'message' => 'Veuillez saisir un numéro de téléphone valide (au moins 8 chiffres).'
        ],
        'type_prestation' => [
            'required' => true,
            'message' => 'Veuillez sélectionner un type de prestation.'
        ],
        'description' => [
            'required' => true,
            'minLength' => 10,
            'message' => 'La description doit contenir au moins 10 caractères.'
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

        // Vérifier le pattern
        if (!empty($valeur) && isset($regle['pattern']) && !preg_match($regle['pattern'], $valeur)) {
            $erreurs[$champ] = $regle['message'];
            continue;
        }
    }

    return $erreurs;
}

// FONCTIONS DE PRÉPARATION DES DONNÉES

// Préparer les données du formulaire pour la sauvegarde

function preparerDonneesFormulaire($imagesUploades = [])
{
    return [
        // Informations personnelles
        'nom' => $_POST['nom'] ?? '',
        'email' => $_POST['email'] ?? '',
        'telephone' => $_POST['telephone'] ?? '',
        'ville' => $_POST['ville'] ?? '',

        // Détails du projet
        'type_prestation' => $_POST['type_prestation'] ?? '',
        'type_meuble' => $_POST['type_meuble'] ?? '',
        'longueur' => !empty($_POST['longueur']) ? $_POST['longueur'] : null,
        'largeur' => !empty($_POST['largeur']) ? $_POST['largeur'] : null,
        'hauteur' => !empty($_POST['hauteur']) ? $_POST['hauteur'] : null,
        'materiau' => $_POST['materiau'] ?? '',
        'finition' => $_POST['finition'] ?? '',

        // Budget et planning
        'budget' => $_POST['budget'] ?? '',
        'date_souhaitee' => !empty($_POST['date_souhaitee']) ? $_POST['date_souhaitee'] : null,
        'urgence' => $_POST['urgence'] ?? 'normal',

        // Description détaillée
        'description' => $_POST['description'] ?? '',
        'contraintes' => $_POST['contraintes'] ?? '',
        'inspiration' => $_POST['inspiration'] ?? '',

        // Images
        'images' => !empty($imagesUploades) ? json_encode($imagesUploades) : null,

        // Options supplémentaires (supprimées du formulaire actuel)
        'deplacement' => isset($_POST['deplacement']) ? 1 : 0,
        'conseil_materiaux' => isset($_POST['conseil_materiaux']) ? 1 : 0,
        'pose' => isset($_POST['pose']) ? 1 : 0,
        'garantie_etendue' => isset($_POST['garantie_etendue']) ? 1 : 0,

        // Communication
        'contact_email' => isset($_POST['contact_email']) ? 1 : 0,
        'contact_telephone' => isset($_POST['contact_telephone']) ? 1 : 0,
        'contact_sms' => isset($_POST['contact_sms']) ? 1 : 0,
        'horaire_contact' => $_POST['horaire_contact'] ?? '',

        // Confidentialité
        'confidentialite' => isset($_POST['confidentialite']) ? 1 : 0,
        'newsletter' => isset($_POST['newsletter']) ? 1 : 0,

        // Métadonnées
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'statut' => 'nouveau'
    ];
}

// FONCTIONS DE SAUVEGARDE EN BASE

// Sauvegarder le devis dans la base de données

function sauvegarderDevis($donnees)
{
    global $bdd;

    try {


        // Préparer la requête d'insertion
        $sql = "INSERT INTO devis (
            nom, email, telephone, ville,
            type_prestation, type_meuble, longueur, largeur, hauteur, materiau, finition,
            budget, date_souhaitee, urgence,
            description, contraintes, inspiration,
            images,
            deplacement, conseil_materiaux, pose, garantie_etendue,
            contact_email, contact_telephone, contact_sms, horaire_contact,
            confidentialite, newsletter,
            user_agent, ip_address, statut
        ) VALUES (
            :nom, :email, :telephone, :ville,
            :type_prestation, :type_meuble, :longueur, :largeur, :hauteur, :materiau, :finition,
            :budget, :date_souhaitee, :urgence,
            :description, :contraintes, :inspiration,
            :images,
            :deplacement, :conseil_materiaux, :pose, :garantie_etendue,
            :contact_email, :contact_telephone, :contact_sms, :horaire_contact,
            :confidentialite, :newsletter,
            :user_agent, :ip_address, :statut
        )";

        $stmt = $bdd->prepare($sql);

        // Binder les paramètres
        $stmt->bindParam(':nom', $donnees['nom']);
        $stmt->bindParam(':email', $donnees['email']);
        $stmt->bindParam(':telephone', $donnees['telephone']);
        $stmt->bindParam(':ville', $donnees['ville']);
        $stmt->bindParam(':type_prestation', $donnees['type_prestation']);
        $stmt->bindParam(':type_meuble', $donnees['type_meuble']);
        $stmt->bindParam(':longueur', $donnees['longueur']);
        $stmt->bindParam(':largeur', $donnees['largeur']);
        $stmt->bindParam(':hauteur', $donnees['hauteur']);
        $stmt->bindParam(':materiau', $donnees['materiau']);
        $stmt->bindParam(':finition', $donnees['finition']);
        $stmt->bindParam(':budget', $donnees['budget']);
        $stmt->bindParam(':date_souhaitee', $donnees['date_souhaitee']);
        $stmt->bindParam(':urgence', $donnees['urgence']);
        $stmt->bindParam(':description', $donnees['description']);
        $stmt->bindParam(':contraintes', $donnees['contraintes']);
        $stmt->bindParam(':inspiration', $donnees['inspiration']);
        $stmt->bindParam(':images', $donnees['images']);
        $stmt->bindParam(':deplacement', $donnees['deplacement'], PDO::PARAM_BOOL);
        $stmt->bindParam(':conseil_materiaux', $donnees['conseil_materiaux'], PDO::PARAM_BOOL);
        $stmt->bindParam(':pose', $donnees['pose'], PDO::PARAM_BOOL);
        $stmt->bindParam(':garantie_etendue', $donnees['garantie_etendue'], PDO::PARAM_BOOL);
        $stmt->bindParam(':contact_email', $donnees['contact_email'], PDO::PARAM_BOOL);
        $stmt->bindParam(':contact_telephone', $donnees['contact_telephone'], PDO::PARAM_BOOL);
        $stmt->bindParam(':contact_sms', $donnees['contact_sms'], PDO::PARAM_BOOL);
        $stmt->bindParam(':horaire_contact', $donnees['horaire_contact']);
        $stmt->bindParam(':confidentialite', $donnees['confidentialite'], PDO::PARAM_BOOL);
        $stmt->bindParam(':newsletter', $donnees['newsletter'], PDO::PARAM_BOOL);
        $stmt->bindParam(':user_agent', $donnees['user_agent']);
        $stmt->bindParam(':ip_address', $donnees['ip_address']);
        $stmt->bindParam(':statut', $donnees['statut']);

        // Exécuter la requête
        $result = $stmt->execute();

        return $result;

    } catch (PDOException $e) {
        global $debugError;
        $debugError = $e->getMessage();
        error_log('Erreur lors de la sauvegarde du devis: ' . $e->getMessage());
        return false;
    }
}




// AFFICHAGE DU RÉSULTAT

// Stocker le message dans la session pour l'afficher sur la page suivante
$_SESSION['message'] = $message;
$_SESSION['messageType'] = $messageType;

// Rediriger vers la page du formulaire avec le message
header('Location: ../../pages/devis.php');
exit;

?>
