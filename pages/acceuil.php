<?php

session_start();

// Configuration de la page
$page_title = "AYOUBDECOR - Meubles modernes sur mesure | Atelier & pose à domicile";
$page_description = "Conception, fabrication et pose de mobilier contemporain pour particuliers et professionnels";
$current_page = "accueil";

$realisations = '<p class="message-info">La galerie est temporairement indisponible.</p>';

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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="../assets/ayoubdecor_logoo.png">

    <!-- Styles supplémentaires pour la galerie d'accueil -->
    <style>
        /* Styles pour la galerie d'accueil */
        .grille-galerie {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: var(--espacement-lg);
            margin-top: var(--espacement-2xl);
        }

        .galerie-item {
            position: relative;
            border-radius: var(--rayon-lg);
            overflow: hidden;
            box-shadow: var(--ombre-legere);
            transition: all var(--transition-normale);
            cursor: pointer;
        }

        .galerie-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--ombre-moyenne);
        }

        .galerie-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
            transition: all var(--transition-normale);
        }

        .galerie-item:hover img {
            transform: scale(1.05);
        }

        .galerie-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            color: var(--neutral-100);
            padding: var(--espacement-lg);
            transform: translateY(100%);
            transition: all var(--transition-normale);
        }

        .galerie-item:hover .galerie-overlay {
            transform: translateY(0);
        }

        .galerie-overlay h4 {
            margin: 0 0 var(--espacement-xs) 0;
            font-size: var(--font-size-lg);
            font-weight: 600;
        }

        .galerie-overlay p {
            margin: 0;
            font-size: var(--font-size-sm);
            opacity: 0.9;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .grille-galerie {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: var(--espacement-md);
            }

            .galerie-item img {
                height: 200px;
            }
        }

        @media (max-width: 480px) {
            .grille-galerie {
                grid-template-columns: 1fr;
            }

            .galerie-item img {
                height: 180px;
            }
        }
    </style>
</head>

