<?php

session_start();

// Inclure la connexion à la base de données
require_once '../boutique/js/db.php';

// Configuration
$page_title = "Administration - Demandes de devis";
$message = '';
$messageType = 'info';

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
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
        }
    }
}

// GESTION DES DEVIS


// Récupérer tous les devis avec pagination

function getAllDevis($page = 1, $perPage = 20)
{
    global $bdd;

    try {
        
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM devis ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";

        $stmt = $bdd->query($sql);

        if (!$stmt) {
            error_log("Erreur d'exécution SQL: " . print_r($bdd->errorInfo(), true));
            return [];
        }

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $data;
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération des devis: ' . $e->getMessage());
        return [];
    }
}


// Compter le nombre total de devis

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

// Récupérer un devis par son ID

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

// Mettre à jour le statut d'un devis

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

// Supprimer un devis

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
                    $cheminImage = 'uploads/' . $image;
                    if (file_exists($cheminImage)) {
                        unlink($cheminImage);
                    }
                }
            }
        }

        // Supprimer le devis de la base de données
        $stmt = $bdd->prepare("DELETE FROM devis WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log('Erreur lors de la suppression du devis: ' . $e->getMessage());
        return false;
    }
}

// Obtenir les statistiques des devis

function getDevisStats()
{
    global $bdd;

    try {
        $stats = [];

        // Total des devis
        $stmt = $bdd->query("SELECT COUNT(*) as total FROM devis");
        $stats['total'] = $stmt->fetch()['total'];

        // Par statut
        $stmt = $bdd->query("SELECT statut, COUNT(*) as count FROM devis GROUP BY statut");
        $stats['par_statut'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Cette semaine
        $stmt = $bdd->query("SELECT COUNT(*) as count FROM devis WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stats['cette_semaine'] = $stmt->fetch()['count'];

        // Aujourd'hui
        $stmt = $bdd->query("SELECT COUNT(*) as count FROM devis WHERE DATE(created_at) = CURDATE()");
        $stats['aujourdhui'] = $stmt->fetch()['count'];

        return $stats;
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
        return ['total' => 0, 'par_statut' => [], 'cette_semaine' => 0, 'aujourdhui' => 0];
    }
}

// PARAMÈTRES DE PAGINATION

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['per_page']) ? min(100, max(10, intval($_GET['per_page']))) : 20;

// Gestion de l'action "view" pour afficher les détails d'un devis
$viewDevis = null;
if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
    $viewDevis = getDevisById(intval($_GET['id']));
}

