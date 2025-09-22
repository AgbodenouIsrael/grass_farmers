<?php
/**
 * AYOUBDECOR - Page de demande de devis
 * Formulaire complet pour la soumission de demandes de devis personnalisées
 */

// Démarrer la session pour les messages et données temporaires
session_start();

// Inclure les fonctions de base de données depuis page_accueil
// require_once '../page_acceuil/php/db_functions.php'; // db_functions.php supprimé

// Configuration de la page
$page_title = "AYOUBDECOR - Demande de devis personnalisé";
$page_description = "Demandez un devis gratuit pour vos meubles sur mesure";
$current_page = "devis";

// Configuration pour l'upload d'images
$maxImages = 5;
$maxTailleImage = 5 * 1024 * 1024; // 5MB
$typesImagesAutorises = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
$dossierUpload = 'uploads/';

// Variables pour les messages et erreurs
$message = '';
$messageType = 'info';
$erreurs = [];

// ========================================
// GESTION DES MESSAGES DE SESSION
// ========================================

// Récupérer les messages depuis la session (envoyés par traitementDevis.php)
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['messageType'] ?? 'info';

// Nettoyer les messages de session après les avoir récupérés
unset($_SESSION['message']);
unset($_SESSION['messageType']);

// ========================================
// FONCTIONS SUPPRIMÉES - DÉPLACÉES VERS traitementDevis.php
// ========================================
// Toutes les fonctions de traitement, validation et sauvegarde ont été déplacées
// vers le fichier traitementDevis.php pour une meilleure séparation des responsabilités

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="<?php echo htmlspecialchars($page_description); ?>">

    <!-- Polices Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- Icônes et styles -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../page_acceuil/acceuil.css">
    <link rel="stylesheet" href="../boutique/js/style.css">
    <link rel="icon" type="image/x-icon" href="assets/ayoubdecor_logoo.png">

    <!-- Styles spécifiques à la page devis -->
    <style>
        /* ========================================
       STYLES POUR LE FORMULAIRE DE DEVIS EN 2 COLONNES
       ======================================== */

        /* Conteneur du formulaire avec largeur augmentée */
        .formulaire {
            max-width: 1400px; /* Largeur augmentée pour le formulaire en 2 colonnes */
            margin: 0 auto;
        }

        /* Mise en page 2 colonnes */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .form-column {
            display: flex;
            flex-direction: column;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.2em;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        /* Champs spéciaux qui prennent toute la largeur */
        .full-width {
            grid-column: 1 / -1;
        }

        /* Zone d'upload d'images */
        .zone-upload {
            border: 2px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
            margin-bottom: var(--espacement-lg);
        }

        .zone-upload:hover,
        .zone-upload.dragover {
            border-color: var(--wood-light);
            background-color: rgba(190, 138, 74, 0.05);
        }

        .zone-upload-contenu {
            pointer-events: none;
        }

        .zone-upload-contenu svg {
            color: #666;
            margin-bottom: 10px;
        }

        .info-upload {
            color: #666;
            font-size: 0.9em;
            margin-top: 8px;
        }

        /* Prévisualisation des images */
        .previsualisation-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .conteneur-image-preview {
            position: relative;
            display: inline-block;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }

        .conteneur-image-preview img {
            max-width: 100px;
            max-height: 100px;
            display: block;
        }

        .conteneur-image-preview button {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .conteneur-image-preview button:hover {
            background: #cc0000;
        }

        /* Messages et notifications */
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

        /* États d'erreur des champs */
        .groupe-formulaire.error input,
        .groupe-formulaire.error select,
        .groupe-formulaire.error textarea {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .message-erreur {
            color: #dc2626;
            font-size: 0.9em;
            margin-top: 4px;
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .zone-upload {
                padding: 15px;
            }

            .previsualisation-images {
                justify-content: center;
            }

            .notification {
                left: 10px;
                right: 10px;
                max-width: none;
            }
        }

        @media (max-width: 480px) {
            .form-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .zone-upload {
                padding: 12px;
            }

            .zone-upload-contenu p {
                font-size: 0.9em;
            }

            .info-upload {
                font-size: 0.8em;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="en-tete" id="en-tete">
        <div class="conteneur">
            <div class="header-contenu">
                <a href="index.html" class="logo" style="display: flex;
    align-items: center;"> <img src="./assets/ayoubdecor_logoo.png" alt="ayoub_logo">AYOUBDECOR</a>

                <nav class="menu-principal" id="menu-principal">
                    <ul>
                        <li><a href="/page_acceuil/acceuil.php">Accueil</a></li>
                        <li><a href="services.php">Services</a></li>
                        <!-- <li><a href="galerie.php">Galerie</a></li> -->
                        <li><a href="a-propos.php">À propos</a></li>
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
    <section class="section-hero" style="min-height: 60vh; padding-top: 100px;">
        <div class="conteneur">
            <div class="section-header" style="text-align: center;">
                <h1>Demande de devis personnalisé</h1>
                <p>Décrivez-nous votre projet et obtenez un devis détaillé pour vos meubles sur mesure</p>
            </div>
        </div>
    </section>

    <!-- Section Formulaire de devis -->
    <section class="section" style="padding-top: 40px;">
        <div class="conteneur">
            <div class="formulaire" id="formulaire-devis">
                <form id="form-devis" method="POST" action="traitementDevis.php" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="action" value="soumettre_devis">

                    <div class="form-grid">
                        <!-- Colonne 1 -->
                        <div class="form-column">
                            <!-- Informations personnelles -->
                            <div class="form-section">
                                <h2>Informations personnelles</h2>

                                <div class="form-group">
                                    <label for="nom">Nom complet *</label>
                                    <input type="text" id="nom" name="nom" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Adresse email *</label>
                                    <input type="email" id="email" name="email" required>
                                </div>

                                <div class="form-group">
                                    <label for="telephone">Numéro de téléphone *</label>
                                    <input type="tel" id="telephone" name="telephone" required>
                                </div>

                                <div class="form-group">
                                    <label for="ville">Ville</label>
                                    <input type="text" id="ville" name="ville">
                                </div>
                            </div>

                            <!-- Type de projet -->
                            <div class="form-section">
                                <h2>Type de projet</h2>

                                <div class="form-group">
                                    <label for="type_prestation">Type de prestation *</label>
                                    <select id="type_prestation" name="type_prestation" required>
                                        <option value="">Sélectionnez une prestation</option>
                                        <option value="fabrication">Fabrication sur mesure</option>
                                        <option value="pose">Pose et installation</option>
                                        <option value="renovation">Rénovation</option>
                                        <option value="amenagement">Aménagement complet</option>
                                        <option value="conseil">Conseil et étude</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="budget">Budget estimé (XOF)</label>
                                    <select id="budget" name="budget">
                                        <option value="">Sélectionnez une fourchette</option>
                                        <option value="500-1000">500 - 1 000 XOF</option>
                                        <option value="1000-2500">1 000 - 2 500 XOF</option>
                                        <option value="2500-5000">2 500 - 5 000 XOF</option>
                                        <option value="5000-10000">5 000 - 10 000 XOF</option>
                                        <option value="10000+">Plus de 10 000 XOF</option>
                                        <option value="conseil">Conseil souhaité</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne 2 -->
                        <div class="form-column">
                            <!-- Détails techniques -->
                            <div class="form-section">
                                <h2>Détails techniques</h2>

                                <div class="form-group">
                                    <label for="type_meuble">Type de meuble</label>
                                    <select id="type_meuble" name="type_meuble">
                                        <option value="">Sélectionnez un type</option>
                                        <option value="cuisine">Cuisine</option>
                                        <option value="salon">Salon / Séjour</option>
                                        <option value="chambre">Chambre</option>
                                        <option value="bureau">Bureau / Bureau à domicile</option>
                                        <option value="salle-de-bain">Salle de bain</option>
                                        <option value="dressing">Dressing</option>
                                        <option value="bibliotheque">Bibliothèque</option>
                                        <option value="meuble-tv">Meuble TV</option>
                                        <option value="etagere">Étagère / Rangement</option>
                                        <option value="autre">Autre</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="materiau">Matériau souhaité</label>
                                    <select id="materiau" name="materiau">
                                        <option value="">Sélectionnez un matériau</option>
                                        <option value="chene">Chêne</option>
                                        <option value="noyer">Noyer</option>
                                        <option value="hetre">Hêtre</option>
                                        <option value="sapin">Sapin</option>
                                        <option value="acier">Acier inoxydable</option>
                                        <option value="aluminium">Aluminium</option>
                                        <option value="verre">Verre</option>
                                        <option value="composite">Composite</option>
                                        <option value="melange">Mélange de matériaux</option>
                                        <option value="conseil">Conseil souhaité</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="urgence">Niveau d'urgence</label>
                                    <select id="urgence" name="urgence">
                                        <option value="normal">Normal (2-4 semaines)</option>
                                        <option value="rapide">Rapide (1-2 semaines)</option>
                                        <option value="urgent">Urgent (moins d'1 semaine)</option>
                                        <option value="flexible">Flexible (selon vos disponibilités)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Images de référence -->
                            <div class="form-section">
                                <h2>Images de référence</h2>
                                <p>Ajoutez des photos pour nous aider à mieux comprendre votre projet (plans, photos de l'espace, références...)</p>

                                <div class="form-group">
                                    <div class="zone-upload">
                                        <div class="zone-upload-contenu">
                                            <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                                            </svg>
                                            <p>Glissez-déposez vos images ici ou cliquez pour sélectionner</p>
                                            <p class="info-upload">JPG, PNG, WebP - Max 5MB par image - 5 images maximum</p>
                                        </div>
                                        <input type="file" id="images" name="images[]" multiple accept="image/jpeg,image/jpg,image/png,image/webp" style="display: none;">
                                        <div class="previsualisation-images"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section pleine largeur pour la description -->
                        <div class="form-section full-width">
                            <h2>Description de votre projet</h2>

                            <div class="form-group">
                                <label for="description">Décrivez votre projet en détail *</label>
                                <textarea id="description" name="description" rows="6" required
                                          placeholder="Décrivez votre projet : style souhaité, fonctionnalités particulières, contraintes techniques, inspiration..."></textarea>
                            </div>
                        </div>
                    </div>



                    <!-- Bouton de soumission -->
                    <div class="section-formulaire" style="text-align: center; margin-top: var(--espacement-2xl);">
                        <button type="submit" class="btn btn-primaire"
                            style="font-size: var(--font-size-lg); padding: var(--espacement-md) var(--espacement-2xl);">
                            Envoyer ma demande de devis
                        </button>
                        <p
                            style="margin-top: var(--espacement-md); font-size: var(--font-size-sm); color: var(--muted);">
                            * Champs obligatoires
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Section Informations complémentaires -->
    <section class="section" style="background-color: var(--neutral-50);">
        <div class="conteneur">
            <div class="section-header">
                <h2>Comment ça marche ?</h2>
                <p>Un processus simple et transparent pour votre projet</p>
            </div>

            <div class="grille-services">
                <div class="carte-service">
                    <div class="icone-service">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" />
                        </svg>
                    </div>
                    <h3>1. Demande de devis</h3>
                    <p>Remplissez notre formulaire détaillé avec vos besoins et préférences. Plus vous êtes précis, plus
                        notre devis sera adapté.</p>
                </div>

                <div class="carte-service">
                    <div class="icone-service">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" />
                        </svg>
                    </div>
                    <h3>2. Étude personnalisée</h3>
                    <p>Nous étudions votre projet et vous contactons sous 24-48h pour discuter des détails et planifier
                        un rendez-vous si nécessaire.</p>
                </div>

                <div class="carte-service">
                    <div class="icone-service">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" />
                        </svg>
                    </div>
                    <h3>3. Devis détaillé</h3>
                    <p>Vous recevez un devis complet avec plans, matériaux, délais et prix. Aucun engagement de votre
                        part.</p>
                </div>

                <div class="carte-service">
                    <div class="icone-service">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L2 7L12 12L22 7L12 2Z" />
                        </svg>
                    </div>
                    <h3>4. Réalisation</h3>
                    <p>Une fois le devis accepté, nous nous occupons de tout : fabrication, livraison et pose si
                        souhaitée.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Contact rapide -->
    <section class="section">
        <div class="conteneur">
            <div class="section-header" style="text-align: center;">
                <h2>Besoin d'aide ?</h2>
                <p>Notre équipe est là pour vous accompagner dans votre projet</p>
                <div style="margin-top: var(--espacement-xl);">
                    <a href="tel:+33123456789" class="btn btn-primaire" style="margin-right: var(--espacement-md);">
                        📞 +228 90 62 87 17
                    </a>
                    <a href="mailto:contact@ayoubdecor.fr" class="btn btn-secondaire">
                        ✉️ contact@ayoubdecor.fr
                    </a>
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
                    <p>Fabricant de meubles modernes sur mesure. Conception, fabrication et pose de mobilier
                        contemporain pour particuliers et professionnels.</p>
                    <div class="reseaux-sociaux">
                        <a href="#" class="reseau-social" aria-label="Facebook">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="#" class="reseau-social" aria-label="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path
                                    d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.297-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.807.875 1.297 2.026 1.297 3.323s-.49 2.448-1.297 3.323c-.875.807-2.026 1.297-3.323 1.297zm7.83-9.281H6.721c-.49 0-.875.385-.875.875v7.83c0 .49.385.875.875.875h9.558c.49 0 .875-.385.875-.875v-7.83c0-.49-.385-.875-.875-.875z" />
                            </svg>
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
                <p>&copy; 2025 AYOUBDECOR. Tous droits réservés. | <a href="#"
                        style="color: var(--neutral-300);">Mentions légales</a> | <a href="#"
                        style="color: var(--neutral-300);">Politique de confidentialité</a></p>
            </div>
        </div>
    </footer>

    <!-- Scripts PHP (migrés depuis JavaScript) -->
    <?php
// Inclusion des fichiers PHP nécessaires
// Les fichiers main.php et db_functions.php ont été supprimés
?>

    <!-- JavaScript pour l'upload d'images et les notifications -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des notifications
            function afficherNotification(message, type = 'info') {
                // Supprimer les notifications existantes
                const notificationsExistantes = document.querySelectorAll('.notification');
                notificationsExistantes.forEach(notif => notif.remove());

                // Créer la nouvelle notification
                const notification = document.createElement('div');
                notification.className = `notification notification-${type}`;
                notification.innerHTML = `
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; font-size: 20px; cursor: pointer; margin-left: 10px;">×</button>
            `;

                // Ajouter au DOM
                document.body.appendChild(notification);

                // Afficher avec animation
                setTimeout(() => notification.classList.add('show'), 100);

                // Masquer automatiquement après 5 secondes
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => notification.remove(), 300);
                }, 5000);
            }

            // Vérifier s'il y a un message à afficher depuis PHP
            <?php if (!empty($message)): ?>
            afficherNotification('<?php echo addslashes($message); ?>',
                '<?php echo $messageType; ?>');
            <?php endif; ?>

            const zoneUpload = document.querySelector('.zone-upload');
            const zoneUploadContenu = document.querySelector('.zone-upload-contenu');
            const inputImages = document.getElementById('images');
            const previsualisation = document.querySelector('.previsualisation-images');

            // Vérifier que tous les éléments existent
            if (!zoneUpload || !inputImages || !previsualisation) {
                return;
            }

            // Stocker les fichiers sélectionnés
            let fichiersSelectionnes = [];

            // Fonction pour déclencher la sélection de fichiers
            function declencherSelectionFichiers() {
                inputImages.click();
            }

            // Événement clic sur la zone d'upload
            zoneUpload.addEventListener('click', function(e) {
                // Vérifier si le clic vient de l'input file lui-même (éviter double déclenchement)
                if (e.target.id === 'images') {
                    return;
                }

                // Vérifier si le clic vient d'un élément interactif
                if (e.target.tagName === 'BUTTON' || e.target.closest('button')) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();
                declencherSelectionFichiers();
            });

            // Le bouton de secours a été supprimé - l'upload fonctionne uniquement via drag & drop et clic sur la zone

            // Gestion du drag and drop
            zoneUpload.addEventListener('dragover', function(e) {
                e.preventDefault();
                zoneUpload.classList.add('dragover');
            });

            zoneUpload.addEventListener('dragleave', function(e) {
                e.preventDefault();
                zoneUpload.classList.remove('dragover');
            });

            zoneUpload.addEventListener('drop', function(e) {
                e.preventDefault();
                zoneUpload.classList.remove('dragover');

                const fichiers = e.dataTransfer.files;
                gererFichiers(fichiers);
            });

            // Gestion de la sélection via le bouton
            inputImages.addEventListener('change', function(e) {
                const fichiers = e.target.files;
                gererFichiers(fichiers);
            });

            // Fonction pour gérer les fichiers sélectionnés
            function gererFichiers(nouveauxFichiers) {
                // Convertir FileList en Array et filtrer les doublons
                const fichiersArray = Array.from(nouveauxFichiers).filter(nouveauFichier => {
                    // Vérifier si ce fichier n'est pas déjà dans la liste
                    return !fichiersSelectionnes.some(fichierExistant =>
                        fichierExistant.name === nouveauFichier.name &&
                        fichierExistant.size === nouveauFichier.size &&
                        fichierExistant.lastModified === nouveauFichier.lastModified
                    );
                });

                // Ajouter les nouveaux fichiers à la liste existante
                fichiersSelectionnes = fichiersSelectionnes.concat(fichiersArray);

                // Vérifier le nombre maximum de fichiers
                if (fichiersSelectionnes.length > 5) {
                    alert(
                        'Vous ne pouvez pas uploader plus de 5 images. Seules les 5 premières seront conservées.'
                        );
                    fichiersSelectionnes = fichiersSelectionnes.slice(0, 5);
                }

                // Mettre à jour la prévisualisation
                mettreAJourPrevisualisation();

                // Créer un DataTransfer pour mettre à jour l'input file
                const dt = new DataTransfer();
                fichiersSelectionnes.forEach(fichier => dt.items.add(fichier));
                inputImages.files = dt.files;
            }

            // Fonction de validation des fichiers
            function validerFichier(fichier) {
                const typesAutorises = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                const tailleMax = 5 * 1024 * 1024; // 5MB

                if (!typesAutorises.includes(fichier.type)) {
                    alert(`Le fichier ${fichier.name} n'est pas un format d'image supporté.`);
                    return false;
                }

                if (fichier.size > tailleMax) {
                    alert(`Le fichier ${fichier.name} est trop volumineux (max 5MB).`);
                    return false;
                }

                return true;
            }

            // Fonction pour mettre à jour toute la prévisualisation
            function mettreAJourPrevisualisation() {
                // Vider la prévisualisation
                previsualisation.innerHTML = '';

                // Afficher tous les fichiers sélectionnés
                fichiersSelectionnes.forEach((fichier, index) => {
                    afficherPrevisualisation(fichier, index);
                });
            }

            // Fonction d'affichage de prévisualisation
            function afficherPrevisualisation(fichier, index) {
                const lecteur = new FileReader();

                lecteur.onload = function(e) {
                    const conteneurImage = document.createElement('div');
                    conteneurImage.className = 'conteneur-image-preview';

                    conteneurImage.innerHTML = `
                    <img src="${e.target.result}" alt="Prévisualisation ${index + 1}" style="max-width: 100px; max-height: 100px; margin: 5px; border: 1px solid #ddd;">
                    <button type="button" onclick="supprimerImage(${index})" style="background: #ff4444; color: white; border: none; padding: 2px 5px; cursor: pointer;">×</button>
                `;

                    previsualisation.appendChild(conteneurImage);
                };

                lecteur.readAsDataURL(fichier);
            }

            // Fonction de suppression d'image
            window.supprimerImage = function(index) {
                // Supprimer le fichier de la liste
                fichiersSelectionnes.splice(index, 1);

                // Mettre à jour la prévisualisation
                mettreAJourPrevisualisation();

                // Mettre à jour l'input file
                const dt = new DataTransfer();
                fichiersSelectionnes.forEach(fichier => dt.items.add(fichier));
                inputImages.files = dt.files;
            };
        });
    </script>



</body>

</html>
