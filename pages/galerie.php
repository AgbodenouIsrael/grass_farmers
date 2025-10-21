<?php
// Page Galerie - AYOUBDECOR
// Configuration de la page
$page_title = "Galerie - AYOUBDECOR | Nos Réalisations de Meubles sur Mesure";
$page_description = "Découvrez notre galerie de réalisations : cuisines, salons, bureaux, chambres et meubles sur mesure";
$current_page = "galerie";

// Inclure la connexion à la base de données
require_once __DIR__ . '/../src/database/db.php';

// Fonction pour récupérer les images de la galerie
function getGalleryImages($categorie = null) {
    global $bdd;
    try {
        $sql = "SELECT * FROM galerie WHERE statut = 1";
        $params = [];

        if ($categorie && $categorie !== 'all') {
            $sql .= " AND categorie = ?";
            $params[] = $categorie;
        }

        $sql .= " ORDER BY ordre ASC, created_at DESC";

        $stmt = $bdd->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Erreur lors de la récupération des images galerie: ' . $e->getMessage());
        return [];
    }
}

// Fonction pour compter les images par catégorie
function countImagesByCategory() {
    global $bdd;
    try {
        $sql = "SELECT categorie, COUNT(*) as count FROM galerie WHERE statut = 1 GROUP BY categorie";
        $stmt = $bdd->query($sql);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        error_log('Erreur lors du comptage des images: ' . $e->getMessage());
        return [];
    }
}

// Récupérer toutes les images pour la galerie
$galleryImages = getGalleryImages();
$categoryCounts = countImagesByCategory();