// Récupérer les données
$devis = getAllDevis($page, $perPage);
$totalDevis = countDevis();
$totalPages = ceil($totalDevis / $perPage);
$stats = getDevisStats();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            /* Couleurs du site */
            --wood-light: #BE8A4A;
            --wood-dark: #5C3A21;
            --neutral-100: #FFFFFF;
            --neutral-50: #FAFAFA;
            --neutral-200: #E5E5E5;
            --neutral-300: #D4D4D4;
            --neutral-600: #525252;
            --neutral-800: #111111;
            --neutral-900: #0A0A0A;
            --muted: #666666;
            --accent: #C9A66B;

            /* Espacements */
            --espacement-xs: 0.5rem;
            --espacement-sm: 1rem;
            --espacement-md: 1.5rem;
            --espacement-lg: 2rem;
            --espacement-xl: 3rem;
            --espacement-2xl: 4rem;

            /* Typo */
            --font-titre: 'Montserrat', sans-serif;
            --font-corps: 'Inter', sans-serif;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-base: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            --font-size-2xl: 1.5rem;
            --font-size-3xl: 1.875rem;
            --font-size-4xl: 2.25rem;

            /* Transitions & ombres */
            --transition-rapide: 150ms ease-in-out;
            --transition-normale: 300ms ease-in-out;
            --ombre-legere: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --ombre-moyenne: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --ombre-forte: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);

            /* Rayons */
            --rayon-sm: 4px;
            --rayon-md: 8px;
            --rayon-lg: 12px;
            --rayon-xl: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-corps);
            font-size: var(--font-size-base);
            color: var(--neutral-800);
            background: var(--neutral-50);
            line-height: 1.6;
        }

        h1, h2, h3 {
            font-family: var(--font-titre);
            font-weight: 700;
            line-height: 1.2;
            color: var(--wood-dark);
            margin-bottom: var(--espacement-md);
        }

        /* Header admin */
        .admin-header {
            background: linear-gradient(135deg, var(--wood-light) 0%, var(--wood-dark) 100%);
            color: var(--neutral-100);
            padding: var(--espacement-xl) 0;
            box-shadow: var(--ombre-moyenne);
        }

        .admin-header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 var(--espacement-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-logo {
            display: flex;
            align-items: center;
            gap: var(--espacement-md);
            text-decoration: none;
            color: var(--neutral-100);
        }

        .admin-logo img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .admin-logo span {
            font-size: var(--font-size-xl);
            font-weight: 700;
        }

        .admin-nav {
            display: flex;
            gap: var(--espacement-lg);
            align-items: center;
        }

        .admin-nav a {
            color: var(--neutral-100);
            text-decoration: none;
            padding: var(--espacement-sm) var(--espacement-md);
            border-radius: var(--rayon-md);
            transition: all var(--transition-rapide);
            font-weight: 500;
        }

        .admin-nav a:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Conteneur principal */
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: var(--espacement-xl) var(--espacement-lg);
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

        /* Statistiques */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--espacement-lg);
            margin-bottom: var(--espacement-2xl);
        }

        .stat-card {
            background: var(--neutral-100);
            padding: var(--espacement-xl);
            border-radius: var(--rayon-xl);
            box-shadow: var(--ombre-legere);
            text-align: center;
            transition: all var(--transition-normale);
            border: 1px solid var(--neutral-200);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--ombre-moyenne);
        }

        .stat-number {
            font-size: var(--font-size-4xl);
            font-weight: 700;
            color: var(--wood-light);
            margin-bottom: var(--espacement-sm);
            display: block;
        }

        .stat-label {
            color: var(--muted);
            font-size: var(--font-size-lg);
            font-weight: 500;
        }

        /* Table des devis */
        .devis-table-container {
            background: var(--neutral-100);
            border-radius: var(--rayon-xl);
            box-shadow: var(--ombre-legere);
            overflow-x: auto;
            border: 1px solid var(--neutral-200);
        }

        .devis-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        .devis-table th,
        .devis-table td {
            padding: var(--espacement-lg);
            text-align: left;
            border-bottom: 1px solid var(--neutral-200);
        }

        .devis-table th {
            background: var(--wood-dark);
            color: var(--neutral-100);
            font-weight: 600;
            font-size: var(--font-size-sm);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: sticky;
            top: 0;
        }

        .devis-table tbody tr {
            transition: all var(--transition-rapide);
        }

        .devis-table tbody tr:hover {
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

        /* Responsive */
        @media (max-width: 768px) {
            .admin-header-content {
                flex-direction: column;
                gap: var(--espacement-md);
                text-align: center;
            }

            .admin-nav {
                flex-wrap: wrap;
                justify-content: center;
            }

            .admin-container {
                padding: var(--espacement-lg) var(--espacement-md);
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .devis-table {
                font-size: var(--font-size-sm);
            }

            .devis-table th,
            .devis-table td {
                padding: var(--espacement-sm);
            }

            .action-buttons {
                flex-direction: column;
                gap: var(--espacement-xs);
            }

            .btn-action {
                width: 100%;
                justify-content: center;
                padding: var(--espacement-sm);
            }
        }

        @media (max-width: 480px) {
            .admin-container {
                padding: var(--espacement-md) var(--espacement-sm);
            }

            .stat-card {
                padding: var(--espacement-lg);
            }

            .stat-number {
                font-size: var(--font-size-3xl);
            }
        }

        /* Sous-titre de section */
        .section-subtitle {
            color: var(--muted);
            font-size: var(--font-size-lg);
            margin-bottom: var(--espacement-2xl);
            text-align: center;
        }

        /* Section détails devis */
        .devis-details-section {
            background: var(--neutral-100);
            border-radius: var(--rayon-xl);
            box-shadow: var(--ombre-legere);
            border: 1px solid var(--neutral-200);
            margin-bottom: var(--espacement-2xl);
        }

        .details-header {
            background: linear-gradient(135deg, var(--wood-light) 0%, var(--wood-dark) 100%);
            color: var(--neutral-100);
            padding: var(--espacement-xl);
            border-radius: var(--rayon-xl) var(--rayon-xl) 0 0;
            text-align: center;
        }

        .details-header h2 {
            color: var(--neutral-100);
            margin-bottom: var(--espacement-sm);
        }

        .details-header p {
            opacity: 0.9;
            font-size: var(--font-size-base);
        }

        .details-content {
            padding: var(--espacement-2xl);
        }

        .details-section {
            margin-bottom: var(--espacement-2xl);
            padding-bottom: var(--espacement-xl);
            border-bottom: 1px solid var(--neutral-200);
        }

        .details-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .details-section h3 {
            color: var(--wood-dark);
            margin-bottom: var(--espacement-lg);
            font-size: var(--font-size-xl);
            display: flex;
            align-items: center;
            gap: var(--espacement-sm);
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--espacement-lg);
        }

        .detail-item {
            background: var(--neutral-50);
            padding: var(--espacement-lg);
            border-radius: var(--rayon-md);
            border: 1px solid var(--neutral-200);
        }

        .detail-label {
            font-weight: 600;
            color: var(--wood-dark);
            margin-bottom: var(--espacement-xs);
            font-size: var(--font-size-sm);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            color: var(--neutral-800);
            line-height: 1.5;
            overflow-x: auto;
        }

        .description-text {
            background: var(--neutral-50);
            padding: var(--espacement-lg);
            border-radius: var(--rayon-md);
            border: 1px solid var(--neutral-200);
            white-space: pre-wrap;
            line-height: 1.6;
        }

        /* Images */
        .images-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: var(--espacement-lg);
            margin-top: var(--espacement-lg);
        }

        .image-link {
            display: block;
            border-radius: var(--rayon-md);
            overflow: hidden;
            box-shadow: var(--ombre-legere);
            transition: all var(--transition-normale);
            text-decoration: none;
        }

        .image-link:hover {
            transform: scale(1.05);
            box-shadow: var(--ombre-moyenne);
        }

        .image-thumb {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: block;
        }

        /* Services */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--espacement-lg);
        }

        .service-item {
            display: flex;
            align-items: center;
            gap: var(--espacement-md);
            padding: var(--espacement-md);
            background: var(--neutral-50);
            border-radius: var(--rayon-md);
            border: 1px solid var(--neutral-200);
        }

        .service-icon {
            font-size: var(--font-size-lg);
            font-weight: bold;
        }

        /* Préférences de contact */
        .contact-prefs {
            display: grid;
            gap: var(--espacement-md);
        }

        .pref-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--espacement-md);
            background: var(--neutral-50);
            border-radius: var(--rayon-md);
            border: 1px solid var(--neutral-200);
        }

        .pref-value {
            font-weight: 600;
            color: var(--wood-dark);
        }

        /* Métadonnées */
        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--espacement-lg);
        }

        .meta-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--espacement-md);
            background: var(--neutral-50);
            border-radius: var(--rayon-md);
            border: 1px solid var(--neutral-200);
        }

        .meta-item span:first-child {
            font-weight: 600;
            color: var(--wood-dark);
            font-size: var(--font-size-sm);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Actions des détails */
        .details-actions {
            display: flex;
            gap: var(--espacement-lg);
            justify-content: center;
            margin-top: var(--espacement-xl);
            padding-top: var(--espacement-xl);
            border-top: 1px solid var(--neutral-200);
        }

        /* Responsive pour détails */
        @media (max-width: 768px) {
            .details-content {
                padding: var(--espacement-lg);
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .services-grid {
                grid-template-columns: 1fr;
            }

            .metadata-grid {
                grid-template-columns: 1fr;
            }

            .images-preview {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }

            .details-actions {
                flex-direction: column;
            }

            .details-actions .btn-action {
                width: 100%;
                justify-content: center;
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
    </style>
</head>
<body>
    <!-- Header admin -->
    <header class="admin-header">
        <div class="admin-header-content">
            <a href="../page_acceuil/acceuil.php" class="admin-logo">
                <img src="../page_acceuil/assets/ayoubdecor_logoo.png" alt="AYOUBDECOR Logo">
                <span>AYOUBDECOR</span>
            </a>
            <nav class="admin-nav">
                <a href="../page_acceuil/acceuil.php">← Retour à l'accueil</a>
                <a href="devis.php">📝 Nouveau devis</a>
                <a href="../boutique/boutique.php">🛍️ Boutique</a>
            </nav>
        </div>
    </header>

    <!-- Contenu principal -->
    <main class="admin-container fade-in">
        <h1>🛠️ Administration des devis</h1>
        <p class="section-subtitle">Gestion professionnelle des demandes de devis clients</p>

        <?php if (!empty($message)): ?>
        <div class="message message-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Statistiques -->
        <section class="stats-grid">
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['total']; ?></span>
                <span class="stat-label">Total devis</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['aujourdhui']; ?></span>
                <span class="stat-label">Aujourd'hui</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['cette_semaine']; ?></span>
                <span class="stat-label">Cette semaine</span>
            </div>
            <div class="stat-card">
                <span class="stat-number"><?php echo $stats['par_statut']['nouveau'] ?? 0; ?></span>
                <span class="stat-label">Nouveaux</span>
            </div>
        </section>

        <!-- Affichage des détails d'un devis spécifique -->
        <?php if ($viewDevis): ?>
        <section class="devis-details-section">
            <div class="details-header">
                <h2>📋 Détails du devis #<?php echo htmlspecialchars($viewDevis['id']); ?></h2>
                <p>Demandé par <?php echo htmlspecialchars($viewDevis['nom']); ?> le <?php echo date('d/m/Y à H:i', strtotime($viewDevis['created_at'])); ?></p>
                <a href="admin.php" class="btn-action btn-view" style="margin-top: var(--espacement-md);">
                    ← Retour à la liste
                </a>
            </div>

            <div class="details-content">
                <!-- Informations personnelles -->
                <div class="details-section">
                    <h3>👤 Informations personnelles</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-label">Nom complet:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($viewDevis['nom']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Email:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($viewDevis['email']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Téléphone:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($viewDevis['telephone']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Ville:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($viewDevis['ville'] ?? 'Non spécifiée'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Détails du projet -->
                <div class="details-section">
                    <h3>🏗️ Détails du projet</h3>
                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-label">Type de prestation:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($viewDevis['type_prestation']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Type de meuble:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($viewDevis['type_meuble'] ?? 'Non spécifié'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Dimensions:</div>
                            <div class="detail-value">
                                <?php
                                $dimensions = [];
                                if (!empty($viewDevis['longueur'])) $dimensions[] = 'L: ' . htmlspecialchars($viewDevis['longueur']);
                                if (!empty($viewDevis['largeur'])) $dimensions[] = 'l: ' . htmlspecialchars($viewDevis['largeur']);
                                if (!empty($viewDevis['hauteur'])) $dimensions[] = 'H: ' . htmlspecialchars($viewDevis['hauteur']);
                                echo !empty($dimensions) ? implode(' × ', $dimensions) : 'Non spécifiées';
                                ?>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Matériau souhaité:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($viewDevis['materiau'] ?? 'Non spécifié'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Finition:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($viewDevis['finition'] ?? 'Non spécifiée'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Budget estimé:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($viewDevis['budget'] ?? 'Non spécifié'); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Date souhaitée:</div>
                            <div class="detail-value"><?php echo !empty($viewDevis['date_souhaitee']) ? date('d/m/Y', strtotime($viewDevis['date_souhaitee'])) : 'Non spécifiée'; ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Urgence:</div>
                            <div class="detail-value"><?php echo htmlspecialchars($viewDevis['urgence'] ?? 'normal'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Description détaillée -->
                <div class="details-section">
                    <h3>📝 Description du projet</h3>
                    <div class="detail-item">
                        <div class="detail-value description-text"><?php echo nl2br(htmlspecialchars($viewDevis['description'])); ?></div>
                    </div>

                    <?php if (!empty($viewDevis['contraintes'])): ?>
                    <div class="detail-item" style="margin-top: var(--espacement-lg);">
                        <div class="detail-label">Contraintes particulières:</div>
                        <div class="detail-value"><?php echo nl2br(htmlspecialchars($viewDevis['contraintes'])); ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($viewDevis['inspiration'])): ?>
                    <div class="detail-item" style="margin-top: var(--espacement-lg);">
                        <div class="detail-label">Sources d'inspiration:</div>
                        <div class="detail-value"><?php echo nl2br(htmlspecialchars($viewDevis['inspiration'])); ?></div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Images uploadées -->
                <?php if (!empty($viewDevis['images'])): ?>
                <div class="details-section">
                    <h3>🖼️ Images fournies</h3>
                    <div class="images-preview">
                        <?php
                        $images = json_decode($viewDevis['images'], true);
                        if (is_array($images)):
                            foreach ($images as $image):
                                $imagePath = 'uploads/' . $image;
                                $fullImagePath = __DIR__ . '/' . $imagePath;
                                $webPath = '/grass_farmers/page_devis/' . $imagePath;
                                if (file_exists($fullImagePath)):
                        ?>
                                <a href="<?php echo htmlspecialchars($webPath); ?>" target="_blank" class="image-link">
                                    <img src="<?php echo htmlspecialchars($webPath); ?>" alt="Image du projet" class="image-thumb">
                                </a>
                        <?php
                                else:
                        ?>
                                <div class="image-error">
                                    <span>❌ Image non trouvée: <?php echo htmlspecialchars($image); ?> (chemin: <?php echo htmlspecialchars($fullImagePath); ?>)</span>
                                </div>
                        <?php
                                endif;
                            endforeach;
                        else:
                        ?>
                        <div class="image-error">
                            <span>❌ Format d'images invalide: <?php echo htmlspecialchars($viewDevis['images']); ?></span>
                        </div>
                        <?php
                        endif;
                        ?>
                    </div>
                </div>
                <?php endif; ?>



                <!-- Métadonnées -->
                <div class="details-section">
                    <h3>📊 Informations techniques</h3>
                    <div class="metadata-grid">
                        <div class="meta-item">
                            <span>Statut actuel:</span>
                            <span class="status-badge status-<?php echo str_replace([' ', '-'], ['', '-'], strtolower($viewDevis['statut'])); ?>">
                                <?php echo htmlspecialchars($viewDevis['statut']); ?>
                            </span>
                        </div>
                        <div class="meta-item">
                            <span>Date de création:</span>
                            <span><?php echo date('d/m/Y à H:i:s', strtotime($viewDevis['created_at'])); ?></span>
                        </div>
                        <div class="meta-item">
                            <span>Dernière modification:</span>
                            <span><?php echo date('d/m/Y à H:i:s', strtotime($viewDevis['updated_at'])); ?></span>
                        </div>
                        <div class="meta-item">
                            <span>Adresse IP:</span>
                            <span><?php echo htmlspecialchars($viewDevis['ip_address'] ?? 'N/A'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="details-actions">
                    <button class="btn-action btn-edit" onclick="changeStatus(<?php echo $viewDevis['id']; ?>, '<?php echo $viewDevis['statut']; ?>')">
                        ✏️ Changer le statut
                    </button>
                    <button class="btn-action btn-delete" onclick="deleteDevis(<?php echo $viewDevis['id']; ?>)">
                        🗑️ Supprimer ce devis
                    </button>
                </div>
            </div>
        </section>
        <?php else: ?>
        <!-- Table des devis -->
        <section class="devis-table-container">
            <table class="devis-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Contact</th>
                        <th>Prestation</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($devis)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon">📋</div>
                                <h3>Aucun devis trouvé</h3>
                                <p>Les nouveaux devis apparaîtront automatiquement ici.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($devis as $d): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($d['id']); ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($d['nom']); ?></strong><br>
                                <small><?php echo htmlspecialchars($d['ville'] ?? 'Ville non spécifiée'); ?></small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($d['email']); ?><br>
                                <small><?php echo htmlspecialchars($d['telephone']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($d['type_prestation']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo str_replace([' ', '-'], ['', '-'], strtolower($d['statut'])); ?>">
                                    <?php echo htmlspecialchars($d['statut']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($d['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action btn-view" onclick="showDevisDetails(<?php echo $d['id']; ?>)">
                                        👁️ Voir
                                    </button>
                                    <button class="btn-action btn-edit" onclick="changeStatus(<?php echo $d['id']; ?>, '<?php echo $d['statut']; ?>')">
                                        ✏️ Statut
                                    </button>
                                    <button class="btn-action btn-delete" onclick="deleteDevis(<?php echo $d['id']; ?>)">
                                        🗑️ Supp.
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
        <?php endif; ?>
    </main>

    <script>
        function showDevisDetails(devisId) {
            window.open('admin.php?action=view&id=' + devisId, '_blank');
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
    </script>
</body>
</html>
