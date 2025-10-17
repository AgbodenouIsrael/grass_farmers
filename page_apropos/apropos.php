<!DOCTYPE html>
<html lang="fr">

<head>
    <?php
    // Définir des variables de page si elles ne sont pas fournies
    $page_title = $page_title ?? "AYOUBDECOR - À propos";
    $page_description = $page_description ?? "À propos d'AYOUBDECOR - Artisanat, design et fabrication de meubles sur mesure.";
    ?>
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/grass_farmers/page_acceuil/acceuil.css">
    <!-- <link rel="stylesheet" href="../boutique/js/style.css"> -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/x-icon" href="/grass_farmers/page_acceuil/assets/ayoubdecor_logoo.png">
</head>

<body>
    <!-- Header partagé -->
    <?php include_once __DIR__ . '/../includes/header.php'; ?>
   
   <main>
    <section class="apropos">
        <div class="conteneur">
            <h1>À propos d'AYOUBDECOR</h1>
            <p>une passion pour l'artisanat et l'excellence depuis 2010</p>
        </div>

        <div class="conteneur apropos-grid">
            <div class="apropos-texte">
                <h2>Notre artisan fondateur</h2>
                <p>Passionné par le travail du bois depuis son plus jeune âge, notre artisan fondateur a su allier tradition et modernité pour créer des meubles uniques.</p>

                <h3>Un parcours d'excellence</h3>
                <p>Formé aux techniques traditionnelles de l'ébénisterie, il a enrichi son savoir-faire au fil des années en intégrant les innovations modernes. Chaque projet est l'occasion de créer quelque chose d'unique, alliant esthétique contemporaine et qualité artisanale.</p>
                <p>Sa philosophie : "Chaque meuble raconte une histoire, celle de ceux qui l'utiliseront au quotidien."</p>

                <h3>Compétences techniques</h3>
                <ul>
                    <li>Ébénisterie traditionnelle et moderne</li>
                    <li>Maitrise des essences de bois nobles</li>
                    <li>Design contemporain</li>
                    <li>Techniques de finition avancées</li>
                    <li>Utilisation de matériaux durables</li>
                    <li>Design et conception 3D</li>
                    <li>Techniques de finition et patine</li>
                </ul>
            </div>

            <div class="apropos-image">
                <img src="/grass_farmers/page_acceuil/assets/portrait.png" alt="Portrait de l'artisan AYOUBDECOR">
                
            </div>
        </div>
    </section>

    <section class="histoire">
        <div class="conteneur">
            <h1>Notre histoire</h1>
            <p>Fondée en 2010, AYOUBDECOR est née de la passion pour l'artisanat et le design. Depuis nos débuts, nous nous sommes engagés à offrir des meubles modernes sur mesure qui allient esthétique, fonctionnalité et durabilité.</p>

            <h2>Nos valeurs</h2>
            <ul>
                <li><strong>Qualité :</strong> Nous utilisons uniquement des matériaux de haute qualité pour garantir la longévité de nos meubles.</li>
                <li><strong>Personnalisation :</strong> Chaque projet est unique. Nous travaillons en étroite collaboration avec nos clients pour créer des pièces qui reflètent leur style et leurs besoins.</li>
                <li><strong>Durabilité :</strong> Nous privilégions les pratiques respectueuses de l'environnement et sélectionnons des matériaux durables.</li>
                <li><strong>Innovation :</strong> Nous intégrons les dernières tendances en matière de design et de technologie pour offrir des solutions modernes et fonctionnelles.</li>
            </ul>

            <h2>Notre équipe</h2>
            <p>Notre équipe est composée d'artisans qualifiés, de designers créatifs et de professionnels dévoués qui partagent une passion commune pour l'excellence. Ensemble, nous mettons tout en œuvre pour transformer vos idées en réalité.</p>

            <h2>Engagement envers la communauté</h2>
            <p>Chez AYOUBDECOR, nous croyons en l'importance de redonner à la communauté. Nous soutenons des initiatives locales et collaborons avec des artisans locaux pour promouvoir le savoir-faire artisanal.</p>
        </div>
    </section>

    <section class="valeurs fade-in">
            <h1>Nos valeurs</h1>
        <p>Les principes qui guident notre travail au quotidien :</p>

        <div class="items-row">
            <div class="item">
                <span class="icon-svg"> <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l2.9 6.26L21 9.27l-5 4.87L17.82 22 12 18.27 6.18 22 7 14.14 2 9.27l6.1-.99L12 2z"/></svg></span>
                <div>
                    <h3>Excellence</h3>
                    <p>Nous nous engageons à livrer un travail de qualité supérieure.</p>
                </div>
            </div>

            <div class="item">
                <span class="icon-svg"> <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 14.5h-2V13h2v3.5zm0-5.5h-2V6h2v5z"/></svg></span>
                <div>
                    <h3>Confiance</h3>
                    <p>Une relation de confiance avec nos clients et partenaires.</p>
                </div>
            </div>
        </div>

        <div class="items-row">
            <div class="item">
                <span class="icon-svg"> <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 3C8.1 3 5 6.1 5 10c0 3.9 3.1 7 7 7s7-3.1 7-7c0-3.9-3.1-7-7-7zm0 12c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5z"/></svg></span>
                <div>
                    <h3>Durabilité</h3>
                    <p>Privilégier des solutions durables et respectueuses de l'environnement.</p>
                </div>
            </div>

            <div class="item">
                <span class="icon-svg"> <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M11 7h2v6h-2zM11 15h2v2h-2z"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/></svg></span>
                <div>
                    <h3>Innovation</h3>
                    <p>Allier savoir-faire traditionnel et nouvelles technologies pour créer des solutions modernes et durables.</p>
                </div>
            </div>
        </div>

        <div class="items-row">
            <div class="item">
                <span class="icon-svg"> <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></span>
                <div>
                    <h3>Passion</h3>
                    <p>Une passion authentique pour l'artisanat et la transformation des idées en projets concrets.</p>
                </div>
            </div>

            <div class="item">
                <span class="icon-svg"> <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg></span>
                <div>
                    <h3>Proximité</h3>
                    <p>Être à l’écoute de nos clients et partenaires pour construire ensemble des solutions adaptées à leurs besoins.</p>
                </div>
            </div>
        </div>
    </section>


    <section class="atelier">
        <h1>Notre atelier</h1>
        <p>Un espace dédié a la creation et à l'excellence</p>

        <h2>Un environnement propice à la création</h2>
        <p>Notre atelier de 200m² est équipé des technologies tout en conservant l'âme de l'artisanat traditionnel. Chaque espace est pensé pour optimiser le travail et garantir la qualité de nos réalisations.</p>

        <div class="atelier-image">
            <img src="/grass_farmers/page_acceuil/assets/atelier.png" alt="Atelier AYOUBDECOR 1">
        </div>

        <h3>Nos équipements</h3>
        <ul>
            <li>Machines à commande numérique pour la précision</li>
            <li>Imprimantes 3D pour des prototypes rapides</li>
            <li>Outils de découpe laser pour des finitions impeccables</li>
            <li>Postes de travail ergonomiques pour le confort de nos artisans</li>
        </ul>
        
        <div class="atelier-image">
            <img src="/grass_farmers/page_acceuil/assets/Atelier (1).png" alt="Atelier AYOUBDECOR 1">
        </div>

        <h3>Nos matériaux</h3>
        <p>Nous sélectionnons avec soin des matériaux de qualité pour garantir la durabilité et l'esthétique de nos créations.</p>
        <ul>
            <li><strong>ABois nobles :</strong> chêne, noyer, teck, acacia</li>
            <li><strong>Matériaux modernes :</strong> verre, métal, béton, acier inoxydable</li>
            <li><strong>Finition :</strong> vernis, huile, cires</li>
            <li><strong>Quincaillerie :</strong> charnières, poignées, systèmes de fixation</li>
        </ul>
    </section>

    <section class="equipe fade-in">
        <h1>Notre équipe</h1>
        <p>Des artisans passionnés et dévoués à l'excellence artisanale</p>

            <div>
                <h2>Notre équipe d'artisans</h2>

                <h3>Ébénistes et menuisiers</h3>
                <p>Une équipe de 4 artisans expérimentés, chacun spécialisé dans son domaine : Conception, fabrication et finition de meubles sur mesure.Tous partagent la même passion pour l'excellence et l'attentions aux details.</p>
            </div>

            <div>
                <img src="/grass_farmers/page_acceuil/assets/equipe.png" alt="artisant au travail">
            </div>
    </section>

    <div class="certif-equipe-grid">
    <section class="certif fade-in">
        <h1>Certifications et engagements</h1>
        <p>Nos garanties de qualité et d'engagement envers nos clients.</p>

        <div class="items-row">
            <div class="item">
                <span class="icon-svg"> <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l2.9 6.26L21 9.27l-5 4.87L17.82 22 12 18.27 6.18 22 7 14.14 2 9.27l6.1-.99L12 2z"/></svg></span>
                <div>
                    <h3>Artisan d'Art</h3>
                    <p>Certification officielle délivrée par la chambre des métiers et de l'artisanat.</p>
                </div>
            </div>

            <div class="item">
                <span class="icon-svg"> <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 3C8.1 3 5 6.1 5 10c0 3.9 3.1 7 7 7s7-3.1 7-7c0-3.9-3.1-7-7-7zm0 12c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5z"/></svg></span>
                <div>
                    <h3>Matériaux durables</h3>
                    <p>Engagement à utiliser des matériaux respectueux de l'environnement.</p>
                </div>
            </div>
        </div>

        <div class="items-row">
            <div class="item">
                <span class="icon-svg"> <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 1L3 5v6c0 5 3.6 9.7 9 11 5.4-1.3 9-6 9-11V5l-9-4zM11 10h2v6h-2z"/></svg></span>
                <div>
                    <h3>Assurance décennale</h3>
                    <p>Couverture complète pour tous nos travaux de pose et d'installation.</p>
                </div>
            </div>

            <div class="item">
                <span class="icon-svg"> <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg></span>
                <div>
                    <h3>Garantie qualité</h3>
                    <p>Allier savoir-faire traditionnel et nouvelles technologies pour créer des solutions modernes et durables.</p>
                </div>
            </div>
        </div>
    </section>
    </div>
