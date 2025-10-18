<?php

// Démarrer la session pour les messages
session_start();

// Inclure la connexion à la base de données
require_once '../database/db.php';

// Configuration
$message = '';
$messageType = 'info';
$debugError = '';

// TRAITEMENT DES ACTIONS POST POUR LA GALERIE

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'ajouter_image':
            $result = ajouterImageGalerie();
            if ($result) {
                $message = 'Image ajoutée à la galerie avec succès.';
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de l\'ajout de l\'image.';
                $messageType = 'error';
            }
            break;

        case 'modifier_image':
            $result = modifierImageGalerie();
            if ($result) {
                $message = 'Image modifiée avec succès.';
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de la modification de l\'image.';
                $messageType = 'error';
            }
            break;

        case 'supprimer_image':
            $result = supprimerImageGalerie();
            if ($result) {
                $message = 'Image supprimée avec succès.';
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de la suppression de l\'image.';
                $messageType = 'error';
            }
            break;

        case 'modifier_ordre':
            $result = modifierOrdreImages();
            if ($result) {
                $message = 'Ordre des images mis à jour avec succès.';
                $messageType = 'success';
            } else {
                $message = 'Erreur lors de la mise à jour de l\'ordre.';
                $messageType = 'error';
            }
            break;
    }
} else {
    // Accès direct au fichier sans POST
    $message = 'Accès non autorisé.';
    $messageType = 'error';
}

// FONCTIONS DE GESTION DE LA GALERIE

// Ajouter une image à la galerie
function ajouterImageGalerie()
{
    global $message, $messageType;

    try {
        // Validation des données
        if (empty($_POST['titre']) || empty($_POST['categorie'])) {
            $message = 'Le titre et la catégorie sont obligatoires.';
            $messageType = 'error';
            return false;
        }

        // Traitement du fichier uploadé
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $message = 'Erreur lors du téléchargement de l\'image.';
            $messageType = 'error';
            return false;
        }

        $imageUpload = traiterUploadImageGalerie();
        if (!$imageUpload) {
            return false;
        }

        // Préparation des données
        $donnees = [
            'titre' => $_POST['titre'],
            'description' => $_POST['description'] ?? '',
            'categorie' => $_POST['categorie'],
            'image_path' => $imageUpload,
            'ordre' => obtenirProchainOrdre($_POST['categorie']),
            'statut' => isset($_POST['statut']) ? 1 : 0
        ];

        // Sauvegarde en base
        return sauvegarderImageGalerie($donnees);

    } catch (Exception $e) {
        global $debugError;
        $debugError = $e->getMessage();
        error_log('Erreur lors de l\'ajout d\'image galerie: ' . $e->getMessage());
        return false;
    }
}

// Modifier une image de la galerie
function modifierImageGalerie()
{
    global $message, $messageType;

    try {
        if (empty($_POST['id'])) {
            $message = 'ID de l\'image manquant.';
            $messageType = 'error';
            return false;
        }

        $id = intval($_POST['id']);
        $donnees = [
            'titre' => $_POST['titre'] ?? '',
            'description' => $_POST['description'] ?? '',
            'categorie' => $_POST['categorie'] ?? '',
            'statut' => isset($_POST['statut']) ? 1 : 0
        ];

        // Traitement d'une nouvelle image si uploadée
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $nouvelleImage = traiterUploadImageGalerie();
            if ($nouvelleImage) {
                $donnees['image_path'] = $nouvelleImage;
                // Supprimer l'ancienne image
                supprimerAncienneImage($id);
            }
        }

        return modifierImageGalerieDB($id, $donnees);

    } catch (Exception $e) {
        global $debugError;
        $debugError = $e->getMessage();
        error_log('Erreur lors de la modification d\'image galerie: ' . $e->getMessage());
        return false;
    }
}

// Supprimer une image de la galerie
function supprimerImageGalerie()
{
    global $message, $messageType;

    try {
        if (empty($_POST['id'])) {
            $message = 'ID de l\'image manquant.';
            $messageType = 'error';
            return false;
        }

        $id = intval($_POST['id']);

        // Supprimer l'image du serveur
        supprimerAncienneImage($id);

        // Supprimer de la base de données
        return supprimerImageGalerieDB($id);

    } catch (Exception $e) {
        global $debugError;
        $debugError = $e->getMessage();
        error_log('Erreur lors de la suppression d\'image galerie: ' . $e->getMessage());
        return false;
    }
}

// Modifier l'ordre des images
function modifierOrdreImages()
{
    global $message, $messageType;

    try {
        if (empty($_POST['ordre_images'])) {
            $message = 'Données d\'ordre manquantes.';
            $messageType = 'error';
            return false;
        }

        $ordreImages = json_decode($_POST['ordre_images'], true);
        if (!$ordreImages) {
            $message = 'Format d\'ordre invalide.';
            $messageType = 'error';
            return false;
        }

        return mettreAJourOrdreImages($ordreImages);

    } catch (Exception $e) {
        global $debugError;
        $debugError = $e->getMessage();
        error_log('Erreur lors de la modification d\'ordre: ' . $e->getMessage());
        return false;
    }
}

// FONCTIONS UTILITAIRES

