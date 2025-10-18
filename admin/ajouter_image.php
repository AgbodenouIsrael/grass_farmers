<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure la connexion à la base de données
require_once '../src/database/db.php';

// Vérifier si l'administrateur est connecté
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

// Configuration
$page_title = "Ajouter une image - Administration";
$message = '';
$messageType = 'info';

// Récupérer les messages de session
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['messageType'] ?? 'info';
    unset($_SESSION['message'], $_SESSION['messageType']);
}

// Catégories disponibles pour la galerie
$categories = [
    'cuisine' => 'Cuisines',
    'salon' => 'Salons',
    'bureau' => 'Bureaux',
    'chambre' => 'Chambres',
    'sdb' => 'Salles de bain',
    'exterieur' => 'Extérieur',
    'autre' => 'Autre'
];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            width: 280px;
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
            margin-left: 280px;
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

        /* Formulaire d'ajout d'image */
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--neutral-100);
            border-radius: var(--rayon-xl);
            box-shadow: var(--ombre-legere);
            border: 1px solid var(--neutral-200);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, var(--wood-light) 0%, var(--wood-dark) 100%);
            color: var(--neutral-100);
            padding: var(--espacement-xl);
            text-align: center;
        }

        .form-header h2 {
            margin: 0;
            font-size: var(--font-size-xl);
            font-weight: 600;
        }

        .form-content {
            padding: var(--espacement-2xl);
        }

        .form-group {
            margin-bottom: var(--espacement-xl);
        }

        .form-label {
            display: block;
            margin-bottom: var(--espacement-sm);
            font-weight: 600;
            color: var(--wood-dark);
            font-size: var(--font-size-sm);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: var(--espacement-md);
            border: 2px solid var(--neutral-200);
            border-radius: var(--rayon-md);
            font-size: var(--font-size-base);
            transition: all var(--transition-rapide);
            background: var(--neutral-100);
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--wood-light);
            box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-checkbox-group {
            display: flex;
            align-items: center;
            gap: var(--espacement-md);
        }

        .form-checkbox {
            width: auto;
            margin: 0;
        }

        .file-upload-area {
            border: 2px dashed var(--neutral-300);
            border-radius: var(--rayon-md);
            padding: var(--espacement-2xl);
            text-align: center;
            transition: all var(--transition-rapide);
            cursor: pointer;
            background: var(--neutral-50);
        }

        .file-upload-area:hover {
            border-color: var(--wood-light);
            background: rgba(139, 115, 85, 0.05);
        }

        .file-upload-area.dragover {
            border-color: var(--wood-light);
            background: rgba(139, 115, 85, 0.1);
        }

        .file-upload-icon {
            font-size: var(--font-size-4xl);
            color: var(--neutral-400);
            margin-bottom: var(--espacement-md);
            display: block;
        }

        .file-upload-text {
            color: var(--muted);
            margin-bottom: var(--espacement-sm);
        }

        .file-upload-hint {
            font-size: var(--font-size-sm);
            color: var(--muted);
        }

        .file-preview {
            margin-top: var(--espacement-lg);
            display: none;
        }

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            border-radius: var(--rayon-md);
            box-shadow: var(--ombre-legere);
            display: block;
            margin: 0 auto;
        }

        .form-actions {
            display: flex;
            gap: var(--espacement-lg);
            justify-content: center;
            margin-top: var(--espacement-2xl);
            padding-top: var(--espacement-xl);
            border-top: 1px solid var(--neutral-200);
        }

        .btn-primary {
            background: var(--wood-light);
            color: var(--neutral-100);
            border: none;
            padding: var(--espacement-md) var(--espacement-2xl);
            border-radius: var(--rayon-md);
            cursor: pointer;
            font-weight: 600;
            font-size: var(--font-size-base);
            transition: all var(--transition-rapide);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: var(--espacement-sm);
        }

        .btn-primary:hover {
            background: var(--wood-dark);
            transform: translateY(-2px);
            box-shadow: var(--ombre-moyenne);
        }

        .btn-secondary {
            background: var(--neutral-200);
            color: var(--wood-dark);
            border: 2px solid var(--neutral-300);
            padding: var(--espacement-md) var(--espacement-2xl);
            border-radius: var(--rayon-md);
            cursor: pointer;
            font-weight: 600;
            font-size: var(--font-size-base);
            transition: all var(--transition-rapide);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: var(--espacement-sm);
        }

        .btn-secondary:hover {
            background: var(--neutral-300);
            transform: translateY(-2px);
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
            .form-content {
                padding: var(--espacement-lg);
            }
            .form-actions {
                flex-direction: column;
            }
            .btn-primary,
            .btn-secondary {
                width: 100%;
                justify-content: center;
            }
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
                        <a href="admin.php?section=dashboard" class="nav-link">
                            <i class="bx bx-home"></i>Dashboard
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Gestion</div>
                    <div class="nav-item">
                        <a href="admin.php?section=devis" class="nav-link">
                            <i class="bx bx-file"></i>Devis
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="admin.php?section=boutique" class="nav-link">
                            <i class="bx bx-shopping-bag"></i>Boutique
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="admin.php?section=galerie" class="nav-link active">
                            <i class="bx bx-images"></i>Galerie
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="admin.php?section=contacts" class="nav-link">
                            <i class="bx bx-envelope"></i>Contacts
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="main-header">
                <h1 class="page-title">Ajouter une image à la galerie</h1>
                <div class="header-actions">
                    <a href="admin.php?section=galerie" class="btn-logout">
                        <i class="bx bx-arrow-back"></i>Retour
                    </a>
                </div>
            </header>

            <div class="main-content">
                <?php if (!empty($message)): ?>
                <div class="message message-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>

                <div class="form-container">
                    <div class="form-header">
                        <h2>🖼️ Nouvelle image de galerie</h2>
                    </div>

                    <form action="../src/controllers/traitementGalerie.php" method="POST" enctype="multipart/form-data" class="form-content">
                        <input type="hidden" name="action" value="ajouter_image">

                        <!-- Sélection du fichier -->
                        <div class="form-group">
                            <label class="form-label">Image *</label>
                            <div class="file-upload-area" id="file-upload-area">
                                <span class="file-upload-icon">📁</span>
                                <div class="file-upload-text">Cliquez pour sélectionner une image</div>
                                <div class="file-upload-hint">Formats acceptés: JPG, PNG, WebP (max 10MB)</div>
                                <input type="file" name="image" id="image" accept="image/*" required style="display: none;">
                            </div>
                            <div class="file-preview" id="file-preview">
                                <img src="" alt="Aperçu" class="preview-image" id="preview-image">
                            </div>
                        </div>

                        <!-- Titre -->
                        <div class="form-group">
                            <label for="titre" class="form-label">Titre *</label>
                            <input type="text" id="titre" name="titre" class="form-input" required
                                   placeholder="Ex: Cuisine moderne en bois massif">
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-textarea"
                                      placeholder="Description optionnelle de l'image..."></textarea>
                        </div>

                        <!-- Catégorie -->
                        <div class="form-group">
                            <label for="categorie" class="form-label">Catégorie *</label>
                            <select id="categorie" name="categorie" class="form-select" required>
                                <option value="">Sélectionnez une catégorie</option>
                                <?php foreach ($categories as $value => $label): ?>
                                <option value="<?php echo htmlspecialchars($value); ?>">
                                    <?php echo htmlspecialchars($label); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Statut -->
                        <div class="form-group">
                            <label class="form-checkbox-group">
                                <input type="checkbox" id="statut" name="statut" class="form-checkbox" checked>
                                <span>Image visible publiquement</span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <i class="bx bx-upload"></i>Ajouter l'image
                            </button>
                            <a href="admin.php?section=galerie" class="btn-secondary">
                                <i class="bx bx-x"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileUploadArea = document.getElementById('file-upload-area');
            const fileInput = document.getElementById('image');
            const filePreview = document.getElementById('file-preview');
            const previewImage = document.getElementById('preview-image');

            // Gestion du clic sur la zone d'upload
            fileUploadArea.addEventListener('click', function() {
                fileInput.click();
            });

            // Gestion du drag & drop
            fileUploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                fileUploadArea.classList.add('dragover');
            });

            fileUploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                fileUploadArea.classList.remove('dragover');
            });

            fileUploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                fileUploadArea.classList.remove('dragover');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFileSelect(files[0]);
                }
            });

            // Gestion de la sélection de fichier
            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target.files[0]);
                }
            });

            function handleFileSelect(file) {
                // Vérification du type de fichier
                if (!file.type.startsWith('image/')) {
                    alert('Veuillez sélectionner un fichier image.');
                    return;
                }

                // Vérification de la taille (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    alert('L\'image est trop volumineuse (max 10MB).');
                    return;
                }

                // Afficher l'aperçu
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    filePreview.style.display = 'block';
                    fileUploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            // Validation du formulaire
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const titre = document.getElementById('titre').value.trim();
                const categorie = document.getElementById('categorie').value;

                if (!titre) {
                    e.preventDefault();
                    alert('Veuillez saisir un titre pour l\'image.');
                    return;
                }

                if (!categorie) {
                    e.preventDefault();
                    alert('Veuillez sélectionner une catégorie.');
                    return;
                }

                if (!fileInput.files.length) {
                    e.preventDefault();
                    alert('Veuillez sélectionner une image.');
                    return;
                }
            });
        });
    </script>
</body>
</html>
