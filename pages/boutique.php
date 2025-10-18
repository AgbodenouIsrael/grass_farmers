<?php
session_start();
include '../src/database/db.php';
include '../src/cart_functions.php';

// Gestion des actions du panier
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'ajouter_panier':
                $result = ajouterAuPanier($_POST['produit_id'], $_POST['quantite'] ?? 1);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;

            case 'modifier_quantite':
                $result = modifierQuantitePanier($_POST['produit_id'], $_POST['quantite']);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;

            case 'supprimer_panier':
                $result = supprimerDuPanier($_POST['produit_id']);
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;

            case 'vider_panier':
                $result = viderPanier();
                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
                break;
        }
    }
}

// Gestion des filtres
$currentCategory = isset($_GET['categorie']) ? $_GET['categorie'] : 'tous';

// Récupérer tous les produits
try {
    $stmt = $bdd->query("SELECT * FROM produits ORDER BY created_at DESC");
    $allProducts = $stmt->fetchAll();

} catch(PDOException $e) {
    $allProducts = [];
}

// Filtrer les produits
$products = array_filter($allProducts, function($product) use ($currentCategory) {
    // Filtre par catégorie uniquement
    return $currentCategory === 'tous' || $product['categorie'] === $currentCategory;
});



// Fonction pour obtenir le label de catégorie
function getCategoryLabel($category) {
    $labels = [
        'tables' => 'Tables',
        'etageres' => 'Étagères',
        'bureaux' => 'Bureaux',
        'rangements' => 'Rangements'
    ];
    return $labels[$category] ?? $category;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Boutique - AYOUBDECOR | Meubles prêts à l'emploi</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../styles/acceuil.css">
    <link rel="stylesheet" href="../styles/boutique.css">
    <link rel="icon" type="image/x-icon" href="../assets/ayoubdecor_logo2.png">
    <style>
    /* Styles pour la boutique PHP */
    .search-container {
        text-align: center;
        margin-bottom: var(--espacement-lg);
    }

    .search-input {
        width: 100%;
        max-width: 400px;
        padding: var(--espacement-sm) var(--espacement-lg);
        padding-right: 40px;
        border: 2px solid var(--neutral-200);
        border-radius: var(--rayon-md);
        font-family: var(--font-corps);
        font-size: var(--font-size-base);
        transition: border-color var(--transition-rapide);
    }

    .search-input:focus {
        outline: none;
        border-color: var(--wood-light);
        box-shadow: 0 0 0 3px rgba(190, 138, 74, 0.1);
    }

    .no-products {
        grid-column: 1 / -1;
        text-align: center;
        padding: var(--espacement-3xl);
        color: var(--muted);
    }

    .no-products i {
        font-size: var(--font-size-4xl);
        margin-bottom: var(--espacement-md);
        display: block;
    }

    .no-products h3 {
        margin-bottom: var(--espacement-sm);
        color: var(--wood-dark);
    }

    .filtre-btn {
        display: inline-block;
        padding: var(--espacement-sm) var(--espacement-md);
        margin: 0 var(--espacement-xs);
        border: 2px solid var(--neutral-200);
        border-radius: var(--rayon-md);
        text-decoration: none;
        color: var(--wood-dark);
        font-weight: 500;
        transition: all var(--transition-rapide);
    }

    .filtre-btn:hover {
        border-color: var(--wood-light);
        color: var(--wood-light);
        background: rgba(190, 138, 74, 0.05);
    }

    .filtre-btn.active {
        border-color: var(--wood-light);
        background: var(--wood-light);
        color: var(--neutral-100);
    }

    .badge-stock {
        padding: var(--espacement-xs) var(--espacement-sm);
        border-radius: var(--rayon-sm);
        font-size: var(--font-size-xs);
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: var(--espacement-sm);
        display: inline-block;
    }

    .badge-stock.en-stock {
        background: rgba(16, 185, 129, 0.1);
        color: #047857;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .badge-stock.rupture {
        background: rgba(220, 38, 38, 0.1);
        color: #dc2626;
        border: 1px solid rgba(220, 38, 38, 0.2);
    }

    .produit-description {
        word-wrap: break-word;
        overflow-wrap: break-word;
        hyphens: auto;
        line-height: 1.5;
        margin-bottom: var(--espacement-sm);
    }

    /* Styles pour le panier PHP */
    .empty-cart {
        text-align: center;
        color: var(--muted);
        font-style: italic;
        padding: var(--espacement-md);
    }

    .empty-cart i {
        font-size: var(--font-size-2xl);
        margin-bottom: var(--espacement-sm);
        display: block;
    }

    .panier-items {
        margin-bottom: var(--espacement-lg);
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--espacement-sm) 0;
        border-bottom: 1px solid var(--neutral-200);
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-item-info h4 {
        margin: 0 0 var(--espacement-xs) 0;
        font-size: var(--font-size-base);
        color: var(--wood-dark);
    }

    .cart-item-controls {
        display: flex;
        align-items: center;
        gap: var(--espacement-xs);
    }

    .quantity-btn {
        width: 24px;
        height: 24px;
        border: 1px solid var(--neutral-300);
        background: var(--neutral-100);
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        transition: all var(--transition-rapide);
        color: var(--wood-dark);
    }

    .quantity-btn:hover:not(:disabled) {
        background: var(--wood-light);
        color: var(--neutral-100);
    }

    .quantity-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .quantity {
        min-width: 30px;
        text-align: center;
        font-weight: 500;
    }

    .cart-item-price {
        display: flex;
        align-items: center;
        gap: var(--espacement-sm);
    }

    .cart-item-price span {
        font-weight: 600;
        color: var(--wood-dark);
    }

    .remove-btn {
        width: 24px;
        height: 24px;
        border: none;
        background: var(--neutral-300);
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--neutral-600);
        font-size: var(--font-size-lg);
        transition: all var(--transition-rapide);
    }

    .remove-btn:hover {
        background: #dc2626;
        color: var(--neutral-100);
    }

    .panier-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: var(--espacement-md);
        padding-top: var(--espacement-md);
        border-top: 1px solid var(--neutral-200);
    }

    .panier-total strong {
        color: var(--wood-dark);
        font-size: var(--font-size-lg);
    }

    .notification {
        position: fixed;
        top: 100px;
        right: 20px;
        padding: var(--espacement-sm) var(--espacement-md);
        border-radius: var(--rayon-md);
        box-shadow: var(--ombre-forte);
        z-index: 1001;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        gap: var(--espacement-sm);
        max-width: 300px;
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

    /* Responsive */
    @media (max-width: 768px) {
        .grille-produits {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: var(--espacement-lg);
        }

        .filtre-btn {
            display: block;
            margin: var(--espacement-xs) 0;
            text-align: center;
        }

        .badge-stock {
            font-size: var(--font-size-xs);
            padding: var(--espacement-xs);
        }

        .panier-total {
            flex-direction: column;
            gap: var(--espacement-sm);
            text-align: center;
        }

        .cart-item {
            flex-direction: column;
            align-items: flex-start;
            gap: var(--espacement-sm);
        }

        .cart-item-price {
            width: 100%;
            justify-content: space-between;
        }
    }

    @media (max-width: 480px) {
        .grille-produits {
            grid-template-columns: 1fr;
        }

        .notification {
            left: 10px;
            right: 10px;
            max-width: none;
        }
    }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="en-tete" id="en-tete">
        <div class="conteneur">
            <div class="header-contenu">
                <a href="../" class="logo"><img src="../assets/ayoubdecor_logoo.png" alt="ayoub_logo">AYOUBDECOR</a>

                <nav class="menu-principal" id="menu-principal">
                    <ul>
                        <li><a href="../">Accueil</a></li>
                        <li><a href="service.php">Services</a></li>
                        <!-- <li><a href="galerie.html">Galerie</a></li> -->
                        <li><a href="apropos.php">À propos</a></li>
                        <li><a href="boutique.php" class="active">Boutique</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </nav>

                <div class="header-actions">
                    <a href="../admin/login.php" class="btn btn-secondaire" style="padding: 10px 15px;">Admin</a>
                    <a href="devis.php" class="btn btn-primaire" style="padding: 15px 20px;">Demander un devis</a>
                    <button class="menu-burger" id="menu-burger" aria-label="Ouvrir le menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Section Hero -->
    <section class="section-hero" style="min-height: 50vh; padding-top: 100px;">
        <div class="conteneur">
            <div class="section-header" style="text-align: center;">
                <h1>Notre boutique</h1>
                <p>Découvrez notre sélection de meubles prêts à l'emploi</p>
            </div>
        </div>
    </section>

    <!-- Section Filtres boutique -->
    <section class="section" style="background-color: var(--neutral-50);">
        <div class="conteneur">
            <!-- Filtres par catégorie -->
            <div class="filtres-boutique">
                <a href="boutique.php?categorie=tous"
                   class="filtre-btn <?php echo $currentCategory === 'tous' ? 'active' : ''; ?>">Tous les produits</a>
                <a href="boutique.php?categorie=tables"
                   class="filtre-btn <?php echo $currentCategory === 'tables' ? 'active' : ''; ?>">Tables</a>
                <a href="boutique.php?categorie=etageres"
                   class="filtre-btn <?php echo $currentCategory === 'etageres' ? 'active' : ''; ?>">Étagères</a>
                <a href="boutique.php?categorie=bureaux"
                   class="filtre-btn <?php echo $currentCategory === 'bureaux' ? 'active' : ''; ?>">Bureaux</a>
                <a href="boutique.php?categorie=rangements"
                   class="filtre-btn <?php echo $currentCategory === 'rangements' ? 'active' : ''; ?>">Rangements</a>
            </div>
        </div>
    </section>

    <!-- Section Produits -->
    <section class="section">
        <div class="conteneur">
            <div class="grille-produits" id="grille-produits">
                <?php if (empty($products)): ?>
                <div class="no-products">
                    <i class='bx bx-search-alt'></i>
                    <h3>Aucun produit trouvé</h3>
                    <p>
                        <?php if ($currentCategory !== 'tous'): ?>
                            Aucun produit dans la catégorie "<strong><?php echo getCategoryLabel($currentCategory); ?></strong>".
                        <?php else: ?>
                            Aucun produit disponible pour le moment.
                        <?php endif; ?>
                    </p>
                    <?php if ($currentCategory !== 'tous'): ?>
                        <a href="boutique.php" class="btn btn-primaire" style="padding: var(--espacement-sm) var(--espacement-lg);">Voir tous les produits</a>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <div class="produit" data-categorie="<?php echo htmlspecialchars($product['categorie']); ?>" data-stock="<?php echo $product['stock']; ?>">
                        <img src="../assets/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['nom']); ?>" class="produit-image" loading="lazy" onerror="this.style.display='none'">
                        <div class="produit-contenu">
                            <div class="badge-stock <?php echo $product['stock'] > 0 ? 'en-stock' : 'rupture'; ?>">
                                <?php echo $product['stock'] > 0 ? 'En stock' : 'Rupture de stock'; ?>
                            </div>
                            <h3><?php echo htmlspecialchars($product['nom']); ?></h3>
                            <div class="produit-prix"><?php echo number_format($product['prix'], 0, ',', ' '); ?> FCFA</div>
                            <p class="produit-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="produit-details">
                                <p><strong>Catégorie :</strong> <?php echo getCategoryLabel($product['categorie']); ?></p>
                                <p><strong>Stock :</strong> <?php echo $product['stock'] > 0 ? $product['stock'] . ' disponible(s)' : 'Indisponible'; ?></p>
                            </div>
                            <div class="produit-actions">
                                <?php if ($product['stock'] > 0): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="ajouter_panier">
                                        <input type="hidden" name="produit_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="quantite" value="1">
                                        <button type="submit" class="btn btn-primaire" style="padding: var(--espacement-sm) var(--espacement-lg); font-size: 1.3rem; margin: 0 auto; display: block;">
                                            <i class='bx bx-cart-add'></i>
                                            Ajouter au panier
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondaire" disabled style="cursor: not-allowed; opacity: 0.6; padding: var(--espacement-sm) var(--espacement-lg); font-size: 1.3rem; margin: 0 auto; display: block;">
                                        <i class='bx bx-time-five'></i>
                                        Indisponible
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <aside class="card" style="padding:16px; margin-top:16px" aria-labelledby="panier-title">
            <h2 id="panier-title">Panier</h2>

            <?php if ($message): ?>
            <div class="notification notification-<?php echo $messageType; ?> show" style="margin-bottom: 15px;">
                <i class='bx <?php echo $messageType === 'success' ? 'bx-check-circle' : 'bx-error-circle'; ?>'></i>
                <span><?php echo $message; ?></span>
            </div>
            <?php endif; ?>

            <?php $panier = getPanier(); ?>
            <?php if (empty($panier)): ?>
                <div class="empty-cart">
                    <i class='bx bx-package'></i>
                    <p>Votre panier est vide</p>
                </div>
            <?php else: ?>
                <div class="panier-items">
                    <?php foreach ($panier as $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <h4><?php echo htmlspecialchars($item['nom']); ?></h4>
                            <div class="cart-item-controls">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="modifier_quantite">
                                    <input type="hidden" name="produit_id" value="<?php echo $item['produit_id']; ?>">
                                    <input type="hidden" name="quantite" value="<?php echo $item['quantite'] - 1; ?>">
                                    <button type="submit" class="quantity-btn" <?php echo $item['quantite'] <= 1 ? 'disabled' : ''; ?>>-</button>
                                </form>
                                <span class="quantity"><?php echo $item['quantite']; ?></span>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="modifier_quantite">
                                    <input type="hidden" name="produit_id" value="<?php echo $item['produit_id']; ?>">
                                    <input type="hidden" name="quantite" value="<?php echo $item['quantite'] + 1; ?>">
                                    <button type="submit" class="quantity-btn" <?php echo $item['quantite'] >= $item['stock'] ? 'disabled' : ''; ?>>+</button>
                                </form>
                            </div>
                        </div>
                        <div class="cart-item-price">
                            <span><?php echo formatPrix($item['sous_total']); ?></span>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="supprimer_panier">
                                <input type="hidden" name="produit_id" value="<?php echo $item['produit_id']; ?>">
                                <button type="submit" class="remove-btn" title="Supprimer">×</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="panier-total">
                    <strong>Total: <?php echo formatPrix(calculerTotalPanier()); ?> FCFA</strong>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="vider_panier">
                        <button type="submit" class="btn btn-secondaire" onclick="return confirm('Vider le panier ?')" style="padding: var(--espacement-sm) var(--espacement-lg);">Vider</button>
                    </form>
                </div>

                <div style="margin-top: 15px;">
                    <a href="../src/controllers/commande.php"><button class="btn btn-primaire" style="width: 100%; padding: var(--espacement-sm) var(--espacement-lg);">Procéder au paiement</button></a>
                </div>
            <?php endif; ?>
        </aside>
    </section>

    <!-- Section Informations livraison -->
    <section class="section" style="background-color: var(--neutral-50);">
        <div class="conteneur">
            <div class="section-header" style="text-align: center;">
                <h2>Livraison et garanties</h2>
                <p>Tout ce que vous devez savoir sur votre achat</p>
            </div>

            <div class="grille-services">
                <div class="carte-service">
                    <div class="icone-service">🚚</div>
                    <h3>Livraison gratuite</h3>
                    <p>Livraison gratuite en France métropolitaine pour toute commande supérieure à 300€. Délai de livraison : 5-7 jours ouvrés.</p>
                </div>

                <div class="carte-service">
                    <div class="icone-service">🛡️</div>
                    <h3>Garantie 2 ans</h3>
                    <p>Tous nos meubles bénéficient d'une garantie de 2 ans sur la fabrication et les matériaux. Service après-vente inclus.</p>
                </div>

                <div class="carte-service">
                    <div class="icone-service">🔧</div>
                    <h3>Montage inclus</h3>
                    <p>Montage professionnel inclus dans le prix. Nos artisans se déplacent pour assembler votre meuble sur place.</p>
                </div>

                <div class="carte-service">
                    <div class="icone-service">↩️</div>
                    <h3>Retour sous 14 jours</h3>
                    <p>Droit de rétractation de 14 jours. Retour gratuit si le produit ne correspond pas à vos attentes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section CTA personnalisé -->
    <section class="section" style="background-color: var(--wood-dark); color: var(--neutral-100);">
        <div class="conteneur">
            <div class="section-header" style="text-align: center;">
                <h2 style="color: var(--neutral-100);">Vous ne trouvez pas votre bonheur ?</h2>
                <p style="color: var(--neutral-200);">Créez votre meuble sur mesure selon vos envies et vos besoins</p>
                <div style="margin-top: var(--espacement-xl);">
                    <a href="devis.php" class="btn btn-primaire" style="margin-right: var(--espacement-md); padding: var(--espacement-sm) var(--espacement-lg);">Demander un devis</a>
                    <a href="service.php" class="btn btn-secondaire" style="border-color: var(--neutral-100); color: var(--neutral-100); padding: var(--espacement-sm) var(--espacement-lg);">Nos services</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="pied-page">
        <div class="conteneur">
            <div class="contenu-footer">
                <div class="colonne-footer">
                    <h3>AYOUBDECOR</h3>
                    <p>Fabricant de meubles modernes sur mesure. Conception, fabrication et pose de mobilier contemporain pour particuliers et professionnels.</p>
                    <div class="reseaux-sociaux">
                        <a href="#" class="reseau-social" aria-label="Facebook">
                            <i class='bx bxl-facebook-circle'></i>
                        </a>
                        <a href="#" class="reseau-social" aria-label="Instagram">
                            <i class='bx bxl-instagram-alt'></i>
                        </a>
                        <a href="#" class="reseau-social" aria-label="LinkedIn">
                            <i class='bx bxl-linkedin-square'></i>
                        </a>
                    </div>
                </div>

                <div class="colonne-footer">
                    <h3>Services</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: var(--espacement-xs);"><a href="service.php#fabrication">Fabrication sur mesure</a></li>
                        <li style="margin-bottom: var(--espacement-xs);"><a href="service.php#pose">Pose et installation</a></li>
                        <li style="margin-bottom: var(--espacement-xs);"><a href="service.php#renovation">Rénovation</a></li>
                        <li style="margin-bottom: var(--espacement-xs);"><a href="service.php#amenagement">Aménagement</a></li>
                    </ul>
                </div>

                <div class="colonne-footer">
                    <h3>Contact</h3>
                    <div style="margin-bottom: var(--espacement-sm);">
                        <strong>Téléphone :</strong><br>
                        <a href="tel:+33123456789">01 23 45 67 89</a>
                    </div>
                    <div style="margin-bottom: var(--espacement-sm);">
                        <strong>Email :</strong><br>
                        <a href="mailto:contact@ayoubdecor.fr">contact@ayoubdecor.fr</a>
                    </div>
                    <div>
                        <strong>Adresse :</strong><br>
                        123 Rue de l'Artisan<br>
                        75000 Paris, France
                    </div>
                </div>

                <div class="colonne-footer">
                    <h3>Horaires</h3>
                    <div style="margin-bottom: var(--espacement-xs);">
                        <strong>Lundi - Vendredi :</strong><br>
                        8h00 - 18h00
                    </div>
                    <div style="margin-bottom: var(--espacement-xs);">
                        <strong>Samedi :</strong><br>
                        9h00 - 17h00
                    </div>
                    <div>
                        <strong>Dimanche :</strong><br>
                        Fermé
                    </div>
                </div>
            </div>

            <div class="copyright">
                <p>&copy; 2024 AYOUBDECOR. Tous droits réservés. | <a href="#" style="color: var(--neutral-300);">Mentions légales</a> | <a href="#" style="color: var(--neutral-300);">Politique de confidentialité</a></p>
            </div>
        </div>
    </footer>

    <script>
    // Gestion des notifications
    document.addEventListener('DOMContentLoaded', function() {
        const notifications = document.querySelectorAll('.notification');

        notifications.forEach(function(notification) {
            // Masquer automatiquement après 5 secondes
            setTimeout(function() {
                notification.style.transform = 'translateX(100%)';
                setTimeout(function() {
                    notification.remove();
                }, 300); // Attendre la fin de la transition
            }, 3000);

            // Masquer au clic
            notification.addEventListener('click', function() {
                notification.style.transform = 'translateX(100%)';
                setTimeout(function() {
                    notification.remove();
                }, 300);
            });
        });
    });
    </script>

</body>

</html>
