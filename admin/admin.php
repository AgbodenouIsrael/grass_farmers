<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure la connexion à la base de données
require_once dirname(__DIR__) . '/src/database/db.php';

// Vérifier si l'administrateur est connecté
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

// Configuration
$page_title = "Administration - AYOUBDECOR";
$message = '';
$messageType = 'info';

// Récupérer la section active
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

// Traitement des actions POST selon la section
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            // Actions pour les devis
            case 'update_status':
                if (isset($_POST['devis_id']) && isset($_POST['statut'])) {
                    $result = updateDevisStatus($_POST['devis_id'], $_POST['statut']);
                    if ($result) {
                        $message = 'Statut du devis mis à jour avec succès.';
                        $messageType = 'success';
                    } else {
                        $message = 'Erreur lors de la mise à jour du statut.';
                        $messageType = 'error';
                    }
                }
                break;

            case 'delete_devis':
                if (isset($_POST['devis_id'])) {
                    $result = deleteDevis($_POST['devis_id']);
                    if ($result) {
                        $message = 'Devis supprimé avec succès.';
                        $messageType = 'success';
                    } else {
                        $message = 'Erreur lors de la suppression du devis.';
                        $messageType = 'error';
                    }
                }
                break;

                // Actions pour les messages de contact
            case 'update_contact_status':
                if (isset($_POST['contact_id']) && isset($_POST['statut'])) {
                    $result = updateContactStatus($_POST['contact_id'], $_POST['statut']);
                    if ($result) {
                        $message = 'Statut du message mis à jour avec succès.';
                        $messageType = 'success';
                    } else {
                        $message = 'Erreur lors de la mise à jour du statut.';
                        $messageType = 'error';
                    }
                }
                break;

            case 'delete_contact':
                if (isset($_POST['contact_id'])) {
                    $result = deleteContact($_POST['contact_id']);
                    if ($result) {
                        $message = 'Message supprimé avec succès.';
                        $messageType = 'success';
                    } else {
                        $message = 'Erreur lors de la suppression du message.';
                        $messageType = 'error';
                    }
                }
                break;

            case 'delete_product':
                if (isset($_POST['product_id'])) {
                    $result = deleteProduct($_POST['product_id']);
                    if ($result) {
                        $message = 'Produit supprimé avec succès.';
                        $messageType = 'success';
                    } else {
                        $message = 'Erreur lors de la suppression du produit.';
                        $messageType = 'error';
                    }
                }
                break;

            case 'update_product':
                if (isset($_POST['product_id'])) {
                    $data = [
                        'nom' => $_POST['nom'] ?? '',
                        'prix' => $_POST['prix'] ?? 0,
                        'categorie' => $_POST['categorie'] ?? '',
                        'stock' => $_POST['stock'] ?? 0,
                        'description' => $_POST['description'] ?? ''
                    ];

                    // Gestion de l'upload d'image
                    $imagePath = null;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = '../assets/uploads/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $fileName = uniqid('product_') . '_' . basename($_FILES['image']['name']);
                        $uploadFile = $uploadDir . $fileName;

                        // Vérifier le type de fichier
                        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        $fileType = $_FILES['image']['type'];

                        if (in_array($fileType, $allowedTypes)) {
                            // Vérifier la taille du fichier (5MB max)
                            if ($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                                    $imagePath = 'uploads/' . $fileName;

                                    // Supprimer l'ancienne image si elle existe
                                    $oldProduct = getProductById($_POST['product_id']);
                                    if ($oldProduct && !empty($oldProduct['image_path'])) {
                                        $oldImagePath = '../assets/' . $oldProduct['image_path'];
                                        if (file_exists($oldImagePath)) {
                                            unlink($oldImagePath);
                                        }
                                    }
                                } else {
                                    $message = 'Erreur lors de l\'upload de l\'image.';
                                    $messageType = 'error';
                                    break;
                                }
                            } else {
                                $message = 'L\'image est trop volumineuse (maximum 5MB).';
                                $messageType = 'error';
                                break;
                            }
                        } else {
                            $message = 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WebP.';
                            $messageType = 'error';
                            break;
                        }
                    }

                    if ($imagePath !== null) {
                        $data['image_path'] = $imagePath;
                    }

                    $result = updateProduct($_POST['product_id'], $data);
                    if ($result) {
                        $message = 'Produit mis à jour avec succès.';
                        $messageType = 'success';
                    } else {
                        $message = 'Erreur lors de la mise à jour du produit.';
                        $messageType = 'error';
                    }
                }
                break;

            case 'add_product':
                if (isset($_POST['nom']) && isset($_POST['prix'])) {
                    $data = [
                        'nom' => $_POST['nom'] ?? '',
                        'prix' => $_POST['prix'] ?? 0,
                        'categorie' => $_POST['categorie'] ?? '',
                        'stock' => $_POST['stock'] ?? 0,
                        'description' => $_POST['description'] ?? ''
                    ];

                    // Gestion de l'upload d'image
                    $imagePath = null;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $uploadDir = '../assets/uploads/';
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $fileName = uniqid('product_') . '_' . basename($_FILES['image']['name']);
                        $uploadFile = $uploadDir . $fileName;

                        // Vérifier le type de fichier
                        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        $fileType = $_FILES['image']['type'];

                        if (in_array($fileType, $allowedTypes)) {
                            // Vérifier la taille du fichier (5MB max)
                            if ($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                                    $imagePath = 'uploads/' . $fileName;
                                } else {
                                    $message = 'Erreur lors de l\'upload de l\'image.';
                                    $messageType = 'error';
                                    break;
                                }
                            } else {
                                $message = 'L\'image est trop volumineuse (maximum 5MB).';
                                $messageType = 'error';
                                break;
                            }
                        } else {
                            $message = 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WebP.';
                            $messageType = 'error';
                            break;
                        }
                    }

                    if ($imagePath !== null) {
                        $data['image_path'] = $imagePath;
                    }

                    $result = addProduct($data);
                    if ($result) {
                        $message = 'Produit ajouté avec succès.';
                        $messageType = 'success';
                    } else {
                        $message = 'Erreur lors de l\'ajout du produit.';
                        $messageType = 'error';
                    }
                }
                break;
        }
    }
}

// Récupérer les messages de session
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'] ?? 'info';
    unset($_SESSION['message'], $_SESSION['messageType']);
}

// FONCTIONS UTILITAIRES

