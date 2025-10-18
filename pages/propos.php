<?php

session_start();

// Configuration de la page
$page_title = "À propos - AYOUBDECOR | Artisan ébéniste sur mesure";
$page_description = "Découvrez l'histoire d'AYOUBDECOR, notre passion pour l'artisanat et notre équipe d'artisans qualifiés. Meubles sur mesure depuis 2015.";
$current_page = "propos";

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="<?php echo htmlspecialchars($page_description); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../styles/propos.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="../assets/ayoubdecor_logoo.png">
</head>

<body>
    <!-- Header partagé -->
    <?php include_once __DIR__ . '/../src/header.php'; ?>

    <!-- Section Hero - Storytelling -->
    <section class="story-hero">
        <div class="conteneur">
            <div class="story-hero-content">
                <div class="story-quote">
                    <blockquote>
                        "Chaque meuble raconte une histoire. La nôtre commence avec le bois, cet élément vivant qui nous
                        inspire depuis toujours."
                    </blockquote>
                    <cite>— AYOUBDECOR, 2015</cite>
                </div>
                <div class="story-stats">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Projets réalisés</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">98%</div>
                        <div class="stat-label">Clients satisfaits</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Années d'expérience</div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- Section Vision & Valeurs -->
    <section class="vision-section">
        <div class="conteneur">
            <div class="vision-content">
                <div class="vision-text">
                    <h2>Notre Vision</h2>
                    <p>Redéfinir l'artisanat moderne en combinant tradition et innovation. Chaque pièce que nous créons
                        est le fruit d'une réflexion profonde sur l'harmonie entre forme, fonction et beauté.</p>
                    <p>Nous croyons que le mobilier ne se limite pas à meubler un espace, mais à créer des lieux de vie
                        qui racontent une histoire et suscitent des émotions.</p>
                </div>
                <div class="vision-values">
                    <div class="value-card">
                        <div class="value-icon">
                            <i class='bx bx-heart'></i>
                        </div>
                        <h3>Passion</h3>
                        <p>Chaque projet est porté par notre amour du bois et du travail bien fait.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">
                            <i class='bx bx-leaf'></i>
                        </div>
                        <h3>Durabilité</h3>
                        <p>Engagement pour des pratiques respectueuses de l'environnement.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">
                            <i class='bx bx-target-lock'></i>
                        </div>
                        <h3>Excellence</h3>
                        <p>Poursuite perpétuelle de la perfection dans chaque détail.</p>
                    </div>
                    <div class="value-card">
                        <div class="value-icon">
                            <i class='bx bx-group'></i>
                        </div>
                        <h3>Proximité</h3>
                        <p>Relation de confiance et transparence avec nos clients.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Équipe -->
    <section class="team-section">
        <div class="conteneur">
            <div class="section-header">
                <h2>Rencontrez Notre Équipe</h2>
                <p>Des artisans passionnés unis par la même vision</p>
            </div>

            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image" onclick="openImageModal('../assets/portrait.png')" style="cursor: pointer;">
                        <img src="../assets/portrait.png" alt="Ayoub - Fondateur & Ébéniste" loading="lazy">
                        <div class="image-overlay">
                            <i class='bx bx-zoom-in'></i>
                        </div>
                    </div>
                    <div class="member-info">
                        <h3>Ayoub</h3>
                        <p class="member-role">Fondateur & Ébéniste Principal</p>
                        <p class="member-bio">Passionné par le bois depuis l'enfance, Ayoub a perfectionné son art
                            pendant plus de 15 ans. Chaque meuble porte sa signature unique.</p>
                    </div>
                </div>

                <div class="team-member">
                    <div class="member-image">
                        <img src="../assets/equipe.png" alt="Notre équipe d'artisans" loading="lazy">
                    </div>
                    <div class="member-info">
                        <h3>L'Équipe AYOUBDECOR</h3>
                        <p class="member-role">Artisans Qualifiés</p>
                        <p class="member-bio">Une équipe complémentaire d'ébénistes, de designers et de poseurs, chacun
                            expert dans son domaine pour garantir l'excellence à chaque étape.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Atelier -->
    <section class="workshop-section">
        <div class="conteneur">
            <div class="workshop-content">
                <div class="workshop-text">
                    <h2>Notre Atelier</h2>
                    <p>Un lieu où le bois prend vie. Notre espace de 200m² est équipé d'outils traditionnels et de
                        technologies modernes, nous permettant de réaliser des projets d'une complexité infinie.</p>
                    <p>Chaque pièce créée ici est le résultat d'un processus rigoureux : de la sélection du bois brut à
                        la finition parfaite, en passant par le façonnage précis et le contrôle qualité.</p>
                </div>
                <div class="workshop-features">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class='bx bx-cog'></i>
                        </div>
                        <h4>Machines de précision</h4>
                        <p>Équipements modernes pour une précision millimétrique</p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class='bx bx-tree'></i>
                        </div>
                        <h4>Bois certifiés</h4>
                        <p>Matériaux issus de forêts gérées durablement</p>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class='bx bx-check-shield'></i>
                        </div>
                        <h4>Contrôle qualité</h4>
                        <p>Vérification rigoureuse à chaque étape</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section CTA Personnalisée -->
    <section class="story-cta-section">
        <div class="conteneur">
            <div class="story-cta-content">
                <h2>Écrivez Votre Histoire Avec Nous</h2>
                <p>Chaque projet est unique, comme votre vision. Laissez-nous transformer vos idées en réalité et créer
                    ensemble des meubles qui vous ressemblent.</p>
                <div class="story-cta-actions">
                    <a href="devis.php" class="btn-story-primary">
                        <i class='bx bx-edit-alt'></i>
                        Commencer Votre Projet
                    </a>
                    <a href="galerie.php" class="btn-story-secondary">
                        <i class='bx bx-images'></i>
                        Voir Nos Réalisations
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal pour l'image agrandie -->
    <div id="imageModal" class="image-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <img id="modalImage" src="" alt="Image agrandie">
        </div>
    </div>

    <!-- Footer -->
    <?php include_once __DIR__ . '/../src/footer.php'; ?>

    <!-- Scripts -->
    <script>
    // Fonction globale pour ouvrir la modal d'image
    function openImageModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');

        if (modal && modalImage) {
            modalImage.src = imageSrc;
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }

    // Fonction globale pour fermer la modal
    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // Animation au scroll simple - sans animation au rechargement
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('visible')) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Observer tous les éléments animables - ils sont visibles par défaut
        document.querySelectorAll('.value-card, .team-member, .feature-item').forEach((element, index) => {
            // Délai échelonné pour l'animation au scroll
            element.style.transitionDelay = `${index * 0.1}s`;
            observer.observe(element);
        });

        // Gestionnaire de la modal d'image
        const modal = document.getElementById('imageModal');
        const closeModal = document.querySelector('.close-modal');

        // Fermer la modal en cliquant sur le bouton
        if (closeModal) {
            closeModal.addEventListener('click', closeImageModal);
        }

        // Fermer la modal en cliquant en dehors de l'image
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeImageModal();
                }
            });
        }

        // Fermer avec la touche Échap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && modal.style.display === 'block') {
                closeImageModal();
            }
        });
    });
    </script>
</body>

</html>
