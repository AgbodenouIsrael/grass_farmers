<?php

session_start();

// Configuration de la page
$page_title = "Nos Services - AYOUBDECOR | Fabrication, Pose et Rénovation de Meubles";
$page_description = "Découvrez nos services complets : fabrication sur mesure, pose et installation professionnelle, rénovation de meubles. Expertise artisanale pour vos projets.";
$current_page = "services";

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../styles/acceuil.css">
    <link rel="stylesheet" href="../styles/services.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="../assets/ayoubdecor_logoo.png">
</head>

<body>
    <!-- Header partagé -->
    <?php include_once __DIR__ . '/../src/header.php'; ?>

    <!-- Section Hero Services - Compact -->
    <section class="services-hero">
        <div class="conteneur">
            <div class="services-hero-content">
                <h1>Nos Services</h1>
                <p>Expertise complète en ébénisterie : de la fabrication sur mesure à l'aménagement d'espaces</p>
            </div>
        </div>
    </section>

    <!-- Section Services en Grille -->
    <section class="services-grid-section">
        <div class="conteneur">
            <div class="services-grid">
                <!-- Service 1: Fabrication -->
                <div class="service-card" data-service="fabrication">
                    <div class="service-card-header">
                        <div class="service-icon">
                            <i class='bx bx-wrench'></i>
                        </div>
                        <h3>Fabrication sur mesure</h3>
                    </div>
                    <div class="service-card-content">
                        <p>Création de meubles uniques selon vos spécifications. Conception personnalisée avec matériaux de qualité supérieure.</p>
                        <ul class="service-features">
                            <li>Plans sur mesure</li>
                            <li>Matériaux premium</li>
                            <li>Finition artisanale</li>
                            <li>Garantie 2 ans</li>
                        </ul>
                    </div>
                    <div class="service-card-footer">
                        <a href="devis.php" class="btn-service">Demander un devis</a>
                    </div>
                </div>

                <!-- Service 2: Pose & Installation -->
                <div class="service-card" data-service="pose">
                    <div class="service-card-header">
                        <div class="service-icon">
                            <i class='bx bx-home-alt'></i>
                        </div>
                        <h3>Pose & Installation</h3>
                    </div>
                    <div class="service-card-content">
                        <p>Installation professionnelle par nos artisans expérimentés. Livraison, montage et mise en service complète.</p>
                        <ul class="service-features">
                            <li>Livraison incluse</li>
                            <li>Montage professionnel</li>
                            <li>Mise en place</li>
                            <li>Nettoyage final</li>
                        </ul>
                    </div>
                    <div class="service-card-footer">
                        <a href="devis.php" class="btn-service">Demander un devis</a>
                    </div>
                </div>

                <!-- Service 3: Rénovation -->
                <div class="service-card" data-service="renovation">
                    <div class="service-card-header">
                        <div class="service-icon">
                            <i class='bx bx-refresh'></i>
                        </div>
                        <h3>Rénovation</h3>
                    </div>
                    <div class="service-card-content">
                        <p>Redonnez vie à vos meubles anciens. Restauration, modernisation et réparation avec techniques traditionnelles.</p>
                        <ul class="service-features">
                            <li>Restauration classique</li>
                            <li>Modernisation</li>
                            <li>Réparation fonctionnelle</li>
                            <li>Devis gratuit</li>
                        </ul>
                    </div>
                    <div class="service-card-footer">
                        <a href="devis.php" class="btn-service">Demander un devis</a>
                    </div>
                </div>

                <!-- Service 4: Aménagement -->
                <div class="service-card" data-service="amenagement">
                    <div class="service-card-header">
                        <div class="service-icon">
                            <i class='bx bx-layout'></i>
                        </div>
                        <h3>Aménagement d'espace</h3>
                    </div>
                    <div class="service-card-content">
                        <p>Optimisation de vos espaces de vie. Solutions sur mesure pour cuisine, salle de bain, bureau et dressing.</p>
                        <ul class="service-features">
                            <li>Étude d'espace</li>
                            <li>Plans 3D</li>
                            <li>Solutions ergonomiques</li>
                            <li>Accompagnement complet</li>
                        </ul>
                    </div>
                    <div class="service-card-footer">
                        <a href="devis.php" class="btn-service">Demander un devis</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Processus -->
    <section class="process-section">
        <div class="conteneur">
            <div class="section-header">
                <h2>Notre processus de travail</h2>
                <p>Une méthodologie éprouvée pour des résultats exceptionnels</p>
            </div>

            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h4>Consultation initiale</h4>
                        <p>Échange sur vos besoins, visite des lieux, prise de mesures et définition du cahier des charges.</p>
                    </div>
                </div>

                <div class="process-step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h4>Conception & Devis</h4>
                        <p>Création des plans détaillés, sélection des matériaux et établissement d'un devis précis.</p>
                    </div>
                </div>

                <div class="process-step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h4>Fabrication</h4>
                        <p>Réalisation en atelier avec contrôle qualité rigoureux et respect des délais convenus.</p>
                    </div>
                </div>

                <div class="process-step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h4>Pose & Installation</h4>
                        <p>Installation professionnelle avec finitions parfaites et nettoyage complet du chantier.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Témoignages -->
    <section class="testimonials-section">
        <div class="conteneur">
            <div class="section-header">
                <h2>Ils nous font confiance</h2>
                <p>La satisfaction de nos clients est notre meilleure récompense</p>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Service impeccable de A à Z. De la conception à l'installation, tout a été parfait. Les meubles sont d'une qualité exceptionnelle."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <strong>Marie Dubois</strong>
                            <span>Cuisine sur mesure</span>
                        </div>
                        <div class="rating">
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Équipe professionnelle et à l'écoute. Ils ont su transformer notre bureau en un espace moderne et fonctionnel."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <strong>Pierre Martin</strong>
                            <span>Aménagement bureau</span>
                        </div>
                        <div class="rating">
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"Rénovation parfaite de nos meubles anciens. Ils ont gardé le charme d'origine tout en modernisant le confort."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <strong>Sophie Laurent</strong>
                            <span>Rénovation meubles</span>
                        </div>
                        <div class="rating">
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                            <i class='bx bxs-star'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section CTA -->
    <section class="section" style="background-color: var(--wood-dark); color: var(--neutral-100);">
        <div class="conteneur">
            <div class="section-header" style="text-align: center;">
                <h2 style="color: var(--neutral-100);">Prêt à réaliser votre projet ?</h2>
                <p style="color: var(--neutral-200);">Contactez-nous pour discuter de vos idées et obtenir un devis personnalisé gratuit</p>
                <div style="margin-top: var(--espacement-xl);">
                    <a href="devis.php" class="btn btn-primaire" style="margin-right: var(--espacement-md);">Demander
                        un devis</a>
                    <a href="contact.php" class="btn btn-secondaire"
                        style="border-color: var(--neutral-100); color: var(--neutral-100);">Nous contacter</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include_once __DIR__ . '/../src/footer.php'; ?>

    <script>
        // Animation au scroll pour les cartes de service
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observer les cartes de service
            document.querySelectorAll('.service-card').forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = `all 0.6s cubic-bezier(0.4, 0, 0.2, 1) ${index * 0.1}s`;
                observer.observe(card);
            });

            // Observer les étapes du processus
            document.querySelectorAll('.process-step').forEach((step, index) => {
                step.style.opacity = '0';
                step.style.transform = 'translateY(30px)';
                step.style.transition = `all 0.6s cubic-bezier(0.4, 0, 0.2, 1) ${index * 0.15}s`;
                observer.observe(step);
            });

            // Observer les témoignages
            document.querySelectorAll('.testimonial-card').forEach((testimonial, index) => {
                testimonial.style.opacity = '0';
                testimonial.style.transform = 'translateY(30px)';
                testimonial.style.transition = `all 0.6s cubic-bezier(0.4, 0, 0.2, 1) ${index * 0.1}s`;
                observer.observe(testimonial);
            });
        });
    </script>
</body>

</html>
