<?php
// Démarrer la session pour les messages
session_start();

// Inclure la connexion à la base de données
require_once '../src/database/db.php';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../styles/contact.css">
    <title>AYOUBDECOR - Meubles modernes sur mesure | Atelier & pose à domicile</title>
</head>

<body>
    <header class="en-tete" id="en-tete">
        <div class="conteneur">
            <div class="header-contenu">
                <a href="../" class="logo">AYOUBDECOR</a>

                <nav class="menu-principal" id="menu-principal">
                    <ul>
                        <li><a href="../">Accueil</a></li>
                        <li><a href="service.php">Services</a></li>
                        <li><a href="galerie.php">Galerie</a></li>
                        <li><a href="propos.php">À propos</a></li>
                        <li><a href="boutique.php">Boutique</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </nav>

                <div class="header-actions">
                    <a href="devis.php" class="btn btn-primaire">Demander un devis</a>
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
    <section class="section-hero" style="min-height: 40vh; padding-top: 100px;">
        <div class="conteneur">
            <div class="section-header" style="text-align: center;">
                <h1>Contactez-nous</h1>
                <p>Nous sommes là pour répondre à toutes vos questions et vous accompagner dans vos projets</p>
            </div>
        </div>
    </section>

    <!-- Affichage des messages -->
    <?php if (!empty($_SESSION['message'])): ?>
    <section class="section">
        <div class="conteneur">
            <div class="message message-<?php echo $_SESSION['messageType'] ?? 'info'; ?>">
                <p><?php echo htmlspecialchars($_SESSION['message']); ?></p>
            </div>
        </div>
    </section>
    <?php
        // Effacer le message après l'avoir affiché
        unset($_SESSION['message']);
        unset($_SESSION['messageType']);
    ?>
    <?php endif; ?>

    <!-- Section Informations de contact -->
    <section class="section">
        <div class="conteneur">
            <div class="informations-contact">
                <div class="info-contact">
                    <div class="icone-contact">📞</div>
                    <h3>Téléphone</h3>
                    <p><a href="tel:+22870297284">70 29 72 84</a></p>
                    <p>Lun-Ven : 8h-18h<br>Samedi : 9h-17h</p>
                </div>

                <div class="info-contact" style="overflow-x: auto; white-space: nowrap; text-align: center;">
                    <div class="icone-contact">✉️</div>
                    <h3>Email</h3>
                    <p><a href="mailto:emmanuelbossro2004@gmail.com">emmanuelbossro2004@gmail.com</a></p>
                    <p>Réponse sous 24h</p>
                </div>

                <div class="info-contact">
                    <div class="icone-contact">📍</div>
                    <h3>Adresse</h3>
                    <p>123 Rue de l'Artisan<br>00228 Lomé, Togo</p>
                    <p>Sur rendez-vous uniquement</p>
                </div>

                <div class="info-contact">
                    <div class="icone-contact">⏰</div>
                    <h3>Horaires</h3>
                    <p>Lun-Ven : 8h00-18h00<br>Samedi : 9h00-17h00<br>Dimanche : Fermé</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Formulaire de contact -->
    <section class="section" style="background-color: var(--neutral-50);">
        <div class="conteneur">
            <div class="formulaire-contact">
                <div class="section-header">
                    <h2>Envoyez-nous un message</h2>
                    <p>Remplissez le formulaire ci-dessous et nous vous répondrons dans les plus brefs délais</p>
                </div>

                <form class="formulaire" id="formulaire-contact" action="traitementContact.php" method="POST">
                    <input type="hidden" name="action" value="envoyer_message">
                    <div class="grille-formulaire">
                        <div class="groupe-formulaire">
                            <label for="nom">Nom complet</label>
                            <input type="text" id="nom" name="nom" required>
                        </div>

                        <div class="groupe-formulaire">
                            <label for="email">Adresse email</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div class="groupe-formulaire">
                            <label for="telephone">Numéro de téléphone</label>
                            <input type="tel" id="telephone" name="telephone">
                        </div>

                        <div class="groupe-formulaire">
                            <label for="sujet">Sujet</label>
                            <select id="sujet" name="sujet" required>
                                <option value="">Sélectionnez un sujet</option>
                                <option value="devis">Demande de devis</option>
                                <option value="renseignement">Renseignement</option>
                                <option value="rendez-vous">Prise de rendez-vous</option>
                                <option value="suivi">Suivi de commande</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                    </div>

                    <div class="groupe-formulaire">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="6" required placeholder="Décrivez votre projet ou votre demande..."></textarea>
                    </div>

                    <div style="text-align: center; margin-top: var(--espacement-xl);">
                        <button type="submit" class="btn btn-primaire">
                            Envoyer le message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Section Carte et localisation -->
    <section class="section">
        <div class="conteneur">
            <div class="section-header">
                <h2>Notre atelier</h2>
                <p>Venez nous rendre visite dans notre atelier pour discuter de votre projet</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--espacement-2xl); align-items: start;">
                <div>
                    <h3>Informations pratiques</h3>
                    <div style="margin-bottom: var(--espacement-lg);">
                        <h4>📍 Adresse</h4>
                        <p>123 Rue de l'Artisan<br>00228 Lomé, Togo</p>
                    </div>

                    <div style="margin-bottom: var(--espacement-lg);">
                        <h4>🚗 Accès</h4>
                        <p><strong>En voiture :</strong> Parking gratuit disponible<br>
                            <strong>Métro :</strong> Ligne 1, station "Artisan" (5 min à pied)<br>
                            <strong>Bus :</strong> Lignes 23, 45, 67 - Arrêt "Rue de l'Artisan"
                        </p>
                    </div>

                    <div style="margin-bottom: var(--espacement-lg);">
                        <h4>⏰ Visite sur rendez-vous</h4>
                        <p>Pour une visite de l'atelier et un entretien personnalisé, nous vous recommandons de prendre
                            rendez-vous au préalable.</p>
                        <a href="devis.php" class="btn btn-primaire">Prendre rendez-vous</a>
                    </div>
                </div>

                <div>
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9914406081494!2d2.2922925156743164!3d48.85837007928746!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1635789123456!5m2!1sfr!2sfr"
                        class="carte-google" style="width: 100%; height: 400px; border-radius: var(--rayon-md);"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <footer class="pied-page">
        <div class="conteneur">
            <div class="contenu-footer">
                <div class="colonne-footer">
                    <h3>AYOUBDECOR</h3>
                    <p>Fabricant de meubles modernes sur mesure. Conception, fabrication et pose de mobilier
                        contemporain pour particuliers et professionnels.</p>
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
                        <li style="margin-bottom: var(--espacement-xs);"><a href="service.php#fabrication">Fabrication
                                sur mesure</a></li>
                        <li style="margin-bottom: var(--espacement-xs);"><a href="service.php#pose">Pose et
                                installation</a></li>
                        <li style="margin-bottom: var(--espacement-xs);"><a
                                href="service.php#renovation">Rénovation</a>
                        </li>
                        <li style="margin-bottom: var(--espacement-xs);"><a
                                href="service.php#amenagement">Aménagement</a>
                        </li>
                    </ul>
                </div>

                <div class="colonne-footer">
                    <h3>Contact</h3>
                    <div style="margin-bottom: var(--espacement-sm);">
                        <strong>Téléphone :</strong><br>
                        <a href="tel:+22870297284">70 29 72 84</a>
                    </div>
                    <div style="margin-bottom: var(--espacement-sm);">
                        <strong>Email :</strong><br>
                        <a href="mailto:emmanuelbossro2004@gmail.com">emmanuelbossro2004@gmail.com</a>
                    </div>
                    <div>
                        <strong>Adresse :</strong><br>
                        123 Rue de l'Artisan<br>
                        00228 Lomé, Togo
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
                <p>&copy; 2024 AYOUBDECOR. Tous droits réservés. | <a href="#"
                        style="color: var(--neutral-300);">Mentions
                        légales</a> | <a href="#" style="color: var(--neutral-300);">Politique de confidentialité</a>
                </p>
            </div>
        </div>
    </footer>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
</body>

</html>