// Traiter l'upload d'une image pour la galerie
function traiterUploadImageGalerie()
{
    global $message, $messageType;

    $fichier = $_FILES['image'];

    // Vérifications de sécurité
    $typesAutorises = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    if (!in_array($fichier['type'], $typesAutorises)) {
        $message = 'Format d\'image non supporté. Utilisez JPG, PNG ou WebP.';
        $messageType = 'error';
        return false;
    }

    // Vérification de la taille (max 10MB)
    $tailleMax = 10 * 1024 * 1024;
    if ($fichier['size'] > $tailleMax) {
        $message = 'L\'image est trop volumineuse (max 10MB).';
        $messageType = 'error';
        return false;
    }

    // Créer le dossier s'il n'existe pas
    $dossierUpload = '../../assets/gallery/';
    if (!is_dir($dossierUpload)) {
        mkdir($dossierUpload, 0755, true);
    }

    // Générer un nom unique
    $extension = pathinfo($fichier['name'], PATHINFO_EXTENSION);
    $nomUnique = 'galerie_' . uniqid() . '.' . $extension;
    $cheminDestination = $dossierUpload . $nomUnique;

    // Redimensionner et optimiser l'image si nécessaire
    if (redimensionnerImage($fichier['tmp_name'], $cheminDestination, $extension)) {
        return 'gallery/' . $nomUnique;
    }

    $message = 'Erreur lors du traitement de l\'image.';
    $messageType = 'error';
    return false;
}

// Redimensionner une image
function redimensionnerImage($source, $destination, $extension)
{
    // Pour l'instant, juste déplacer le fichier
    // TODO: Implémenter le redimensionnement avec GD ou Imagick
    return move_uploaded_file($source, $destination);
}

// Obtenir le prochain ordre pour une catégorie
function obtenirProchainOrdre($categorie)
{
    global $bdd;

    try {
        $stmt = $bdd->prepare("SELECT MAX(ordre) as max_ordre FROM galerie WHERE categorie = ?");
        $stmt->execute([$categorie]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['max_ordre'] ?? 0) + 1;
    } catch (PDOException $e) {
        error_log('Erreur lors de l\'obtention du prochain ordre: ' . $e->getMessage());
        return 1;
    }
}

// Supprimer l'ancienne image du serveur
function supprimerAncienneImage($id)
{
    global $bdd;

    try {
        $stmt = $bdd->prepare("SELECT image_path FROM galerie WHERE id = ?");
        $stmt->execute([$id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image && !empty($image['image_path'])) {
            $cheminImage = '../../assets/' . $image['image_path'];
            if (file_exists($cheminImage)) {
                unlink($cheminImage);
            }
        }
    } catch (PDOException $e) {
        error_log('Erreur lors de la suppression de l\'ancienne image: ' . $e->getMessage());
    }
}

// FONCTIONS DE BASE DE DONNÉES

// Sauvegarder une image en base
function sauvegarderImageGalerie($donnees)
{
    global $bdd;

    try {
        $sql = "INSERT INTO galerie (titre, description, categorie, image_path, ordre, statut, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $bdd->prepare($sql);
        return $stmt->execute([
            $donnees['titre'],
            $donnees['description'],
            $donnees['categorie'],
            $donnees['image_path'],
            $donnees['ordre'],
            $donnees['statut']
        ]);
    } catch (PDOException $e) {
        global $debugError;
        $debugError = $e->getMessage();
        error_log('Erreur lors de la sauvegarde en base: ' . $e->getMessage());
        return false;
    }
}

// Modifier une image en base
function modifierImageGalerieDB($id, $donnees)
{
    global $bdd;

    try {
        $sql = "UPDATE galerie SET titre = ?, description = ?, categorie = ?, statut = ?";
        $params = [$donnees['titre'], $donnees['description'], $donnees['categorie'], $donnees['statut']];

        if (isset($donnees['image_path'])) {
            $sql .= ", image_path = ?";
            $params[] = $donnees['image_path'];
        }

        $sql .= ", updated_at = NOW() WHERE id = ?";
        $params[] = $id;

        $stmt = $bdd->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        global $debugError;
        $debugError = $e->getMessage();
        error_log('Erreur lors de la modification en base: ' . $e->getMessage());
        return false;
    }
}

// Supprimer une image de la base
function supprimerImageGalerieDB($id)
{
    global $bdd;

    try {
        $stmt = $bdd->prepare("DELETE FROM galerie WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        global $debugError;
        $debugError = $e->getMessage();
        error_log('Erreur lors de la suppression en base: ' . $e->getMessage());
        return false;
    }
}

// Mettre à jour l'ordre des images
function mettreAJourOrdreImages($ordreImages)
{
    global $bdd;

    try {
        $bdd->beginTransaction();

        foreach ($ordreImages as $id => $ordre) {
            $stmt = $bdd->prepare("UPDATE galerie SET ordre = ? WHERE id = ?");
            $stmt->execute([$ordre, $id]);
        }

        $bdd->commit();
        return true;
    } catch (PDOException $e) {
        $bdd->rollBack();
        global $debugError;
        $debugError = $e->getMessage();
        error_log('Erreur lors de la mise à jour de l\'ordre: ' . $e->getMessage());
        return false;
    }
}

// AFFICHAGE DU RÉSULTAT

// Stocker le message dans la session pour l'afficher sur la page suivante
$_SESSION['message'] = $message;
$_SESSION['messageType'] = $messageType;

// Rediriger vers la page admin avec la section galerie
header('Location: ../../admin/admin.php?section=galerie');
exit;

?>