<body>
    <!-- Header partagé -->
    <?php include_once __DIR__ . '/../src/header.php'; ?>

    <!-- Section Hero -->
    <section class="section-hero">
        <div class="conteneur">
            <div class="hero-contenu">
                <div class="hero-texte">
                    <h1>Meubles modernes sur mesure</h1>
                    <p>Conception, fabrication et pose de mobilier contemporain pour particuliers et professionnels.
                        Transformez vos espaces avec des créations uniques alliant esthétique moderne et savoir-faire
                        artisanal.</p>
                    <div class="hero-actions">
                        <a href="devis.php" class="btn btn-primaire">Demander un devis</a>
                        <a href="#realisations" class="btn btn-secondaire">Voir nos réalisations</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="../assets/1.jpeg" alt="Meuble moderne sur mesure en bois et métal"
                        loading="eager">
                </div>
            </div>
        </div>
    </section>

    <!-- Section Services -->
    <section class="section" id="services">
        <div class="conteneur">
            <div class="section-header">
                <h2>Nos services</h2>
                <p>Des solutions complètes pour tous vos besoins en mobilier sur mesure</p>
            </div>

            <div class="grille-services">
                <div class="carte-service">
                    <div class="icone-service">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" />
                            <path d="M2 17L12 22L22 17" />
                            <path d="M2 12L12 17L22 12" />
                        </svg>
                    </div>
                    <h3>Fabrication sur mesure</h3>
                    <p>Création de meubles uniques adaptés à vos espaces et à votre style. Chaque pièce est pensée et
                        réalisée selon vos spécifications exactes.</p>
                    <a href="services.php#fabrication" class="btn btn-texte">En savoir plus →</a>
                </div>

                <div class="carte-service">
                    <div class="icone-service">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" />
                            <path d="M2 17L12 22L22 17" />
                            <path d="M2 12L12 17L22 12" />
                        </svg>
                    </div>
                    <h3>Pose et installation</h3>
                    <p>Installation professionnelle de vos meubles avec soin et précision. Nous nous occupons de tout
                        pour un résultat parfait.</p>
                    <a href="services.php#pose" class="btn btn-texte">En savoir plus →</a>
                </div>

                <div class="carte-service">
                    <div class="icone-service">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" />
                            <path d="M2 17L12 22L22 17" />
                            <path d="M2 12L12 17L22 12" />
                        </svg>
                    </div>
                    <h3>Rénovation</h3>
                    <p>Remise à neuf de vos meubles existants. Donnez une seconde vie à vos pièces préférées avec notre
                        expertise.</p>
                    <a href="services.php#renovation" class="btn btn-texte">En savoir plus →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Galerie - Réalisations -->
    <section class="section galerie" id="realisations">
        <div class="conteneur">
            <div class="section-header">
                <h2>Nos réalisations</h2>
                <p>Découvrez quelques-unes de nos créations récentes</p>
            </div>

            <div class="grille-galerie" id="grille-galerie">
                <!-- Réalisations mises en avant -->
                <div class="galerie-item">
                    <img src="../assets/1.jpeg" alt="Cuisine moderne sur mesure" loading="lazy">
                    <div class="galerie-overlay">
                        <h4>Cuisine Moderne</h4>
                        <p>Bois massif et finitions élégantes</p>
                    </div>
                </div>

                <div class="galerie-item">
                    <img src="../assets/3.jpeg" alt="Bibliothèque sur mesure pour salon" loading="lazy">
                    <div class="galerie-overlay">
                        <h4>Bibliothèque Salon</h4>
                        <p>Rangement et design contemporain</p>
                    </div>
                </div>

                <div class="galerie-item">
                    <img src="../assets/5.jpeg" alt="Bureau moderne" loading="lazy">
                    <div class="galerie-overlay">
                        <h4>Bureau Design</h4>
                        <p>Coin travail optimisé et fonctionnel</p>
                    </div>
                </div>

                <div class="galerie-item">
                    <img src="../assets/7.jpeg" alt="Dressing sur mesure" loading="lazy">
                    <div class="galerie-overlay">
                        <h4>Dressing Sur Mesure</h4>
                        <p>Organisation intérieure optimale</p>
                    </div>
                </div>
            </div>

            <div class="section-footer" style="text-align: center; margin-top: var(--espacement-2xl);">
                <a href="galerie.php" class="btn btn-primaire">
                    <i class="fas fa-images"></i> Voir toutes nos réalisations
                </a>
            </div>
        </div>
    </section>

    <!-- Section Témoignages -->
    <section class="section temoignages">
        <div class="conteneur">
            <div class="section-header">
                <h2>Ce que disent nos clients</h2>
                <p>Des témoignages authentiques de nos clients satisfaits</p>
            </div>

            <div class="carousel-temoignages" id="carousel-temoignages" aria-roledescription="carousel">
                <div class="temoignage active" role="group" aria-label="Témoignage 1 sur 3">
                    <div class="temoignage-texte">
                        "Un travail exceptionnel ! Notre cuisine sur mesure dépasse toutes nos attentes. L'attention aux
                        détails et la finition sont remarquables. Je recommande vivement AYOUBDECOR."
                    </div>
                    <div class="temoignage-auteur">Marie Dubois</div>
                    <div class="temoignage-ville">Lyon</div>
                </div>

                <div class="temoignage" role="group" aria-label="Témoignage 2 sur 3">
                    <div class="temoignage-texte">
                        "Service impeccable de A à Z. De la conception à la pose, tout s'est déroulé parfaitement. Notre
                        bureau sur mesure est exactement ce que nous avions imaginé."
                    </div>
                    <div class="temoignage-auteur">Pierre Martin</div>
                    <div class="temoignage-ville">Marseille</div>
                </div>

                <div class="temoignage" role="group" aria-label="Témoignage 3 sur 3">
                    <div class="temoignage-texte">
                        "Artisan passionné et professionnel. La rénovation de notre bibliothèque a donné un résultat
                        magnifique. Un savoir-faire rare de nos jours."
                    </div>
                    <div class="temoignage-auteur">Sophie Leroy</div>
                    <div class="temoignage-ville">Toulouse</div>
                </div>
                <div class="temoignages-controles" aria-label="Navigation des témoignages">
                    <button class="temoin-point actif" aria-label="Témoignage 1"></button>
                    <button class="temoin-point" aria-label="Témoignage 2"></button>
                    <button class="temoin-point" aria-label="Témoignage 3"></button>
                </div>
            </div>
        </div>
    </section>

    

    <!-- Footer -->
    <footer class="pied-page">

    <!-- Section CTA -->
    <section class="section" style="background-color: var(--wood-dark); color: var(--neutral-100);">
        <div class="conteneur">
            <div class="section-header" style="text-align: center;">
                <h2 style="color: var(--neutral-100);">Prêt à créer votre meuble sur mesure ?</h2>
                <p style="color: var(--neutral-200);">Contactez-nous pour discuter de votre projet et obtenir un devis
                    personnalisé</p>
                <div style="margin-top: var(--espacement-xl);">
                    <a href="devis.php" class="btn btn-primaire" style="margin-right: var(--espacement-md);">Demander
                        un devis</a>
                    <a href="contact.php" class="btn btn-secondaire"
                        style="border-color: var(--neutral-100); color: var(--neutral-100);">Nous contacter</a>
                </div>
            </div>
        </div>
    </section>
    
        <div class="conteneur">
            <div class="contenu-footer">
                <div class="colonne-footer">
                    <h3>AYOUBDECOR</h3>
                    <p>Fabricant de meubles modernes sur mesure. Conception, fabrication et pose de mobilier
                        contemporain pour particuliers et professionnels.</p>
                    <div class="reseaux-sociaux">
                        <a href="#" class="reseau-social" aria-label="Facebook">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="#" class="reseau-social" aria-label="TikTok">
                            <svg  width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"></path></svg>
                        </a>
                        <a href="#" class="reseau-social" aria-label="LinkedIn">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="colonne-footer">
                    <h3>Services</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: var(--espacement-xs);"><a href="services.php#fabrication">Fabrication
                                sur mesure</a></li>
                        <li style="margin-bottom: var(--espacement-xs);"><a href="services.php#pose">Pose et
                                installation</a></li>
                        <li style="margin-bottom: var(--espacement-xs);"><a
                                href="services.php#renovation">Rénovation</a></li>
                        <li style="margin-bottom: var(--espacement-xs);"><a
                                href="services.php#amenagement">Aménagement</a></li>
                    </ul>
                </div>

                <div class="colonne-footer">
                    <h3>Contact</h3>
                    <div style="margin-bottom: var(--espacement-sm);">
                        <strong>Téléphone :</strong><br>
                        <a href="tel:+228 90 62 87 17">+228 90 62 87 17</a>
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

            <div class="carte-google-container" style="margin: var(--espacement-2xl) 0;">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9914406081494!2d2.2922925156743164!3d48.85837007928746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1635789123456!5m2!1sfr!2sfr"
                    class="carte-google" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <div class="copyright">
                <p>&copy; 2025 AYOUBDECOR. Tous droits réservés. | <a href="#"
                        style="color: var(--neutral-300);">Mentions légales</a> | <a href="#"
                        style="color: var(--neutral-300);">Politique de confidentialité</a></p>
            </div>
        </div>
    </footer>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <div class="lightbox-contenu">
            <button class="lightbox-fermer" id="lightbox-fermer" aria-label="Fermer la lightbox">&times;</button>
            <button class="lightbox-nav lightbox-prec" id="lightbox-prec" aria-label="Image précédente">&larr;</button>
            <img id="lightbox-image" src="" alt="">
            <button class="lightbox-nav lightbox-suiv" id="lightbox-suiv" aria-label="Image suivante">&rarr;</button>
        </div>
    </div>

    <!-- Scripts PHP (migrés depuis JavaScript) -->
    <?php
    // Les fichiers PHP main.php et gallery.php ont été supprimés
    ?>
    <script>
        (function initCarouselTemoignages(){
    const container = document.getElementById('carousel-temoignages');
    if (!container) return;

    let items = Array.from(container.querySelectorAll('.temoignage'));
    let points = Array.from(container.querySelectorAll('.temoin-point'));
    if (items.length === 0) return;
    let indexActuel = 0;

    function afficher(i){
        items.forEach((el,idx)=>{
            el.classList.toggle('active', idx===i);
        });
        points.forEach((p,idx)=>{
            p.classList.toggle('actif', idx===i);
        });
        indexActuel = i;
    }

    // Points cliquables
    points.forEach((p,idx)=>{
        p.addEventListener('click', ()=> afficher(idx));
    });

    // Navigation clavier (flèches gauche/droite)
    container.addEventListener('keydown', (e)=>{
        if (e.key === 'ArrowRight') {
            e.preventDefault();
            afficher((indexActuel+1)%items.length);
        } else if (e.key === 'ArrowLeft') {
            e.preventDefault();
            afficher((indexActuel-1+items.length)%items.length);
        }
    });

    // Lecture auto douce (pause au focus/hover)
    let timer = setInterval(()=> afficher((indexActuel+1)%items.length), 5000);
    container.addEventListener('mouseenter', ()=> clearInterval(timer));
    container.addEventListener('mouseleave', ()=> timer = setInterval(()=> afficher((indexActuel+1)%items.length), 5000));

    // Démarrer à 0
    afficher(0);
    })();
    </script>
</body>

</html>