// GESTION DES DEVIS
function getAllDevis()
{
    global $bdd;
    try {
        $sql = "SELECT * FROM devis ORDER BY created_at DESC";
        $stmt = $bdd->query($sql);
        if (!$stmt) {
            error_log("Erreur d'exécution SQL: " . print_r($bdd->errorInfo(), true));
            return [];
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération des devis: ' . $e->getMessage());
        return [];
    }
}

function countDevis()
{
    global $bdd;
    try {
        $sql = "SELECT COUNT(*) as total FROM devis";
        $stmt = $bdd->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    } catch (PDOException $e) {
        error_log('Erreur lors du comptage des devis: ' . $e->getMessage());
        return 0;
    }
}

function getDevisById($id)
{
    global $bdd;
    try {
        $stmt = $bdd->prepare("SELECT * FROM devis WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération du devis: ' . $e->getMessage());
        return null;
    }
}

function updateDevisStatus($id, $statut)
{
    global $bdd;
    try {
        $stmt = $bdd->prepare("UPDATE devis SET statut = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$statut, $id]);
    } catch (PDOException $e) {
        error_log('Erreur lors de la mise à jour du statut: ' . $e->getMessage());
        return false;
    }
}

function deleteDevis($id)
{
    global $bdd;
    try {
        // Récupérer les images avant suppression pour les supprimer du serveur
        $devis = getDevisById($id);
        if ($devis && !empty($devis['images'])) {
            $images = json_decode($devis['images'], true);
            if (is_array($images)) {
                foreach ($images as $image) {
                    $cheminImage = '../assets/uploads/' . $image;
                    if (file_exists($cheminImage)) {
                        unlink($cheminImage);
                    }
                }
            }
        }
        $stmt = $bdd->prepare("DELETE FROM devis WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur lors de la suppression du devis: ' . $e->getMessage());
        return false;
    }
}

function getDevisStats()
{
    global $bdd;
    try {
        $stats = [];
        $stmt = $bdd->query("SELECT COUNT(*) as total FROM devis");
        $stats['total'] = $stmt->fetch()['total'];
        $stmt = $bdd->query("SELECT statut, COUNT(*) as count FROM devis GROUP BY statut");
        $stats['par_statut'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $stmt = $bdd->query("SELECT COUNT(*) as count FROM devis WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stats['cette_semaine'] = $stmt->fetch()['count'];
        $stmt = $bdd->query("SELECT COUNT(*) as count FROM devis WHERE DATE(created_at) = CURDATE()");
        $stats['aujourdhui'] = $stmt->fetch()['count'];
        return $stats;
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
        return ['total' => 0, 'par_statut' => [], 'cette_semaine' => 0, 'aujourdhui' => 0];
    }
}

// GESTION DES CONTACTS
function getAllContacts()
{
    global $bdd;
    try {
        $sql = "SELECT * FROM messages_contact ORDER BY created_at DESC";
        $stmt = $bdd->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération des contacts: ' . $e->getMessage());
        return [];
    }
}

function countContacts()
{
    global $bdd;
    try {
        $sql = "SELECT COUNT(*) as total FROM messages_contact";
        $stmt = $bdd->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    } catch (PDOException $e) {
        error_log('Erreur lors du comptage des contacts: ' . $e->getMessage());
        return 0;
    }
}

function getContactById($id)
{
    global $bdd;
    try {
        $stmt = $bdd->prepare("SELECT * FROM messages_contact WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération du contact: ' . $e->getMessage());
        return null;
    }
}

function updateContactStatus($id, $statut)
{
    global $bdd;
    try {
        $stmt = $bdd->prepare("UPDATE messages_contact SET statut = ? WHERE id = ?");
        return $stmt->execute([$statut, $id]);
    } catch (PDOException $e) {
        error_log('Erreur lors de la mise à jour du statut: ' . $e->getMessage());
        return false;
    }
}

function deleteContact($id)
{
    global $bdd;
    try {
        $stmt = $bdd->prepare("DELETE FROM messages_contact WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur lors de la suppression du contact: ' . $e->getMessage());
        return false;
    }
}

function getContactStats()
{
    global $bdd;
    try {
        $stats = [];
        $stmt = $bdd->query("SELECT COUNT(*) as total FROM messages_contact");
        $stats['total'] = $stmt->fetch()['total'];
        $stmt = $bdd->query("SELECT statut, COUNT(*) as count FROM messages_contact GROUP BY statut");
        $stats['par_statut'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $stmt = $bdd->query("SELECT COUNT(*) as count FROM messages_contact WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stats['cette_semaine'] = $stmt->fetch()['count'];
        $stmt = $bdd->query("SELECT COUNT(*) as count FROM messages_contact WHERE DATE(created_at) = CURDATE()");
        $stats['aujourdhui'] = $stmt->fetch()['count'];
        return $stats;
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
        return ['total' => 0, 'par_statut' => [], 'cette_semaine' => 0, 'aujourdhui' => 0];
    }
}

// GESTION DE LA GALERIE
function getAllGalleryImages()
{
    global $bdd;
    try {
        $sql = "SELECT * FROM galerie ORDER BY categorie, ordre ASC";
        $stmt = $bdd->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération des images: ' . $e->getMessage());
        return [];
    }
}

function countGalleryImages()
{
    global $bdd;
    try {
        $sql = "SELECT COUNT(*) as total FROM galerie";
        $stmt = $bdd->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    } catch (PDOException $e) {
        error_log('Erreur lors du comptage des images: ' . $e->getMessage());
        return 0;
    }
}

// GESTION DES PRODUITS (BOUTIQUE)
function getAllProducts()
{
    global $bdd;
    try {
        $sql = "SELECT * FROM produits ORDER BY created_at DESC";
        $stmt = $bdd->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération des produits: ' . $e->getMessage());
        return [];
    }
}

function countProducts()
{
    global $bdd;
    try {
        $sql = "SELECT COUNT(*) as total FROM produits";
        $stmt = $bdd->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    } catch (PDOException $e) {
        error_log('Erreur lors du comptage des produits: ' . $e->getMessage());
        return 0;
    }
}

function getProductById($id)
{
    global $bdd;
    try {
        $stmt = $bdd->prepare("SELECT * FROM produits WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération du produit: ' . $e->getMessage());
        return null;
    }
}

function updateProduct($id, $data)
{
    global $bdd;
    try {
        $sql = "UPDATE produits SET nom = ?, description = ?, prix = ?, categorie = ?, stock = ?, updated_at = CURRENT_TIMESTAMP";
        $params = [$data['nom'], $data['description'], $data['prix'], $data['categorie'], $data['stock']];

        if (isset($data['image_path'])) {
            $sql .= ", image_path = ?";
            $params[] = $data['image_path'];
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $bdd->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log('Erreur lors de la mise à jour du produit: ' . $e->getMessage());
        return false;
    }
}

function deleteProduct($id)
{
    global $bdd;
    try {
        $stmt = $bdd->prepare("DELETE FROM produits WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur lors de la suppression du produit: ' . $e->getMessage());
        return false;
    }
}

function addProduct($data)
{
    global $bdd;
    try {
        // Vérifier d'abord si la table existe
        $checkTable = $bdd->query("SHOW TABLES LIKE 'produits'");
        if ($checkTable->rowCount() == 0) {
            error_log('Table produits n\'existe pas');
            return false;
        }

        $sql = "INSERT INTO produits (nom, description, prix, categorie, stock, image, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $stmt = $bdd->prepare($sql);
        $result = $stmt->execute([
            $data['nom'],
            $data['description'] ?? '',
            $data['prix'],
            $data['categorie'] ?? '',
            $data['stock'] ?? 0,
            $data['image_path'] ?? null
        ]);

        if ($result) {
            error_log('Produit ajouté avec succès, ID: ' . $bdd->lastInsertId());
            return true;
        } else {
            error_log('Échec de l\'exécution de la requête INSERT');
            return false;
        }
    } catch (PDOException $e) {
        error_log('Erreur PDO lors de l\'ajout du produit: ' . $e->getMessage());
        return false;
    }
}

// PARAMÈTRES DE PAGINATION
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['per_page']) ? min(100, max(10, intval($_GET['per_page']))) : 20;

// Gestion de l'action "view" pour afficher les détails
$viewDevis = null;
$viewContact = null;
if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
    if ($section === 'devis') {
        $viewDevis = getDevisById(intval($_GET['id']));
    } elseif ($section === 'contacts') {
        $viewContact = getContactById(intval($_GET['id']));
    }
}

// Récupérer les données selon la section
$devis = ($section === 'devis') ? getAllDevis() : [];
$totalDevis = ($section === 'devis') ? countDevis() : 0;
$statsDevis = ($section === 'devis') ? getDevisStats() : [];

$contacts = ($section === 'contacts') ? getAllContacts() : [];
$totalContacts = ($section === 'contacts') ? countContacts() : 0;
$statsContacts = ($section === 'contacts') ? getContactStats() : [];

$galleryImages = ($section === 'galerie') ? getAllGalleryImages() : [];
$totalGalleryImages = ($section === 'galerie') ? countGalleryImages() : 0;

$products = ($section === 'boutique') ? getAllProducts() : [];
$totalProducts = ($section === 'boutique') ? countProducts() : 0;

// Initialiser les variables de contrôle d'affichage
$showProductAddForm = ($section === 'boutique' && isset($_GET['action']) && $_GET['action'] === 'add');

// Gestion de l'affichage des détails pour les contacts (pour le modal AJAX)
if ($section === 'contacts' && isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
    $viewContact = getContactById(intval($_GET['id']));
    if ($viewContact) {
        // Afficher seulement les détails du contact pour le modal AJAX
        ?>
<div class="devis-details-section">
    <div class="details-header">
        <h2>📧 Message de
            <?php echo htmlspecialchars($viewContact['nom']); ?>
        </h2>
        <p>Envoyé le
            <?php echo date('d/m/Y à H:i', strtotime($viewContact['created_at'])); ?>
        </p>
    </div>

    <div class="details-content">
        <!-- Informations de l'expéditeur -->
        <div class="details-section">
            <h3>👤 Informations de l'expéditeur</h3>
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Nom complet:</div>
                    <div class="detail-value">
                        <?php echo htmlspecialchars($viewContact['nom']); ?>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email:</div>
                    <div class="detail-value">
                        <a href="mailto:<?php echo htmlspecialchars($viewContact['email']); ?>"
                            style="color: var(--wood-light); text-decoration: none;">
                            <?php echo htmlspecialchars($viewContact['email']); ?>
                        </a>
                    </div>
                </div>
                <?php if (!empty($viewContact['telephone'])): ?>
                <div class="detail-item">
                    <div class="detail-label">Téléphone:</div>
                    <div class="detail-value">
                        <a href="tel:<?php echo htmlspecialchars($viewContact['telephone']); ?>"
                            style="color: var(--wood-light); text-decoration: none;">
                            <?php echo htmlspecialchars($viewContact['telephone']); ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                <div class="detail-item">
                    <div class="detail-label">Sujet:</div>
                    <div class="detail-value">
                        <?php echo htmlspecialchars($viewContact['sujet']); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message -->
        <div class="details-section">
            <h3>💬 Message</h3>
            <div class="detail-item">
                <div class="detail-value description-text">
                    <?php echo nl2br(htmlspecialchars($viewContact['message'])); ?>
                </div>
            </div>
        </div>

        <!-- Métadonnées -->
        <div class="details-section">
            <h3>📊 Informations techniques</h3>
            <div class="metadata-grid">
                <div class="meta-item">
                    <span>Statut actuel:</span>
                    <span
                        class="status-badge status-<?php echo $viewContact['statut'] === 'nouveau' ? 'nouveau' : 'lu'; ?>">
                        <?php echo htmlspecialchars($viewContact['statut']); ?>
                    </span>
                </div>
                <div class="meta-item">
                    <span>Date d'envoi:</span>
                    <span><?php echo date('d/m/Y à H:i:s', strtotime($viewContact['created_at'])); ?></span>
                </div>
                <div class="meta-item">
                    <span>ID du message:</span>
                    <span>#<?php echo htmlspecialchars($viewContact['id']); ?></span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="details-actions">
            <button class="btn-action btn-edit"
                onclick="changeContactStatus(<?php echo $viewContact['id']; ?>, '<?php echo $viewContact['statut']; ?>')">
                ✏️ Changer le statut
            </button>
            <button class="btn-action btn-delete"
                onclick="deleteContact(<?php echo $viewContact['id']; ?>)">
                🗑️ Supprimer ce message
            </button>
        </div>
    </div>
</div>
<?php
        exit; // Sortir pour éviter d'afficher le reste de la page
    }
}

// Gestion de l'ajout de produit
if ($section === 'boutique' && isset($_GET['action']) && $_GET['action'] === 'add') {
    $showProductAddForm = ($section === 'boutique' && isset($_GET['action']) && $_GET['action'] === 'add');
}

// Gestion de l'affichage des détails pour les produits (pour le modal AJAX)
if ($section === 'boutique' && isset($_GET['action']) && isset($_GET['id'])) {
    $productId = intval($_GET['id']);
    $product = getProductById($productId);

    if ($product) {
        if ($_GET['action'] === 'view') {
            // Afficher les détails du produit
            ?>
<div class="product-details-section">
    <div class="details-header">
        <h2>🛍️ Produit:
            <?php echo htmlspecialchars($product['nom']); ?>
        </h2>
        <p>ID:
            #<?php echo htmlspecialchars($product['id']); ?>
            | Créé le
            <?php echo date('d/m/Y', strtotime($product['created_at'])); ?>
        </p>
    </div>

    <div class="details-content">
        <!-- Informations générales -->
        <div class="details-section">
            <h3>📦 Informations générales</h3>
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Nom du produit:</div>
                    <div class="detail-value">
                        <?php echo htmlspecialchars($product['nom']); ?>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Prix:</div>
                    <div class="detail-value">
                        <?php echo htmlspecialchars($product['prix']); ?>€
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Catégorie:</div>
                    <div class="detail-value">
                        <?php echo htmlspecialchars($product['categorie'] ?? 'Non catégorisé'); ?>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Stock:</div>
                    <div class="detail-value">
                        <?php echo htmlspecialchars($product['stock'] ?? 0); ?>
                        unités
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <?php if (!empty($product['description'])): ?>
        <div class="details-section">
            <h3>📝 Description</h3>
            <div class="detail-item">
                <div class="detail-value description-text">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Image du produit -->
        <?php if (!empty($product['image'])): ?>
        <div class="details-section">
            <h3>🖼️ Image du produit</h3>
            <div class="detail-item">
                <img src="../assets/<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['nom']); ?>"
                    style="max-width: 300px; max-height: 300px; border-radius: var(--rayon-lg); box-shadow: var(--ombre-moyenne);">
            </div>
        </div>
        <?php endif; ?>

        <!-- Métadonnées -->
        <div class="details-section">
            <h3>📊 Informations techniques</h3>
            <div class="metadata-grid">
                <div class="meta-item">
                    <span>ID du produit:</span>
                    <span>#<?php echo htmlspecialchars($product['id']); ?></span>
                </div>
                <div class="meta-item">
                    <span>Date de création:</span>
                    <span><?php echo date('d/m/Y à H:i:s', strtotime($product['created_at'])); ?></span>
                </div>
                <div class="meta-item">
                    <span>Dernière modification:</span>
                    <span><?php echo date('d/m/Y à H:i:s', strtotime($product['updated_at'] ?? $product['created_at'])); ?></span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="details-actions">
            <button class="btn-action btn-edit"
                onclick="editProduct(<?php echo $product['id']; ?>)">
                ✏️ Modifier ce produit
            </button>
            <button class="btn-action btn-delete"
                onclick="deleteProduct(<?php echo $product['id']; ?>)">
                🗑️ Supprimer ce produit
            </button>
        </div>
    </div>
</div>
<?php
            exit;
        } elseif ($_GET['action'] === 'edit') {
            // Afficher le formulaire d'édition
            ?>
<div class="product-edit-section">
    <form method="POST" action="?section=boutique" style="max-width: 600px; margin: 0 auto;">
        <input type="hidden" name="action" value="update_product">
        <input type="hidden" name="product_id"
            value="<?php echo htmlspecialchars($product['id']); ?>">

        <div style="margin-bottom: var(--espacement-lg);">
            <label for="nom"
                style="display: block; margin-bottom: var(--espacement-sm); font-weight: 600; color: var(--wood-dark);">Nom
                du produit *</label>
            <input type="text" id="nom" name="nom"
                value="<?php echo htmlspecialchars($product['nom']); ?>"
                required
                style="width: 100%; padding: var(--espacement-md); border: 1px solid var(--neutral-300); border-radius: var(--rayon-md); font-size: var(--font-size-base);">
        </div>

        <div style="margin-bottom: var(--espacement-lg);">
            <label for="prix"
                style="display: block; margin-bottom: var(--espacement-sm); font-weight: 600; color: var(--wood-dark);">Prix
                (FCFA) *</label>
            <input type="number" id="prix" name="prix"
                value="<?php echo htmlspecialchars($product['prix']); ?>"
                step="0.01" min="0" required
                style="width: 100%; padding: var(--espacement-md); border: 1px solid var(--neutral-300); border-radius: var(--rayon-md); font-size: var(--font-size-base);">
        </div>

        <div style="margin-bottom: var(--espacement-lg);">
            <label for="categorie"
                style="display: block; margin-bottom: var(--espacement-sm); font-weight: 600; color: var(--wood-dark);">Catégorie</label>
            <input type="text" id="categorie" name="categorie"
                value="<?php echo htmlspecialchars($product['categorie'] ?? ''); ?>"
                style="width: 100%; padding: var(--espacement-md); border: 1px solid var(--neutral-300); border-radius: var(--rayon-md); font-size: var(--font-size-base);">
        </div>

        <div style="margin-bottom: var(--espacement-lg);">
            <label for="stock"
                style="display: block; margin-bottom: var(--espacement-sm); font-weight: 600; color: var(--wood-dark);">Stock</label>
            <input type="number" id="stock" name="stock"
                value="<?php echo htmlspecialchars($product['stock'] ?? 0); ?>"
                min="0"
                style="width: 100%; padding: var(--espacement-md); border: 1px solid var(--neutral-300); border-radius: var(--rayon-md); font-size: var(--font-size-base);">
        </div>

        <div style="margin-bottom: var(--espacement-lg);">
            <label for="description"
                style="display: block; margin-bottom: var(--espacement-sm); font-weight: 600; color: var(--wood-dark);">Description</label>
            <textarea id="description" name="description" rows="4"
                style="width: 100%; padding: var(--espacement-md); border: 1px solid var(--neutral-300); border-radius: var(--rayon-md); font-size: var(--font-size-base); resize: vertical;"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
        </div>

        <div style="margin-bottom: var(--espacement-lg);">
            <label for="image"
                style="display: block; margin-bottom: var(--espacement-sm); font-weight: 600; color: var(--wood-dark);">Photo
                du produit</label>
            <input type="file" id="image" name="image" accept="image/*"
                style="width: 100%; padding: var(--espacement-md); border: 1px solid var(--neutral-300); border-radius: var(--rayon-md); font-size: var(--font-size-base);">
            <?php if (!empty($product['image_path'])): ?>
            <div style="margin-top: var(--espacement-sm);">
                <small style="color: var(--muted); font-size: var(--font-size-sm);">Image actuelle:</small><br>
                <img src="../assets/<?php echo htmlspecialchars($product['image_path']); ?>"
                    alt="Image actuelle"
                    style="max-width: 100px; max-height: 100px; border-radius: var(--rayon-md); margin-top: var(--espacement-xs);">
            </div>
            <?php endif; ?>
            <small
                style="color: var(--muted); font-size: var(--font-size-sm); display: block; margin-top: var(--espacement-xs);">Formats
                acceptés: JPG, PNG, GIF. Taille maximale: 5MB</small>
        </div>

        <div style="display: flex; gap: var(--espacement-md); justify-content: flex-end;">
            <button type="button" onclick="closeProductModal()" class="btn-action"
                style="background: var(--neutral-300); color: var(--neutral-700); padding: var(--espacement-md) var(--espacement-lg);">
                Annuler
            </button>
            <button type="submit" class="btn-action btn-edit">
                💾 Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
<?php
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../styles/boutique.css">
    <style>
        /* Layout principal */
        .admin-layout {
            display: flex;
            min-height: 100vh;
            background: var(--neutral-50);
        }

        /* Sidebar */
        .admin-sidebar {
            width: 250px;
            background: var(--neutral-100);
            border-right: 1px solid var(--neutral-200);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar-header {
            padding: var(--espacement-xl);
            background: linear-gradient(135deg, var(--wood-light) 0%, var(--wood-dark) 100%);
            color: var(--neutral-100);
            text-align: center;
            border-bottom: 1px solid var(--neutral-200);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--espacement-md);
            margin-bottom: var(--espacement-md);
        }

        .sidebar-logo img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .sidebar-logo span {
            font-size: var(--font-size-lg);
            font-weight: 700;
        }

        .sidebar-subtitle {
            font-size: var(--font-size-sm);
            opacity: 0.9;
        }

        .sidebar-nav {
            flex: 1;
            padding: var(--espacement-lg) 0;
        }

        .nav-section {
            margin-bottom: var(--espacement-xl);
        }

        .nav-section-title {
            padding: 0 var(--espacement-xl);
            font-size: var(--font-size-sm);
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: var(--espacement-md);
        }

        .nav-item {
            margin: 0 var(--espacement-md);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: var(--espacement-md);
            padding: var(--espacement-md) var(--espacement-lg);
            color: var(--neutral-700);
            text-decoration: none;
            border-radius: var(--rayon-md);
            transition: all var(--transition-rapide);
            font-weight: 500;
        }

        .nav-link:hover {
            background: var(--neutral-200);
            color: var(--wood-dark);
        }

        .nav-link.active {
            background: var(--wood-light);
            color: var(--neutral-100);
        }

        .nav-link i {
            font-size: var(--font-size-lg);
            width: 20px;
            text-align: center;
        }

        /* Contenu principal */
        .admin-main {
            flex: 1;
            margin-left: 250px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-header {
            background: var(--neutral-100);
            border-bottom: 1px solid var(--neutral-200);
            padding: var(--espacement-lg) var(--espacement-xl);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            margin: 0;
            color: var(--wood-dark);
            font-size: var(--font-size-2xl);
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            gap: var(--espacement-md);
            align-items: center;
        }

        .btn-logout {
            background: #ef4444;
            color: var(--neutral-100);
            border: none;
            padding: var(--espacement-sm) var(--espacement-lg);
            border-radius: var(--rayon-md);
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            transition: all var(--transition-rapide);
        }

        .btn-logout:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .main-content {
            flex: 1;
            padding: var(--espacement-xl);
            overflow-y: auto;
        }

        /* Messages */
        .message {
            padding: var(--espacement-lg);
            border-radius: var(--rayon-md);
            margin-bottom: var(--espacement-lg);
            font-weight: 500;
            border-left: 4px solid;
        }

        .message-success {
            background: rgba(34, 197, 94, 0.1);
            color: #15803d;
            border-left-color: #22c55e;
        }

        .message-error {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border-left-color: #ef4444;
        }

        .message-info {
            background: rgba(59, 130, 246, 0.1);
            color: #1d4ed8;
            border-left-color: #3b82f6;
        }

        /* Dashboard */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: var(--espacement-xl);
            margin-bottom: var(--espacement-2xl);
        }

        .dashboard-card {
            background: var(--neutral-100);
            padding: var(--espacement-xl);
            border-radius: var(--rayon-xl);
            box-shadow: var(--ombre-legere);
            border: 1px solid var(--neutral-200);
            transition: all var(--transition-normale);
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--ombre-moyenne);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: var(--espacement-md);
            margin-bottom: var(--espacement-lg);
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: var(--rayon-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-size-xl);
        }

        .card-icon.devis {
            background: rgba(59, 130, 246, 0.1);
            color: #1d4ed8;
        }

        .card-icon.contacts {
            background: rgba(34, 197, 94, 0.1);
            color: #15803d;
        }

        .card-icon.galerie {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .card-icon.boutique {
            background: rgba(168, 85, 247, 0.1);
            color: #7c3aed;
        }

        .card-title {
            font-size: var(--font-size-lg);
            font-weight: 600;
            color: var(--wood-dark);
            margin: 0;
        }

        .card-value {
            font-size: var(--font-size-3xl);
            font-weight: 700;
            color: var(--wood-light);
            margin-bottom: var(--espacement-sm);
        }

        .card-subtitle {
            color: var(--muted);
            font-size: var(--font-size-sm);
        }

        /* Tables */
        .data-table-container {
            background: var(--neutral-100);
            border-radius: var(--rayon-xl);
            box-shadow: var(--ombre-legere);
            overflow-x: auto;
            border: 1px solid var(--neutral-200);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .data-table th,
        .data-table td {
            padding: var(--espacement-lg);
            text-align: left;
            border-bottom: 1px solid var(--neutral-200);
        }

        .data-table th {
            background: var(--wood-dark);
            color: var(--neutral-100);
            font-weight: 600;
            font-size: var(--font-size-sm);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
        }

        .data-table tbody tr {
            transition: all var(--transition-rapide);
        }

        .data-table tbody tr:hover {
            background: var(--neutral-50);
        }

        /* Statuts */
        .status-badge {
            padding: var(--espacement-xs) var(--espacement-sm);
            border-radius: var(--rayon-lg);
            font-size: var(--font-size-xs);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .status-nouveau {
            background: rgba(59, 130, 246, 0.1);
            color: #1d4ed8;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .status-en-cours {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .status-termine {
            background: rgba(34, 197, 94, 0.1);
            color: #15803d;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .status-annule {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Actions */
        .action-buttons {
            display: flex;
            gap: var(--espacement-sm);
            align-items: center;
        }

        .btn-action {
            padding: var(--espacement-xs) var(--espacement-sm);
            border: none;
            border-radius: var(--rayon-md);
            cursor: pointer;
            font-size: var(--font-size-sm);
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: var(--espacement-xs);
            transition: all var(--transition-rapide);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-view {
            background: var(--wood-light);
            color: var(--neutral-100);
        }

        .btn-view:hover {
            background: var(--wood-dark);
            transform: translateY(-1px);
        }

        .btn-edit {
            background: var(--accent);
            color: var(--neutral-900);
        }

        .btn-edit:hover {
            background: #b8945a;
            transform: translateY(-1px);
        }

        .btn-delete {
            background: #ef4444;
            color: var(--neutral-100);
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        /* État vide */
        .empty-state {
            text-align: center;
            padding: var(--espacement-3xl);
            color: var(--muted);
        }

        .empty-state h3 {
            color: var(--wood-dark);
            margin-bottom: var(--espacement-md);
        }

        .empty-icon {
            font-size: var(--font-size-5xl);
            margin-bottom: var(--espacement-lg);
            opacity: 0.5;
        }

        /* Galerie */
        .galerie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: var(--espacement-lg);
        }

        .galerie-item {
            background: var(--neutral-100);
            border-radius: var(--rayon-lg);
            overflow: hidden;
            box-shadow: var(--ombre-legere);
            transition: all var(--transition-normale);
            border: 1px solid var(--neutral-200);
        }

        .galerie-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--ombre-moyenne);
        }

        .galerie-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .galerie-info {
            padding: var(--espacement-lg);
        }

        .galerie-title {
            font-weight: 600;
            color: var(--wood-dark);
            margin-bottom: var(--espacement-xs);
        }

        .galerie-category {
            background: var(--wood-light);
            color: var(--neutral-100);
            padding: var(--espacement-xs) var(--espacement-sm);
            border-radius: var(--rayon-sm);
            font-size: var(--font-size-xs);
            font-weight: 600;
            text-transform: uppercase;
            display: inline-block;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .admin-sidebar {
                width: 250px;
            }

            .admin-main {
                margin-left: 250px;
            }
        }

        @media (max-width: 768px) {
            .admin-layout {
                flex-direction: column;
            }

            .admin-sidebar {
                width: 100%;
                height: auto;
                position: static;
            }

            .admin-main {
                margin-left: 0;
            }

            .main-header {
                padding: var(--espacement-md);
            }

            .main-content {
                padding: var(--espacement-lg);
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .galerie-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        /* Contacts Cards */
        .contacts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: var(--espacement-lg);
        }

        .contact-card {
            background: var(--neutral-100);
            border-radius: var(--rayon-lg);
            box-shadow: var(--ombre-legere);
            border: 1px solid var(--neutral-200);
            transition: all var(--transition-normale);
            overflow: hidden;
        }

        .contact-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--ombre-moyenne);
        }

        .contact-card[data-status="nouveau"] {
            border-left: 4px solid #3b82f6;
        }

        .contact-header {
            padding: var(--espacement-lg);
            background: var(--neutral-50);
            border-bottom: 1px solid var(--neutral-200);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .contact-info {
            flex: 1;
        }

        .contact-name {
            margin: 0 0 var(--espacement-xs) 0;
            color: var(--wood-dark);
            font-size: var(--font-size-lg);
            font-weight: 600;
        }

        .contact-meta {
            display: flex;
            flex-direction: column;
            gap: var(--espacement-xs);
            font-size: var(--font-size-sm);
            color: var(--muted);
        }

        .contact-email {
            color: var(--wood-light);
            font-weight: 500;
        }

        .contact-date {
            color: var(--muted);
        }

        .contact-actions {
            flex-shrink: 0;
        }

        .contact-subject {
            padding: var(--espacement-lg);
            background: var(--neutral-100);
            border-bottom: 1px solid var(--neutral-200);
            font-size: var(--font-size-base);
            color: var(--wood-dark);
        }

        .contact-message {
            padding: var(--espacement-lg);
            color: var(--neutral-700);
            line-height: 1.5;
            font-size: var(--font-size-sm);
            background: var(--neutral-100);
        }

        .contact-footer {
            padding: var(--espacement-lg);
            background: var(--neutral-50);
            border-top: 1px solid var(--neutral-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .contact-details {
            display: flex;
            gap: var(--espacement-lg);
            font-size: var(--font-size-sm);
            color: var(--muted);
        }

        .contact-buttons {
            display: flex;
            gap: var(--espacement-sm);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: var(--neutral-100);
            margin: 5% auto;
            padding: 0;
            border-radius: var(--rayon-lg);
            width: 90%;
            max-width: 700px;
            box-shadow: var(--ombre-moyenne);
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            padding: var(--espacement-xl);
            border-bottom: 1px solid var(--neutral-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            color: var(--wood-dark);
            font-size: var(--font-size-xl);
        }

        .modal-close {
            font-size: var(--font-size-2xl);
            cursor: pointer;
            color: var(--muted);
            transition: all var(--transition-rapide);
        }

        .modal-close:hover {
            color: var(--wood-dark);
            transform: scale(1.2);
        }

        .modal-body {
            padding: var(--espacement-xl);
            max-height: 60vh;
            overflow-y: auto;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: var(--espacement-lg);
            margin-top: var(--espacement-xl);
            padding: var(--espacement-lg);
        }

        /* Contacts Table Styles */
        .contacts-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--espacement-lg);
            padding: var(--espacement-lg);
            background: var(--neutral-100);
            border-radius: var(--rayon-lg);
            border: 1px solid var(--neutral-200);
        }

        .toolbar-left {
            display: flex;
            gap: var(--espacement-lg);
        }

        .toolbar-right {
            display: flex;
            gap: var(--espacement-md);
            align-items: center;
        }

        .contacts-stats {
            display: flex;
            gap: var(--espacement-lg);
        }

        .stat-item {
            font-size: var(--font-size-sm);
            color: var(--muted);
        }

        .stat-item strong {
            color: var(--wood-dark);
            font-weight: 600;
        }

        .filter-select,
        .search-input {
            padding: var(--espacement-sm) var(--espacement-md);
            border: 1px solid var(--neutral-300);
            border-radius: var(--rayon-md);
            font-size: var(--font-size-sm);
            background: var(--neutral-100);
            color: var(--neutral-700);
        }

        .filter-select:focus,
        .search-input:focus {
            outline: none;
            border-color: var(--wood-light);
            box-shadow: 0 0 0 2px rgba(139, 115, 85, 0.1);
        }

        .bulk-selection-bar {
            background: var(--wood-light);
            color: var(--neutral-100);
            padding: var(--espacement-md) var(--espacement-lg);
            border-radius: var(--rayon-md);
            margin-bottom: var(--espacement-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .bulk-actions {
            display: flex;
            gap: var(--espacement-md);
        }

        .btn-bulk {
            background: rgba(255, 255, 255, 0.2);
            color: var(--neutral-100);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: var(--espacement-xs) var(--espacement-md);
            border-radius: var(--rayon-md);
            cursor: pointer;
            font-size: var(--font-size-sm);
            display: flex;
            align-items: center;
            gap: var(--espacement-xs);
            transition: all var(--transition-rapide);
        }

        .btn-bulk:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .btn-bulk.btn-danger {
            background: rgba(239, 68, 68, 0.8);
            border-color: rgba(239, 68, 68, 1);
        }

        .btn-bulk.btn-danger:hover {
            background: #dc2626;
        }

        .contacts-table-container {
            background: var(--neutral-100);
            border-radius: var(--rayon-lg);
            border: 1px solid var(--neutral-200);
            overflow: hidden;
            box-shadow: var(--ombre-legere);
        }

        .contacts-table {
            width: 100%;
            min-width: 1200px;
        }

        .table-header {
            display: grid;
            grid-template-columns: 200px 200px 1fr 120px 100px 120px;
            gap: var(--espacement-md);
            padding: var(--espacement-lg);
            background: var(--wood-dark);
            color: var(--neutral-100);
            font-weight: 600;
            font-size: var(--font-size-sm);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--neutral-200);
        }

        .table-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .table-row {
            display: grid;
            grid-template-columns: 200px 200px 1fr 120px 100px 120px;
            gap: var(--espacement-md);
            padding: var(--espacement-lg);
            border-bottom: 1px solid var(--neutral-200);
            transition: all var(--transition-rapide);
            cursor: pointer;
        }

        .table-row:hover {
            background: var(--neutral-50);
        }

        .table-row.row-unread {
            background: rgba(59, 130, 246, 0.02);
            border-left: 3px solid #3b82f6;
        }

        .table-row.row-unread:hover {
            background: rgba(59, 130, 246, 0.05);
        }

        .col-checkbox {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .col-checkbox input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .col-sender {
            display: flex;
            flex-direction: column;
            gap: var(--espacement-xs);
        }

        .sender-name {
            font-weight: 600;
            color: var(--wood-dark);
            font-size: var(--font-size-base);
        }

        .sender-email {
            color: var(--wood-light);
            font-size: var(--font-size-sm);
            font-weight: 500;
        }

        .sender-phone {
            color: var(--muted);
            font-size: var(--font-size-xs);
        }

        .col-subject {
            display: flex;
            align-items: center;
        }

        .subject-text {
            font-weight: 500;
            color: var(--wood-dark);
            font-size: var(--font-size-base);
        }

        .col-message {
            display: flex;
            align-items: center;
        }

        .message-preview {
            color: var(--neutral-700);
            font-size: var(--font-size-sm);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .col-date {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .date-text {
            font-weight: 600;
            color: var(--wood-dark);
            font-size: var(--font-size-sm);
        }

        .time-text {
            color: var(--muted);
            font-size: var(--font-size-xs);
        }

        .col-status {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .col-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--espacement-sm);
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: var(--rayon-md);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--font-size-lg);
            transition: all var(--transition-rapide);
            background: transparent;
            color: var(--muted);
        }

        .btn-icon:hover {
            background: var(--neutral-200);
            color: var(--wood-dark);
            transform: scale(1.1);
        }

        .btn-icon.btn-delete:hover {
            background: #fee2e2;
            color: #dc2626;
        }

        /* Responsive pour contacts */
        @media (max-width: 1200px) {
            .contacts-table {
                min-width: 1000px;
            }

            .table-header,
            .table-row {
                grid-template-columns: 150px 150px 1fr 100px 80px 100px;
                gap: var(--espacement-sm);
                padding: var(--espacement-md);
            }
        }

        @media (max-width: 768px) {
            .contacts-toolbar {
                flex-direction: column;
                gap: var(--espacement-md);
                align-items: stretch;
            }

            .toolbar-left,
            .toolbar-right {
                justify-content: center;
            }

            .contacts-table-container {
                overflow-x: auto;
            }

            .bulk-selection-bar {
                flex-direction: column;
                gap: var(--espacement-md);
                text-align: center;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Style pour la page d'ajout de produit */
        .product-add-section {
            background: var(--neutral-100);
            border-radius: var(--rayon-xl);
            box-shadow: var(--ombre-legere);
            border: 1px solid var(--neutral-200);
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }

        .product-add-section .details-header {
            background: linear-gradient(135deg, var(--wood-light) 0%, var(--wood-dark) 100%);
            color: var(--neutral-100);
            padding: var(--espacement-xl);
            text-align: center;
            border-bottom: 1px solid var(--neutral-200);
        }

        .product-add-section .details-header h2 {
            margin: 0;
            font-size: var(--font-size-2xl);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: var(--espacement-md);
        }

        .product-add-section .details-header a {
            display: inline-flex;
            align-items: center;
            gap: var(--espacement-sm);
            margin-top: var(--espacement-lg);
            color: var(--neutral-100);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--transition-rapide);
        }

        .product-add-section .details-header a:hover {
            opacity: 0.8;
            transform: translateX(-2px);
        }

        /* Formulaire d'ajout */
        .product-add-section form {
            padding: var(--espacement-2xl);
            max-width: 800px;
            margin: 0 auto;
        }

        .product-add-section .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--espacement-xl);
            margin-bottom: var(--espacement-xl);
        }

        .product-add-section .form-full {
            margin-bottom: var(--espacement-xl);
        }

        .product-add-section .form-group {
            margin-bottom: var(--espacement-lg);
        }

        .product-add-section label {
            display: block;
            margin-bottom: var(--espacement-sm);
            font-weight: 600;
            color: var(--wood-dark);
            font-size: var(--font-size-base);
        }

        .product-add-section label::after {
            content: ' *';
            color: #ef4444;
            font-weight: 700;
        }

        .product-add-section label:not([for="categorie"]):not([for="stock"]):not([for="description"])::after {
            content: '';
        }

        .product-add-section input[type="text"],
        .product-add-section input[type="number"],
        .product-add-section input[type="file"],
        .product-add-section textarea {
            width: 100%;
            padding: var(--espacement-md);
            border: 2px solid var(--neutral-300);
            border-radius: var(--rayon-lg);
            font-size: var(--font-size-base);
            transition: all var(--transition-rapide);
            background: var(--neutral-100);
            color: var(--wood-dark);
        }

        .product-add-section input[type="text"]:focus,
        .product-add-section input[type="number"]:focus,
        .product-add-section input[type="file"]:focus,
        .product-add-section textarea:focus {
            outline: none;
            border-color: var(--wood-light);
            box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.1);
            background: var(--neutral-50);
        }

        .product-add-section input[type="file"] {
            padding: var(--espacement-sm);
            cursor: pointer;
        }

        .product-add-section textarea {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .product-add-section .file-info {
            margin-top: var(--espacement-sm);
            font-size: var(--font-size-sm);
            color: var(--muted);
            font-style: italic;
        }

        .product-add-section .file-info strong {
            color: var(--wood-dark);
        }

        /* Actions du formulaire */
        .product-add-section .form-actions {
            display: flex;
            gap: var(--espacement-lg);
            justify-content: center;
            align-items: center;
            padding-top: var(--espacement-2xl);
            border-top: 2px solid var(--neutral-200);
            margin-top: var(--espacement-xl);
            background: linear-gradient(135deg, var(--neutral-50) 0%, var(--neutral-100) 100%);
            border-radius: 0 0 var(--rayon-xl) var(--rayon-xl);
            padding-bottom: var(--espacement-xl);
        }

        .product-add-section .form-actions .btn-action {
            padding: 1rem 1.5rem;
            font-size: 1.125rem;
            font-weight: 700;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            min-width: 180px;
            justify-content: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-family: inherit;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .product-add-section .form-actions .btn-action::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .product-add-section .form-actions .btn-action:hover::before {
            left: 100%;
        }

        .product-add-section .form-actions .btn-action:hover {
            transform: translateY(-3px);
            box-shadow: var(--ombre-moyenne);
        }

        .product-add-section .form-actions .btn-action:active {
            transform: translateY(-1px);
        }

        .product-add-section .form-actions .btn-cancel {
            background: linear-gradient(135deg, var(--neutral-200) 0%, var(--neutral-300) 100%);
            color: var(--neutral-800);
            border: 2px solid var(--neutral-300);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .product-add-section .form-actions .btn-cancel:hover {
            background: linear-gradient(135deg, var(--neutral-300) 0%, var(--neutral-400) 100%);
            border-color: var(--neutral-400);
            color: var(--neutral-900);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .product-add-section .form-actions .btn-cancel i {
            color: var(--neutral-600);
        }

        .product-add-section .form-actions .btn-submit {
            background: linear-gradient(135deg, var(--wood-light) 0%, var(--wood-dark) 100%);
            color: var(--neutral-100);
            border: 2px solid var(--wood-light);
            box-shadow: 0 4px 15px rgba(139, 115, 85, 0.3);
            position: relative;
            z-index: 1;
        }

        .product-add-section .form-actions .btn-submit:hover {
            background: linear-gradient(135deg, var(--wood-dark) 0%, #8b7355 100%);
            border-color: var(--wood-dark);
            box-shadow: 0 8px 25px rgba(139, 115, 85, 0.4);
            color: var(--neutral-50);
        }

        .product-add-section .form-actions .btn-submit i {
            color: var(--neutral-50);
            font-size: var(--font-size-xl);
        }

        /* Animation spéciale pour le bouton principal */
        .product-add-section .form-actions .btn-submit {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 4px 15px rgba(139, 115, 85, 0.3);
            }

            50% {
                box-shadow: 0 4px 20px rgba(139, 115, 85, 0.5);
            }

            100% {
                box-shadow: 0 4px 15px rgba(139, 115, 85, 0.3);
            }
        }

        .product-add-section .form-actions .btn-submit:hover {
            animation: none;
        }

        /* Responsive pour le formulaire */
        @media (max-width: 768px) {
            .product-add-section form {
                padding: var(--espacement-lg);
            }

            .product-add-section .form-grid {
                grid-template-columns: 1fr;
                gap: var(--espacement-lg);
            }

            .product-add-section .form-actions {
                flex-direction: column;
                gap: var(--espacement-md);
            }

            .product-add-section .form-actions .btn-action {
                width: 100%;
            }
        }

        /* Améliorations visuelles */
        .product-add-section .input-group {
            position: relative;
        }

        .product-add-section .input-icon {
            position: absolute;
            right: var(--espacement-md);
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: var(--font-size-lg);
        }

        .product-add-section .input-hint {
            margin-top: var(--espacement-xs);
            font-size: var(--font-size-sm);
            color: var(--muted);
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="../assets/ayoubdecor_logoo.png" alt="AYOUBDECOR">
                    <span>AYOUBDECOR</span>
                </div>
                <div class="sidebar-subtitle">Administration</div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Principal</div>
                    <div class="nav-item">
                        <a href="?section=dashboard"
                            class="nav-link <?php echo $section === 'dashboard' ? 'active' : ''; ?>">
                            <i class="bx bx-home"></i>Dashboard
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Gestion</div>
                    <div class="nav-item">
                        <a href="?section=devis"
                            class="nav-link <?php echo $section === 'devis' ? 'active' : ''; ?>">
                            <i class="bx bx-file"></i>Devis
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="?section=boutique"
                            class="nav-link <?php echo $section === 'boutique' ? 'active' : ''; ?>">
                            <i class="bx bx-shopping-bag"></i>Boutique
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="?section=galerie"
                            class="nav-link <?php echo $section === 'galerie' ? 'active' : ''; ?>">
                            <i class="bx bx-images"></i>Galerie
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="?section=contacts"
                            class="nav-link <?php echo $section === 'contacts' ? 'active' : ''; ?>">
                            <i class="bx bx-envelope"></i>Contacts
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="main-header">
                <h1 class="page-title">
                    <?php
                    switch ($section) {
                        case 'dashboard': echo 'Dashboard';
                            break;
                        case 'devis': echo 'Gestion des Devis';
                            break;
                        case 'boutique': echo 'Gestion de la Boutique';
                            break;
                        case 'galerie': echo 'Gestion de la Galerie';
                            break;
                        case 'contacts': echo 'Messages de Contact';
                            break;
                        default: echo 'Administration';
                    }
?>
                </h1>
                <div class="header-actions">
                    <a href="login.php?action=logout" class="btn-logout">
                        <i class="bx bx-log-out"></i>Déconnexion
                    </a>
                </div>
            </header>

            <div class="main-content">
                <?php if (!empty($message)): ?>
                <div
                    class="message message-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>

                <?php if ($section === 'dashboard'): ?>
                <!-- Dashboard -->
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon devis">
                                <i class="bx bx-file"></i>
                            </div>
                            <h3 class="card-title">Devis</h3>
                        </div>
                        <div class="card-value">
                            <?php echo $totalDevis; ?>
                        </div>
                        <div class="card-subtitle">Total des demandes</div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon contacts">
                                <i class="bx bx-envelope"></i>
                            </div>
                            <h3 class="card-title">Messages</h3>
                        </div>
                        <div class="card-value">
                            <?php echo $totalContacts; ?>
                        </div>
                        <div class="card-subtitle">Messages de contact</div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon galerie">
                                <i class="bx bx-images"></i>
                            </div>
                            <h3 class="card-title">Galerie</h3>
                        </div>
                        <div class="card-value">
                            <?php echo $totalGalleryImages; ?>
                        </div>
                        <div class="card-subtitle">Images dans la galerie</div>
                    </div>

                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-icon boutique">
                                <i class="bx bx-shopping-bag"></i>
                            </div>
                            <h3 class="card-title">Produits</h3>
                        </div>
                        <div class="card-value">
                            <?php echo $totalProducts; ?>
                        </div>
                        <div class="card-subtitle">Produits en boutique</div>
                    </div>
                </div>

                <?php elseif ($section === 'devis'): ?>
                <!-- Section Devis -->
                <?php if ($viewDevis): ?>
                <!-- Détails d'un devis -->
                <div class="devis-details-section">
                    <div class="details-header">
                        <h2>📋 Détails du devis
                            #<?php echo htmlspecialchars($viewDevis['id']); ?>
                        </h2>
                        <p>Demandé par
                            <?php echo htmlspecialchars($viewDevis['nom']); ?>
                            le
                            <?php echo date('d/m/Y à H:i', strtotime($viewDevis['created_at'])); ?>
                        </p>
                        <a href="?section=devis" class="btn-action btn-view" style="margin-top: var(--espacement-md);">
                            ← Retour à la liste
                        </a>
                    </div>
                    <div class="details-content">
                        <div class="details-section">
                            <h3>👤 Informations personnelles</h3>
                            <div class="details-grid">
                                <div class="detail-item">
                                    <div class="detail-label">Nom complet:</div>
                                    <div class="detail-value">
                                        <?php echo htmlspecialchars($viewDevis['nom']); ?>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Email:</div>
                                    <div class="detail-value">
                                        <?php echo htmlspecialchars($viewDevis['email']); ?>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Téléphone:</div>
                                    <div class="detail-value">
                                        <?php echo htmlspecialchars($viewDevis['telephone']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="details-section">
                            <h3>🏗️ Détails du projet</h3>
                            <div class="detail-item">
                                <div class="detail-value description-text">
                                    <?php echo nl2br(htmlspecialchars($viewDevis['description'])); ?>
                                </div>
                            </div>
                        </div>
                        <div class="details-actions">
                            <button class="btn-action btn-edit"
                                onclick="changeStatus(<?php echo $viewDevis['id']; ?>, '<?php echo $viewDevis['statut']; ?>')">✏️
                                Changer le statut</button>
                            <button class="btn-action btn-delete"
                                onclick="deleteDevis(<?php echo $viewDevis['id']; ?>)">🗑️
                                Supprimer</button>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <!-- Liste des devis -->
                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($devis)): ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <div class="empty-icon">📋</div>
                                        <h3>Aucun devis</h3>
                                        <p>Les demandes de devis apparaîtront ici.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($devis as $d): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($d['id']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($d['nom']); ?>
                                </td>
                                <td><span
                                        class="status-badge status-<?php echo str_replace([' ', '-'], ['', '-'], strtolower($d['statut'])); ?>"><?php echo htmlspecialchars($d['statut']); ?></span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($d['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-view"
                                            onclick="showDevisDetails(<?php echo $d['id']; ?>)">👁️
                                            Voir</button>
                                        <button class="btn-action btn-edit"
                                            onclick="changeStatus(<?php echo $d['id']; ?>, '<?php echo $d['statut']; ?>')">✏️
                                            Statut</button>
                                        <button class="btn-action btn-delete"
                                            onclick="deleteDevis(<?php echo $d['id']; ?>)">🗑️
                                            Supp.</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <?php elseif ($section === 'contacts'): ?>
                <!-- Section Contacts -->
                <?php if (empty($contacts)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📧</div>
                    <h3>Aucun message de contact</h3>
                    <p>Les nouveaux messages apparaîtront automatiquement ici.</p>
                </div>
                <?php else: ?>
                <!-- Toolbar avec statistiques et filtres -->
                <div class="contacts-toolbar">
                    <div class="toolbar-left">
                        <div class="contacts-stats">
                            <span class="stat-item">
                                <strong><?php echo count($contacts); ?></strong>
                                messages
                            </span>
                            <span class="stat-item">
                                <strong><?php echo count(array_filter($contacts, fn ($c) => $c['statut'] === 'nouveau')); ?></strong>
                                non lus
                            </span>
                        </div>
                    </div>
                    <div class="toolbar-right">
                        <select id="status-filter" onchange="filterContacts()" class="filter-select">
                            <option value="all">Tous les statuts</option>
                            <option value="nouveau">Nouveaux</option>
                            <option value="lu">Lus</option>
                        </select>
                        <input type="text" id="search-input" placeholder="Rechercher..." class="search-input"
                            onkeyup="searchContacts()">
                    </div>
                </div>

                <!-- Liste des messages -->
                <div class="contacts-table-container">
                    <div class="contacts-table">
                        <!-- En-têtes -->
                        <div class="table-header">
                            <div class="col-sender">Expéditeur</div>
                            <div class="col-subject">Sujet</div>
                            <div class="col-message">Message</div>
                            <div class="col-date">Date</div>
                            <div class="col-status">Statut</div>
                            <div class="col-actions">Actions</div>
                        </div>

                        <!-- Corps du tableau -->
                        <div class="table-body">
                            <?php foreach ($contacts as $contact): ?>
                            <div class="table-row <?php echo $contact['statut'] === 'nouveau' ? 'row-unread' : 'row-read'; ?>"
                                data-status="<?php echo htmlspecialchars($contact['statut']); ?>"
                                data-id="<?php echo $contact['id']; ?>">
                                <div class="col-sender"
                                    onclick="showContactDetails(<?php echo $contact['id']; ?>)">
                                    <div class="sender-name">
                                        <?php echo htmlspecialchars($contact['nom']); ?>
                                    </div>
                                    <div class="sender-email">
                                        <?php echo htmlspecialchars($contact['email']); ?>
                                    </div>
                                    <?php if (!empty($contact['telephone'])): ?>
                                    <div class="sender-phone">
                                        <?php echo htmlspecialchars($contact['telephone']); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-subject"
                                    onclick="showContactDetails(<?php echo $contact['id']; ?>)">
                                    <div class="subject-text">
                                        <?php echo htmlspecialchars($contact['sujet']); ?>
                                    </div>
                                </div>

                                <div class="col-message"
                                    onclick="showContactDetails(<?php echo $contact['id']; ?>)">
                                    <div class="message-preview">
                                        <?php
                    $message = htmlspecialchars($contact['message']);
                                echo strlen($message) > 100 ? substr($message, 0, 100) . '...' : $message;
                                ?>
                                    </div>
                                </div>

                                <div class="col-date"
                                    onclick="showContactDetails(<?php echo $contact['id']; ?>)">
                                    <div class="date-text">
                                        <?php echo date('d/m/Y', strtotime($contact['created_at'])); ?>
                                    </div>
                                    <div class="time-text">
                                        <?php echo date('H:i', strtotime($contact['created_at'])); ?>
                                    </div>
                                </div>

                                <div class="col-status">
                                    <span
                                        class="status-badge status-<?php echo $contact['statut'] === 'nouveau' ? 'nouveau' : 'lu'; ?>">
                                        <?php echo htmlspecialchars($contact['statut']); ?>
                                    </span>
                                </div>

                                <div class="col-actions">
                                    <button class="btn-icon"
                                        onclick="showContactDetails(<?php echo $contact['id']; ?>)"
                                        title="Voir les détails">
                                        <i class="bx bx-show"></i>
                                    </button>
                                    <button class="btn-icon"
                                        onclick="changeContactStatus(<?php echo $contact['id']; ?>, '<?php echo $contact['statut']; ?>')"
                                        title="Changer le statut">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button class="btn-icon btn-delete"
                                        onclick="deleteContact(<?php echo $contact['id']; ?>)"
                                        title="Supprimer">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <?php endif; ?>

                <!-- Modal pour détails complets -->
                <div id="contact-modal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 id="modal-title">Détails du message</h3>
                            <span class="modal-close" onclick="closeContactModal()">&times;</span>
                        </div>
                        <div id="modal-body" class="modal-body">
                            <!-- Contenu chargé dynamiquement -->
                        </div>
                    </div>
                </div>

                <?php elseif ($section === 'galerie'): ?>
                <!-- Section Galerie -->
                <div style="margin-bottom: var(--espacement-lg);">
                    <a href="ajouter_image.php" class="btn-action btn-edit"
                        style="background: var(--wood-light); color: var(--neutral-100); padding: var(--espacement-md) var(--espacement-lg); text-decoration: none;">
                        ➕ Ajouter une image
                    </a>
                </div>

                <?php if (empty($galleryImages)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🖼️</div>
                    <h3>Aucune image</h3>
                    <p>La galerie est vide. Ajoutez des images pour commencer.</p>
                </div>
                <?php else: ?>
                <div class="galerie-grid">
                    <?php foreach ($galleryImages as $image): ?>
                    <div class="galerie-item">
                        <img src="../assets/<?php echo htmlspecialchars($image['image_path']); ?>"
                            alt="<?php echo htmlspecialchars($image['titre']); ?>"
                            class="galerie-image">
                        <div class="galerie-info">
                            <h4 class="galerie-title">
                                <?php echo htmlspecialchars($image['titre']); ?>
                            </h4>
                            <span
                                class="galerie-category"><?php echo htmlspecialchars($image['categorie']); ?></span>
                            <div style="margin-top: var(--espacement-md);">
                                <button class="btn-action btn-edit"
                                    onclick="editGalleryImage(<?php echo $image['id']; ?>)">✏️
                                    Modifier</button>
                                <button class="btn-action btn-delete"
                                    onclick="deleteGalleryImage(<?php echo $image['id']; ?>)">🗑️
                                    Supprimer</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <?php elseif ($section === 'boutique'): ?>
                <!-- Section Boutique -->
                <?php if ($showProductAddForm): ?>
                <!-- Formulaire d'ajout de produit -->
                <div class="product-add-section">
                    <div class="details-header">
                        <h2>➕ Ajouter un nouveau produit</h2>
                        <a href="?section=boutique" class="btn-action btn-view"
                            style="margin-top: var(--espacement-md);">
                            ← Retour à la liste
                        </a>
                    </div>

                    <form method="POST" action="?section=boutique" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add_product">

                        <!-- Informations principales -->
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nom">Nom du produit</label>
                                <div class="input-group">
                                    <input type="text" id="nom" name="nom" required
                                        placeholder="Ex: Table en bois massif">
                                    <i class="bx bx-package input-icon"></i>
                                </div>
                                <div class="input-hint">Nom affiché dans la boutique</div>
                            </div>

                            <div class="form-group">
                                <label for="prix">Prix (€)</label>
                                <div class="input-group">
                                    <input type="number" id="prix" name="prix" step="0.01" min="0" required
                                        placeholder="0.00">
                                    <i class="bx bx-euro input-icon"></i>
                                </div>
                                <div class="input-hint">Prix TTC en euros</div>
                            </div>
                        </div>

                        <!-- Catégorie et stock -->
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="categorie">Catégorie</label>
                                <div class="input-group">
                                    <input type="text" id="categorie" name="categorie"
                                        placeholder="Ex: Mobilier, Décoration...">
                                    <i class="bx bx-category input-icon"></i>
                                </div>
                                <div class="input-hint">Optionnel - pour organiser les produits</div>
                            </div>

                            <div class="form-group">
                                <label for="stock">Stock</label>
                                <div class="input-group">
                                    <input type="number" id="stock" name="stock" min="0" placeholder="0">
                                    <i class="bx bx-cube input-icon"></i>
                                </div>
                                <div class="input-hint">Quantité disponible (0 = hors stock)</div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-full">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" rows="4"
                                    placeholder="Décrivez votre produit en détail..."></textarea>
                                <div class="input-hint">Description détaillée du produit, matériaux, dimensions, etc.
                                </div>
                            </div>
                        </div>

                        <!-- Photo du produit -->
                        <div class="form-full">
                            <div class="form-group">
                                <label for="image">Photo du produit</label>
                                <input type="file" id="image" name="image" accept="image/*" required>
                                <div class="file-info">
                                    <strong>Formats acceptés:</strong> JPG, PNG, GIF, WebP<br>
                                    <strong>Taille maximale:</strong> 5MB<br>
                                    <strong>Conseil:</strong> Utilisez une image de haute qualité (minimum 800x600px)
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="form-actions">
                            <a href="?section=boutique" class="btn-action btn-cancel">
                                <i class="bx bx-x"></i>Annuler
                            </a>
                            <button type="submit" class="btn-action btn-submit">
                                <i class="bx bx-save"></i>Ajouter le produit
                            </button>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <!-- Liste des produits -->
                <div style="margin-bottom: var(--espacement-lg);">
                    <a href="?section=boutique&action=add" class="btn-action btn-edit"
                        style="background: var(--wood-light); color: var(--neutral-100); padding: var(--espacement-md) var(--espacement-lg); text-decoration: none;">
                        ➕ Ajouter un produit
                    </a>
                </div>

                <div class="data-table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Prix</th>
                                <th>Catégorie</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div class="empty-icon">🛍️</div>
                                        <h3>Aucun produit</h3>
                                        <p>Les produits de la boutique apparaîtront ici.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($products as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['id']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($p['nom']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($p['prix']); ?>€
                                </td>
                                <td><?php echo htmlspecialchars($p['categorie'] ?? 'Non catégorisé'); ?>
                                </td>
                                <td><?php echo htmlspecialchars($p['stock'] ?? 0); ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action btn-view"
                                            onclick="viewProduct(<?php echo $p['id']; ?>)">👁️
                                            Voir</button>
                                        <button class="btn-action btn-edit"
                                            onclick="editProduct(<?php echo $p['id']; ?>)">✏️
                                            Modifier</button>
                                        <button class="btn-action btn-delete"
                                            onclick="deleteProduct(<?php echo $p['id']; ?>)">🗑️
                                            Supprimer</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php endif; ?>

                <?php endif; ?>

                <!-- Modal pour les produits (disponible sur toutes les pages) -->
                <div id="product-modal" class="modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3 id="product-modal-title">Détails du produit</h3>
                            <span class="modal-close" onclick="closeProductModal()">&times;</span>
                        </div>
                        <div id="product-modal-body" class="modal-body">
                            <!-- Contenu chargé dynamiquement -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function showDevisDetails(devisId) {
            window.location.href = '?section=devis&action=view&id=' + devisId;
        }

        function changeStatus(devisId, currentStatus) {
            const newStatus = prompt('Nouveau statut:', currentStatus);
            if (newStatus && newStatus !== currentStatus) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="devis_id" value="${devisId}">
                    <input type="hidden" name="statut" value="${newStatus}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteDevis(devisId) {
            if (confirm('Supprimer ce devis ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_devis">
                    <input type="hidden" name="devis_id" value="${devisId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function showContactDetails(contactId) {
            // Charger les détails via AJAX/fetch
            fetch('admin.php?section=contacts&action=view&id=' + contactId)
                .then(response => response.text())
                .then(html => {
                    // Extraire le contenu du contact depuis la réponse
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const contactDetails = doc.querySelector('.devis-details-section');

                    if (contactDetails) {
                        // Créer le contenu du modal
                        const modal = document.getElementById('contact-modal');
                        const modalTitle = document.getElementById('modal-title');
                        const modalBody = document.getElementById('modal-body');

                        // Extraire les informations du contact
                        const name = contactDetails.querySelector('h2')?.textContent || 'Message de contact';
                        const content = contactDetails.querySelector('.details-content')?.innerHTML ||
                            'Contenu non disponible';

                        modalTitle.textContent = name;
                        modalBody.innerHTML = content;
                        modal.style.display = 'block';

                        // Fermer le modal si on clique en dehors
                        modal.addEventListener('click', function(e) {
                            if (e.target === modal) {
                                closeContactModal();
                            }
                        });
                    } else {
                        alert('Impossible de charger les détails du message.');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement:', error);
                    alert('Erreur lors du chargement des détails.');
                });
        }

        function closeContactModal() {
            const modal = document.getElementById('contact-modal');
            modal.style.display = 'none';
        }

        function changeContactStatus(contactId, currentStatus) {
            const newStatus = prompt('Nouveau statut:', currentStatus);
            if (newStatus && newStatus !== currentStatus) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_contact_status">
                    <input type="hidden" name="contact_id" value="${contactId}">
                    <input type="hidden" name="statut" value="${newStatus}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteContact(contactId) {
            if (confirm('Supprimer ce message ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_contact">
                    <input type="hidden" name="contact_id" value="${contactId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function editGalleryImage(imageId) {
            // TODO: Implement edit functionality
            alert('Fonctionnalité de modification à implémenter');
        }

        function deleteGalleryImage(imageId) {
            if (confirm('Supprimer cette image ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../src/controllers/traitementGalerie.php';
                form.innerHTML = `
                    <input type="hidden" name="action" value="supprimer_image">
                    <input type="hidden" name="id" value="${imageId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Fonctions pour la gestion des produits (boutique)
        function viewProduct(productId) {
            // Charger les détails du produit via AJAX/fetch
            fetch('admin.php?section=boutique&action=view&id=' + productId)
                .then(response => response.text())
                .then(html => {
                    // Extraire le contenu du produit depuis la réponse
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const productDetails = doc.querySelector('.product-details-section');

                    if (productDetails) {
                        // Créer le contenu du modal
                        const modal = document.getElementById('product-modal');
                        const modalTitle = document.getElementById('product-modal-title');
                        const modalBody = document.getElementById('product-modal-body');

                        // Extraire les informations du produit
                        const name = productDetails.querySelector('h2')?.textContent || 'Détails du produit';
                        const content = productDetails.querySelector('.details-content')?.innerHTML ||
                            'Contenu non disponible';

                        modalTitle.textContent = name;
                        modalBody.innerHTML = content;
                        modal.style.display = 'block';

                        // Fermer le modal si on clique en dehors
                        modal.addEventListener('click', function(e) {
                            if (e.target === modal) {
                                closeProductModal();
                            }
                        });
                    } else {
                        alert('Impossible de charger les détails du produit.');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement:', error);
                    alert('Erreur lors du chargement des détails.');
                });
        }

        function editProduct(productId) {
            console.log('editProduct called with productId:', productId);

            // Vérifier que les éléments du modal existent
            const modal = document.getElementById('product-modal');
            const modalTitle = document.getElementById('product-modal-title');
            const modalBody = document.getElementById('product-modal-body');

            console.log('Modal elements found:', {
                modal,
                modalTitle,
                modalBody
            });

            if (!modal || !modalTitle || !modalBody) {
                alert('Erreur: Éléments du modal non trouvés. Veuillez rafraîchir la page.');
                return;
            }

            // Charger le formulaire d'édition via AJAX/fetch
            fetch('admin.php?section=boutique&action=edit&id=' + productId)
                .then(response => {
                    console.log('Fetch response status:', response.status);
                    return response.text();
                })
                .then(html => {
                    console.log('Response HTML length:', html.length);
                    console.log('Response HTML preview:', html.substring(0, 500));

                    // Extraire le formulaire depuis la réponse
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const editForm = doc.querySelector('.product-edit-section');

                    console.log('Edit form found:', editForm);

                    if (editForm) {
                        modalTitle.textContent = 'Modifier le produit';
                        modalBody.innerHTML = editForm.outerHTML;
                        modal.style.display = 'block';

                        // Fermer le modal si on clique en dehors
                        modal.addEventListener('click', function(e) {
                            if (e.target === modal) {
                                closeProductModal();
                            }
                        });
                    } else {
                        alert(
                            'Impossible de charger le formulaire d\'édition. Élément .product-edit-section non trouvé.'
                            );
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement:', error);
                    alert('Erreur lors du chargement du formulaire: ' + error.message);
                });
        }

        function deleteProduct(productId) {
            if (confirm('Supprimer ce produit ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_product">
                    <input type="hidden" name="product_id" value="${productId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function closeProductModal() {
            const modal = document.getElementById('product-modal');
            modal.style.display = 'none';
        }

        // Fonctions pour la gestion des contacts (table)
        function filterContacts() {
            const filterValue = document.getElementById('status-filter').value;
            const rows = document.querySelectorAll('.table-row');

            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                if (filterValue === 'all' || status === filterValue) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function searchContacts() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const rows = document.querySelectorAll('.table-row');

            rows.forEach(row => {
                const senderName = row.querySelector('.sender-name').textContent.toLowerCase();
                const senderEmail = row.querySelector('.sender-email').textContent.toLowerCase();
                const subject = row.querySelector('.subject-text').textContent.toLowerCase();
                const message = row.querySelector('.message-preview').textContent.toLowerCase();

                if (senderName.includes(searchTerm) ||
                    senderEmail.includes(searchTerm) ||
                    subject.includes(searchTerm) ||
                    message.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>