</main>


 <footer class="pied-page">

    <!-- Section CTA -->
    <section class="section" style="background-color: var(--wood-dark); color: var(--neutral-100);">
        <div class="conteneur">
            <div class="section-header" style="text-align: center;">
                <h2 style="color: var(--neutral-100);">Convaincu par notre approche ?</h2>
                <p style="color: var(--neutral-200);">Decouvrez nos services et laissez-nous créer votre meuble sur mesure</p>
                <div style="margin-top: var(--espacement-xl);">
                    <a href="../page_devis/devis.php" class="btn btn-primaire" style="margin-right: var(--espacement-md);">Demander
                        un devis</a>
                    <a href="contact.php" class="btn btn-secondaire"
                        style="border-color: var(--neutral-100); color: var(--neutral-100);">Nos Services</a>
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
                        <li style="margin-bottom: var(--espacement-xs);"><a href="services.html#fabrication">Fabrication
                                sur mesure</a></li>
                        <li style="margin-bottom: var(--espacement-xs);"><a href="services.html#pose">Pose et
                                installation</a></li>
                        <li style="margin-bottom: var(--espacement-xs);"><a
                                href="services.html#renovation">Rénovation</a></li>
                        <li style="margin-bottom: var(--espacement-xs);"><a
                                href="services.html#amenagement">Aménagement</a></li>
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
    <script>
    // Fade-in on scroll using IntersectionObserver
    (function(){
        var observer = new IntersectionObserver(function(entries){
            entries.forEach(function(entry){
                if(entry.isIntersecting){
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {threshold: 0.12});

        document.querySelectorAll('.fade-in').forEach(function(el){
            observer.observe(el);
        });
    })();
    </script>
    </body>