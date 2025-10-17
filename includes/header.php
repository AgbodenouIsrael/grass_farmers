<?php
// Header partagé pour le site AYOUBDECOR
// Utiliser des chemins root-relative pour que l'inclusion fonctionne depuis n'importe quel dossier
?>
<!-- Header -->
<header class="en-tete" id="en-tete">
    <div class="conteneur">
        <div class="header-contenu">
            <a href="/grass_farmers/page_acceuil/acceuil.php" class="logo">
                <img src="/grass_farmers/page_acceuil/assets/ayoubdecor_logoo.png" alt="ayoub_logo">
                AYOUBDECOR
            </a>

            <nav class="menu-principal" id="menu-principal">
                <ul>
                    <li><a href="/grass_farmers/page_acceuil/acceuil.php">Accueil</a></li>
                    <li><a href="/grass_farmers/page_acceuil/services.php">Services</a></li>
                    <!-- <li><a href="/grass_farmers/page_acceuil/galerie.php">Galerie</a></li> -->
                    <li><a href="/grass_farmers/page_apropos/apropos.php">À propos</a></li>
                    <li><a href="/grass_farmers/page_acceuil/boutique.php">Boutique</a></li>
                    <li><a href="/grass_farmers/page_acceuil/contact.php">Contact</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <a href="/grass_farmers/page_devis/devis.php" class="btn btn-primaire">Demander un devis</a>
                <button class="menu-burger" id="menu-burger" aria-label="Ouvrir le menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>
</header>