// Catégories disponibles
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
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">

    <!-- Polices Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Icônes -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="../styles/acceuil.css">
    <link rel="icon" type="image/x-icon" href="../assets/ayoubdecor_logoo.png">

    <!-- Styles spécifiques à la galerie -->
    <style>
        /* ========================================
           STYLES POUR LA GALERIE
           ======================================== */

        /* Hero Section */
        .galerie-hero {
            background: linear-gradient(135deg, var(--wood-light) 0%, var(--wood-dark) 100%);
            color: var(--neutral-100);
            padding: var(--espacement-4xl) 0;
            text-align: center;
            position: relative;
            overflow: hidden;

        }

        .galerie-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../assets/ayoubdecor_logoo.png') center center no-repeat;
            background-size: 200px;
            opacity: 0.05;
            z-index: 1;
        }

        .galerie-hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .galerie-hero h1 {
            font-size: var(--font-size-4xl);
            margin-bottom: var(--espacement-lg);
            font-weight: 700;
        }

        .galerie-hero p {
            font-size: var(--font-size-lg);
            margin-bottom: var(--espacement-2xl);
            opacity: 0.9;
        }

        .galerie-hero h1,p{
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            max-width: 600px;
            margin: auto;
        }

        /* Filtres */
        .galerie-filters {
            padding: var(--espacement-2xl) 0;
            background: var(--neutral-50);
            border-bottom: 1px solid var(--neutral-200);
        }

        .filters-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: var(--espacement-md);
        }

        .filter-btn {
            padding: var(--espacement-sm) var(--espacement-lg);
            border: 2px solid var(--wood-light);
            background: var(--neutral-100);
            color: var(--wood-dark);
            border-radius: var(--rayon-lg);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-rapide);
            font-size: var(--font-size-base);
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: var(--wood-light);
            color: var(--neutral-100);
            transform: translateY(-2px);
            box-shadow: var(--ombre-moyenne);
        }

        /* Galerie */
        .galerie-main {
            padding: var(--espacement-4xl) 0;
            background: var(--neutral-100);
        }

        .galerie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: var(--espacement-xl);
            width: 100%;
        }

        .galerie-card {
            background: var(--neutral-100);
            border-radius: var(--rayon-xl);
            overflow: hidden;
            box-shadow: var(--ombre-legere);
            transition: all var(--transition-normale);
            cursor: pointer;
            position: relative;
            opacity: 1;
            transform: scale(1);
        }

        .galerie-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--ombre-moyenne);
        }

        .galerie-card.hidden {
            display: none !important;
        }

        .galerie-image {
            width: 100%;
            height: 280px;
            object-fit: cover;
            display: block;
            transition: all var(--transition-normale);
        }

        .galerie-card:hover .galerie-image {
            transform: scale(1.05);
        }

        .galerie-meta {
            padding: var(--espacement-lg);
            background: var(--neutral-100);
        }

        .galerie-title {
            color: var(--wood-dark);
            margin-bottom: var(--espacement-xs);
            font-size: var(--font-size-lg);
            font-weight: 600;
        }

        .galerie-description {
            color: var(--muted);
            font-size: var(--font-size-sm);
            margin: 0;
        }

        .galerie-category {
            position: absolute;
            top: var(--espacement-md);
            right: var(--espacement-md);
            background: var(--wood-light);
            color: var(--neutral-100);
            padding: var(--espacement-xs) var(--espacement-sm);
            border-radius: var(--rayon-md);
            font-size: var(--font-size-xs);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Lightbox */
        .lightbox {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: all var(--transition-normale);
        }

        .lightbox.show {
            opacity: 1;
            visibility: visible;
        }

        .lightbox-content {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            border-radius: var(--rayon-md);
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
        }

        .lightbox-close {
            position: absolute;
            top: var(--espacement-xl);
            right: var(--espacement-xl);
            color: var(--neutral-100);
            font-size: var(--font-size-3xl);
            cursor: pointer;
            z-index: 10001;
            transition: all var(--transition-rapide);
        }

        .lightbox-close:hover {
            transform: scale(1.2);
            color: var(--wood-light);
        }

        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            color: var(--neutral-100);
            font-size: var(--font-size-3xl);
            cursor: pointer;
            padding: var(--espacement-lg);
            transition: all var(--transition-rapide);
            z-index: 10001;
        }

        .lightbox-nav:hover {
            color: var(--wood-light);
            transform: translateY(-50%) scale(1.1);
        }

        .lightbox-prev {
            left: var(--espacement-xl);
        }

        .lightbox-next {
            right: var(--espacement-xl);
        }

        /* CTA Section */
        .galerie-cta {
            background: linear-gradient(135deg, var(--wood-dark) 0%, var(--wood-light) 100%);
            color: var(--neutral-100);
            padding: var(--espacement-4xl) 0;
            text-align: center;
        }

        .galerie-cta h2 {
            color: var(--neutral-100);
            margin-bottom: var(--espacement-lg);
            font-size: var(--font-size-3xl);
        }

        .galerie-cta p {
            font-size: var(--font-size-lg);
            margin-bottom: var(--espacement-2xl);
            opacity: 0.9;
        }

        .cta-buttons {
            display: flex;
            gap: var(--espacement-lg);
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-buttons .btn {
            padding: var(--espacement-md) var(--espacement-2xl);
            font-size: var(--font-size-lg);
            font-weight: 600;
        }

        /* Stats Section */
        .galerie-stats {
            padding: var(--espacement-4xl) 0;
            background: var(--neutral-50);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--espacement-2xl);
        }

        .stat-item {
            text-align: center;
            padding: var(--espacement-2xl);
            background: var(--neutral-100);
            border-radius: var(--rayon-xl);
            border: 1px solid var(--neutral-200);
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

        /* Responsive */
        @media (max-width: 768px) {
            .galerie-hero h1 {
                font-size: var(--font-size-3xl);
            }

            .galerie-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                gap: var(--espacement-lg);
            }

            .filters-container {
                padding: 0 var(--espacement-md);
            }

            .filter-btn {
                padding: var(--espacement-xs) var(--espacement-md);
                font-size: var(--font-size-sm);
            }

            .lightbox-content {
                max-width: 95%;
                max-height: 80%;
            }

            .lightbox-nav,
            .lightbox-close {
                font-size: var(--font-size-2xl);
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .cta-buttons .btn {
                width: 100%;
                max-width: 300px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .galerie-grid {
                grid-template-columns: 1fr;
            }

            .galerie-card {
                margin: 0 var(--espacement-sm);
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-item {
                margin: 0 var(--espacement-md);
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .fade-in-delay {
            animation: fadeInUp 0.8s ease-out 0.2s forwards;
        }

        .fade-in-delay-2 {
            animation: fadeInUp 0.8s ease-out 0.4s forwards;
        }
    </style>
</head>

<body>
    <!-- Header partagé -->
    <?php include_once __DIR__ . '/../src/header.php'; ?>

    <!-- Hero Section -->
    <section class="galerie-hero">
        <div class="galerie-hero-content">
            <h1>Nos Réalisations</h1>
            <p>Découvrez quelques-unes de nos créations sur mesure, témoignages de notre savoir-faire et de notre passion pour l'artisanat</p>
        </div>
    </section>

    <!-- Statistiques -->
    <section class="galerie-stats">
        <div class="conteneur">
            <div class="stats-grid fade-in">
                <div class="stat-item">
                    <span class="stat-number">200+</span>
                    <span class="stat-label">Projets réalisés</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">50+</span>
                    <span class="stat-label">Clients satisfaits</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">15</span>
                    <span class="stat-label">Années d'expérience</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Artisanal</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtres -->
    <section class="galerie-filters">
        <div class="conteneur">
            <div class="filters-container">
                <button class="filter-btn active" data-filter="all">Tous</button>
                <button class="filter-btn" data-filter="cuisine">Cuisines</button>
                <button class="filter-btn" data-filter="salon">Salons</button>
                <button class="filter-btn" data-filter="bureau">Bureaux</button>
                <button class="filter-btn" data-filter="chambre">Chambres</button>
                <button class="filter-btn" data-filter="sdb">Salles de bain</button>
                <button class="filter-btn" data-filter="exterieur">Extérieur</button>
            </div>
        </div>
    </section>

    <!-- Galerie -->
    <section class="galerie-main">
        <div class="conteneur">
            <div class="galerie-grid" id="galerie-grid">
                <?php if (empty($galleryImages)): ?>
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <div class="empty-icon">🖼️</div>
                    <h3>Aucune image disponible</h3>
                    <p>La galerie est actuellement vide. Nos réalisations apparaîtront bientôt ici.</p>
                </div>
                <?php else: ?>
                    <?php
                    $animationClasses = ['fade-in', 'fade-in-delay', 'fade-in-delay-2'];
                    $animationIndex = 0;
                    foreach ($galleryImages as $image):
                        $animationClass = $animationClasses[$animationIndex % count($animationClasses)];
                        $animationIndex++;
                    ?>
                    <div class="galerie-card <?php echo $animationClass; ?>" data-category="<?php echo htmlspecialchars($image['categorie']); ?>">
                        <img src="../assets/<?php echo htmlspecialchars($image['image_path']); ?>"
                             alt="<?php echo htmlspecialchars($image['titre']); ?>"
                             class="galerie-image">
                        <div class="galerie-category">
                            <?php echo htmlspecialchars($categories[$image['categorie']] ?? ucfirst($image['categorie'])); ?>
                        </div>
                        <div class="galerie-meta">
                            <h3 class="galerie-title"><?php echo htmlspecialchars($image['titre']); ?></h3>
                            <p class="galerie-description">
                                <?php echo htmlspecialchars($image['description'] ?: 'Réalisation sur mesure'); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <span class="lightbox-close" id="lightbox-close">&times;</span>
        <span class="lightbox-nav lightbox-prev" id="lightbox-prev">&#10094;</span>
        <span class="lightbox-nav lightbox-next" id="lightbox-next">&#10095;</span>
        <img class="lightbox-content" id="lightbox-img" alt="Image agrandie">
    </div>

    <!-- CTA Section -->
    <section class="galerie-cta">
        <div class="conteneur">
            <h2>Inspiré par nos réalisations ?</h2>
            <p>Contactez-nous pour créer votre propre meuble sur mesure</p>
            <div class="cta-buttons">
                <a href="devis.php" class="btn btn-primaire">
                    <i class="fas fa-file-alt"></i> Demander un devis
                </a>
                <a href="contact.php" class="btn btn-secondaire">
                    <i class="fas fa-phone"></i> Nous contacter
                </a>
            </div>
        </div>
    </section>

    <!-- Footer partagé -->
    <?php include_once __DIR__ . '/../src/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables
            const filterButtons = document.querySelectorAll('.filter-btn');
            const galerieCards = document.querySelectorAll('.galerie-card');
            const lightbox = document.getElementById('lightbox');
            const lightboxImg = document.getElementById('lightbox-img');
            const lightboxClose = document.getElementById('lightbox-close');
            const lightboxPrev = document.getElementById('lightbox-prev');
            const lightboxNext = document.getElementById('lightbox-next');

            let currentImageIndex = 0;
            let visibleImages = [];

            // Fonction de filtrage
            function filterImages(category) {
                visibleImages = [];
                galerieCards.forEach((card, index) => {
                    if (category === 'all' || card.dataset.category === category) {
                        card.classList.remove('hidden');
                        visibleImages.push(index);
                    } else {
                        card.classList.add('hidden');
                    }
                });
            }

            // Événements des boutons de filtre
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Retirer la classe active de tous les boutons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    // Ajouter la classe active au bouton cliqué
                    this.classList.add('active');

                    // Filtrer les images
                    const filterValue = this.dataset.filter;
                    filterImages(filterValue);
                });
            });

            // Fonction d'ouverture de la lightbox
            function openLightbox(index) {
                const card = galerieCards[index];
                const img = card.querySelector('.galerie-image');
                lightboxImg.src = img.src;
                lightboxImg.alt = img.alt;
                currentImageIndex = visibleImages.indexOf(index);
                lightbox.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            // Fonction de fermeture de la lightbox
            function closeLightbox() {
                lightbox.classList.remove('show');
                document.body.style.overflow = 'auto';
            }

            // Navigation dans la lightbox
            function showPrevImage() {
                if (visibleImages.length > 1) {
                    currentImageIndex = (currentImageIndex - 1 + visibleImages.length) % visibleImages.length;
                    const card = galerieCards[visibleImages[currentImageIndex]];
                    const img = card.querySelector('.galerie-image');
                    lightboxImg.src = img.src;
                    lightboxImg.alt = img.alt;
                }
            }

            function showNextImage() {
                if (visibleImages.length > 1) {
                    currentImageIndex = (currentImageIndex + 1) % visibleImages.length;
                    const card = galerieCards[visibleImages[currentImageIndex]];
                    const img = card.querySelector('.galerie-image');
                    lightboxImg.src = img.src;
                    lightboxImg.alt = img.alt;
                }
            }

            // Événements des cartes de galerie
            galerieCards.forEach((card, index) => {
                card.addEventListener('click', function() {
                    openLightbox(index);
                });
            });

            // Événements de la lightbox
            lightboxClose.addEventListener('click', closeLightbox);
            lightboxPrev.addEventListener('click', showPrevImage);
            lightboxNext.addEventListener('click', showNextImage);

            // Fermer la lightbox en cliquant en dehors
            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox) {
                    closeLightbox();
                }
            });

            // Navigation au clavier
            document.addEventListener('keydown', function(e) {
                if (!lightbox.classList.contains('show')) return;

                switch(e.key) {
                    case 'Escape':
                        closeLightbox();
                        break;
                    case 'ArrowLeft':
                        showPrevImage();
                        break;
                    case 'ArrowRight':
                        showNextImage();
                        break;
                }
            });

            // Initialisation
            filterImages('all');
        });
    </script>

  
</body>
</html>
