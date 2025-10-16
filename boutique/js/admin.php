<?php
session_start();
include 'db.php';

// Gestion de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Vérifier l'authentification
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login.php");
    exit;
}

// Gérer les actions CRUD
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        try {
            // Gestion de l'upload d'image
            $imagePath = './assets/default-product.png'; // Image par défaut

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../assets/uploads/'; // Dossier de destination
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                $uploadFile = $uploadDir . $fileName;

                // Validation du fichier
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                $maxSize = 5 * 1024 * 1024; // 5MB

                if (!in_array($_FILES['image']['type'], $allowedTypes)) {
                    throw new Exception('Format d\'image non supporté. Utilisez JPG, PNG, GIF ou WebP.');
                }

                if ($_FILES['image']['size'] > $maxSize) {
                    throw new Exception('L\'image est trop volumineuse. Taille maximum : 5MB.');
                }

                // Créer le dossier s'il n'existe pas
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Déplacer le fichier
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    $imagePath = './assets/uploads/' . $fileName;
                } else {
                    throw new Exception('Erreur lors de l\'upload de l\'image.');
                }
            }

            switch ($action) {
                case 'add':
                    $stmt = $bdd->prepare("INSERT INTO produits (nom, prix, categorie, stock, description, image) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['nom'],
                        $_POST['prix'],
                        $_POST['categorie'],
                        $_POST['stock'],
                        $_POST['description'],
                        $imagePath
                    ]);
                    $message = "Produit ajouté avec succès !";
                    $messageType = 'success';
                    break;

                case 'edit':
                    // Récupérer l'ancienne image pour la supprimer si nécessaire
                    if (isset($_POST['id'])) {
                        $stmt = $bdd->prepare("SELECT image FROM produits WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        $oldProduct = $stmt->fetch();

                        // Si une nouvelle image est uploadée et qu'il y avait une ancienne image personnalisée
                        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK &&
                            $oldProduct && $oldProduct['image'] !== './assets/default-product.png' &&
                            file_exists('../' . $oldProduct['image'])) {
                            unlink('../' . $oldProduct['image']); // Supprimer l'ancienne image
                        } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                            // Garder l'ancienne image si aucune nouvelle n'est uploadée
                            $imagePath = $oldProduct['image'];
                        }
                    }

                    $stmt = $bdd->prepare("UPDATE produits SET nom = ?, prix = ?, categorie = ?, stock = ?, description = ?, image = ? WHERE id = ?");
                    $stmt->execute([
                        $_POST['nom'],
                        $_POST['prix'],
                        $_POST['categorie'],
                        $_POST['stock'],
                        $_POST['description'],
                        $imagePath,
                        $_POST['id']
                    ]);
                    $message = "Produit modifié avec succès !";
                    $messageType = 'success';
                    break;

                case 'delete':
                    // Supprimer l'image associée avant de supprimer le produit
                    if (isset($_POST['id'])) {
                        $stmt = $bdd->prepare("SELECT image FROM produits WHERE id = ?");
                        $stmt->execute([$_POST['id']]);
                        $product = $stmt->fetch();

                        if ($product && $product['image'] !== './assets/default-product.png' &&
                            file_exists('../' . $product['image'])) {
                            unlink('../' . $product['image']); // Supprimer l'image
                        }
                    }

                    $stmt = $bdd->prepare("DELETE FROM produits WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $message = "Produit supprimé avec succès !";
                    $messageType = 'success';
                    break;
            }
        } catch(Exception $e) {
            $message = "Erreur : " . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Récupérer tous les produits
try {
    $stmt = $bdd->query("SELECT * FROM produits ORDER BY created_at DESC");
    $products = $stmt->fetchAll();
} catch(PDOException $e) {
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - AYOUBDECOR</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" type="image/x-icon" href="../assets/ayoubdecor_logo2.png">
</head>
<body>
    <!-- Header Admin -->
    <header class="en-tete">
        <div class="conteneur">
            <div class="header-contenu">
                <a href="../boutique.php" class="logo">AYOUBDECOR - Admin</a>
                <nav class="menu-principal">
                    <ul>
                        <li><a href="../boutique.php">Retour à la boutique</a></li>
                        <li><a href="?logout=1" style="color: #dc2626;">Déconnexion</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Section Admin -->
    <section class="section">
        <div class="conteneur">
            <div class="admin-header">
                <h1>Gestion des Produits</h1>
                <button class="btn btn-primaire" id="add-product-btn" style="padding: var(--espacement-sm) var(--espacement-lg);">
                    <i class='bx bx-plus'></i> Ajouter un produit
                </button>
            </div>

            <?php if ($message): ?>
            <div class="notification notification-<?php echo $messageType; ?>" style="margin-bottom: 20px;">
                <i class='bx <?php echo $messageType === 'success' ? 'bx-check-circle' : 'bx-error-circle'; ?>'></i>
                <span><?php echo $message; ?></span>
            </div>
            <?php endif; ?>

            <!-- Formulaire d'ajout/modification -->
            <div class="admin-form" id="product-form" style="display: none;">
                <h2 id="form-title">Ajouter un produit</h2>
                <form id="product-form-element" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="product-id" name="id">
                    <input type="hidden" id="action" name="action" value="add">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-name">Nom du produit *</label>
                            <input type="text" id="product-name" name="nom" required>
                        </div>
                        <div class="form-group">
                            <label for="product-price">Prix (FCFA) *</label>
                            <input type="number" id="product-price" name="prix" min="0" step="0.01" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-category">Catégorie *</label>
                            <select id="product-category" name="categorie" required>
                                <option value="">Sélectionner une catégorie</option>
                                <option value="tables">Tables</option>
                                <option value="etageres">Étagères</option>
                                <option value="bureaux">Bureaux</option>
                                <option value="rangements">Rangements</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="product-stock">Stock *</label>
                            <input type="number" id="product-stock" name="stock" min="0" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="product-description">Description</label>
                        <textarea id="product-description" name="description" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="product-image">Image du produit</label>
                        <div class="image-upload-container">
                            <input type="file" id="product-image" name="image" accept="image/*" style="display: none;">
                            <div class="image-upload-area" id="image-upload-area">
                                <div class="upload-placeholder">
                                    <i class='bx bx-cloud-upload'></i>
                                    <p>Cliquez pour sélectionner une image</p>
                                    <small>PNG, JPG, GIF jusqu'à 5MB</small>
                                </div>
                                <div class="image-preview" id="image-preview" style="display: none;">
                                    <img id="preview-img" src="" alt="Aperçu">
                                    <button type="button" class="remove-image" id="remove-image">
                                        <i class='bx bx-x'></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <small style="color: var(--neutral-500);">Formats acceptés : PNG, JPG, GIF (max 5MB)</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primaire" style="padding: var(--espacement-sm) var(--espacement-lg);">Enregistrer</button>
                        <button type="button" class="btn btn-secondaire" id="cancel-btn" style="padding: var(--espacement-sm) var(--espacement-lg);">Annuler</button>
                    </div>
                </form>
            </div>

            <!-- Liste des produits -->
            <div class="products-table">
                <div class="table-header">
                    <h2>Liste des produits</h2>
                    <div class="table-stats">
                        <span id="total-products"><?php echo count($products); ?> produit(s)</span>
                    </div>
                </div>

                <div class="table-container">
                    <table id="products-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="products-tbody">
                            <?php if (count($products) === 0): ?>
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class='bx bx-package'></i>
                                    <p>Aucun produit trouvé</p>
                                    <button class="btn btn-primaire" id="add-first-product-btn" style="padding: var(--espacement-sm) var(--espacement-lg);">
                                        Ajouter votre premier produit
                                    </button>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars(str_replace('./assets/', '../assets/', $product['image'])); ?>" alt="<?php echo htmlspecialchars($product['nom']); ?>" class="product-thumbnail">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['nom']); ?></td>
                                    <td>
                                        <span class="category-badge category-<?php echo $product['categorie']; ?>">
                                            <?php
                                            $categories = [
                                                'tables' => 'Tables',
                                                'etageres' => 'Étagères',
                                                'bureaux' => 'Bureaux',
                                                'rangements' => 'Rangements'
                                            ];
                                            echo $categories[$product['categorie']] ?? $product['categorie'];
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($product['prix'], 0, ',', ' '); ?> FCFA</td>
                                    <td>
                                        <span class="stock-status <?php echo $product['stock'] > 0 ? 'in-stock' : 'out-of-stock'; ?>">
                                            <?php echo $product['stock'] > 0 ? $product['stock'] . ' en stock' : 'Rupture'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-icon edit-btn" onclick="editProduct(<?php echo $product['id']; ?>, '<?php echo addslashes($product['nom']); ?>', <?php echo $product['prix']; ?>, '<?php echo $product['categorie']; ?>', <?php echo $product['stock']; ?>, '<?php echo addslashes($product['description']); ?>', '<?php echo addslashes($product['image']); ?>')" title="Modifier">
                                                <i class='bx bx-edit'></i>
                                            </button>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                <button type="submit" class="btn-icon delete-btn" title="Supprimer">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <style>
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--espacement-2xl);
    }

    .admin-form {
        background: var(--neutral-100);
        padding: var(--espacement-2xl);
        border-radius: var(--rayon-lg);
        box-shadow: var(--ombre-legere);
        margin-bottom: var(--espacement-3xl);
        border: 1px solid var(--neutral-200);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--espacement-lg);
        margin-bottom: var(--espacement-lg);
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        margin-bottom: var(--espacement-xs);
        font-weight: 500;
        color: var(--wood-dark);
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: var(--espacement-sm);
        border: 2px solid var(--neutral-200);
        border-radius: var(--rayon-md);
        font-family: var(--font-corps);
        font-size: var(--font-size-base);
        transition: border-color var(--transition-rapide);
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--wood-light);
        box-shadow: 0 0 0 3px rgba(190, 138, 74, 0.1);
    }

    .form-actions {
        display: flex;
        gap: var(--espacement-md);
        margin-top: var(--espacement-lg);
    }

    .products-table {
        background: var(--neutral-100);
        border-radius: var(--rayon-lg);
        overflow: hidden;
        box-shadow: var(--ombre-legere);
        border: 1px solid var(--neutral-200);
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--espacement-lg);
        background: var(--wood-light);
        color: var(--neutral-100);
    }

    .table-container {
        overflow-x: auto;
    }

    #products-table {
        width: 100%;
        border-collapse: collapse;
    }

    #products-table th,
    #products-table td {
        padding: var(--espacement-md);
        text-align: left;
        border-bottom: 1px solid var(--neutral-200);
    }

    #products-table th {
        background: var(--neutral-50);
        font-weight: 600;
        color: var(--wood-dark);
    }

    .product-thumbnail {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: var(--rayon-sm);
    }

    .category-badge {
        padding: var(--espacement-xs) var(--espacement-sm);
        border-radius: var(--rayon-sm);
        font-size: var(--font-size-xs);
        font-weight: 500;
        text-transform: uppercase;
    }

    .category-tables { background: rgba(59, 130, 246, 0.1); color: #1d4ed8; }
    .category-etageres { background: rgba(16, 185, 129, 0.1); color: #047857; }
    .category-bureaux { background: rgba(245, 158, 11, 0.1); color: #d97706; }
    .category-rangements { background: rgba(139, 69, 19, 0.1); color: #9a3412; }

    .stock-status {
        font-weight: 500;
    }

    .stock-status.in-stock {
        color: #16a34a;
    }

    .stock-status.out-of-stock {
        color: #dc2626;
    }

    .action-buttons {
        display: flex;
        gap: var(--espacement-xs);
    }

    .btn-icon {
        border: none;
        font-size: 1.3rem;
        border-radius: var(--rayon-md);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all var(--transition-rapide);
    }

    .edit-btn {
        background: var(--wood-light);
        color: var(--neutral-100);
    }

    .edit-btn:hover {
        background: var(--accent);
        transform: translateY(-1px);
    }

    .delete-btn {
        background: #dc2626;
        color: var(--neutral-100);
    }

    .delete-btn:hover {
        background: #b91c1c;
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: var(--espacement-3xl);
        color: var(--muted);
    }

    .empty-state i {
        font-size: var(--font-size-4xl);
        margin-bottom: var(--espacement-md);
        display: block;
    }

    .notification {
        position: fixed;
        top: 100px;
        right: 20px;
        padding: var(--espacement-md) var(--espacement-lg);
        border-radius: var(--rayon-md);
        box-shadow: var(--ombre-forte);
        z-index: 1001;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        gap: var(--espacement-sm);
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification-success {
        background: #16a34a;
        color: var(--neutral-100);
    }

    .notification-error {
        background: #dc2626;
        color: var(--neutral-100);
    }

    .notification-info {
        background: var(--wood-light);
        color: var(--neutral-100);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .admin-header {
            flex-direction: column;
            gap: var(--espacement-md);
            text-align: center;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .table-header {
            flex-direction: column;
            gap: var(--espacement-sm);
        }

        #products-table th,
        #products-table td {
            padding: var(--espacement-sm);
        }

        .product-thumbnail {
            width: 40px;
            height: 40px;
        }
    }
    </style>

    <style>
    /* Styles pour l'upload d'image */
    .image-upload-container {
        position: relative;
    }

    .image-upload-area {
        border: 2px dashed var(--neutral-300);
        border-radius: var(--rayon-md);
        padding: var(--espacement-xl);
        text-align: center;
        cursor: pointer;
        transition: all var(--transition-rapide);
        background: var(--neutral-50);
        position: relative;
        overflow: hidden;
    }

    .image-upload-area:hover {
        border-color: var(--wood-light);
        background: rgba(190, 138, 74, 0.05);
    }

    .upload-placeholder {
        color: var(--muted);
    }

    .upload-placeholder i {
        font-size: var(--font-size-4xl);
        margin-bottom: var(--espacement-md);
        display: block;
    }

    .upload-placeholder p {
        margin: var(--espacement-sm) 0;
        font-weight: 500;
    }

    .upload-placeholder small {
        color: var(--neutral-500);
        font-size: var(--font-size-sm);
    }

    .image-preview {
        position: relative;
        display: inline-block;
    }

    .image-preview img {
        max-width: 100%;
        max-height: 200px;
        border-radius: var(--rayon-md);
        box-shadow: var(--ombre-legere);
    }

    .remove-image {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #dc2626;
        color: var(--neutral-100);
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--font-size-sm);
        transition: all var(--transition-rapide);
    }

    .remove-image:hover {
        background: #b91c1c;
        transform: scale(1.1);
    }
    </style>

    <script>
    // Gestion du formulaire
    document.getElementById('add-product-btn').addEventListener('click', () => showForm());
    document.getElementById('add-first-product-btn')?.addEventListener('click', () => showForm());
    document.getElementById('cancel-btn').addEventListener('click', () => hideForm());

    // Gestion de l'upload d'image
    setupImageUpload();

    function setupImageUpload() {
        const imageInput = document.getElementById('product-image');
        const uploadArea = document.getElementById('image-upload-area');
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        const removeBtn = document.getElementById('remove-image');

        if (uploadArea) {
            uploadArea.addEventListener('click', () => {
                imageInput.click();
            });
        }

        if (imageInput) {
            imageInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    handleImageUpload(file);
                }
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                removeImage();
            });
        }
    }

    function handleImageUpload(file) {
        // Validation de l'image
        if (!validateImage(file)) {
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            displayImagePreview(e.target.result);
        };
        reader.readAsDataURL(file);
    }

    function validateImage(file) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

        if (!allowedTypes.includes(file.type)) {
            alert('Format d\'image non supporté. Utilisez JPG, PNG, GIF ou WebP.');
            return false;
        }

        if (file.size > maxSize) {
            alert('L\'image est trop volumineuse. Taille maximum : 5MB.');
            return false;
        }

        return true;
    }

    function displayImagePreview(imageData) {
        const uploadPlaceholder = document.querySelector('.upload-placeholder');
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');

        if (uploadPlaceholder) uploadPlaceholder.style.display = 'none';
        if (preview) preview.style.display = 'block';
        if (previewImg) previewImg.src = imageData;
    }

    function removeImage() {
        const imageInput = document.getElementById('product-image');
        const uploadPlaceholder = document.querySelector('.upload-placeholder');
        const preview = document.getElementById('image-preview');

        // Réinitialiser l'input file
        if (imageInput) {
            imageInput.value = '';
        }

        // Masquer l'aperçu et afficher le placeholder
        if (preview) preview.style.display = 'none';
        if (uploadPlaceholder) uploadPlaceholder.style.display = 'block';
    }

    function showForm(product = null) {
        const form = document.getElementById('product-form');
        const formTitle = document.getElementById('form-title');
        const actionInput = document.getElementById('action');

        if (product) {
            formTitle.textContent = 'Modifier le produit';
            populateForm(product);
            actionInput.value = 'edit';
        } else {
            formTitle.textContent = 'Ajouter un produit';
            resetForm();
            actionInput.value = 'add';
        }

        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    }

    function hideForm() {
        const form = document.getElementById('product-form');
        form.style.display = 'none';
        resetForm();
    }

    function populateForm(product) {
        document.getElementById('product-id').value = product.id;
        document.getElementById('product-name').value = product.nom;
        document.getElementById('product-price').value = product.prix;
        document.getElementById('product-category').value = product.categorie;
        document.getElementById('product-stock').value = product.stock;
        document.getElementById('product-description').value = product.description;

        // Pour l'image, on ne peut pas pré-remplir le champ file
        // L'utilisateur devra re-sélectionner l'image s'il veut la changer
    }

    function resetForm() {
        document.getElementById('product-form-element').reset();
        document.getElementById('product-id').value = '';
        removeImage(); // Réinitialiser l'upload d'image
    }

    function editProduct(id, nom, prix, categorie, stock, description, image) {
        const product = { id, nom, prix, categorie, stock, description, image };
        showForm(product);
    }


    </script>
</body>
</html>
