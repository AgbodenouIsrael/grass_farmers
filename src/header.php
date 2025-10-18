<?php
// Header partagé pour le site AYOUBDECOR
// Utiliser des chemins root-relative pour que l'inclusion fonctionne depuis n'importe quel dossier
?>
<header class="en-tete" id="en-tete">
    <div class="conteneur">
        <div class="header-contenu">
            <a href="/grass_farmers/" class="logo">
                <img src="/grass_farmers/assets/ayoubdecor_logoo.png" alt="ayoub_logo">
                AYOUBDECOR
            </a>

            <nav class="menu-principal" id="menu-principal">
                <ul>
                    <li><a href="/grass_farmers/">Accueil</a></li>
                    <li><a href="/grass_farmers/pages/services.php">Services</a></li>
                    <li><a href="/grass_farmers/pages/galerie.php">Galerie</a></li>
                    <li><a href="/grass_farmers/pages/propos.php">À propos</a></li>
                    <li><a href="/grass_farmers/pages/boutique.php">Boutique</a></li>
                    <li><a href="/grass_farmers/pages/contact.php">Contact</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <a href="/grass_farmers/pages/devis.php" class="btn btn-primaire">Demander un devis</a>
                <button class="menu-burger" id="menu-burger" aria-label="Ouvrir le menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </div>
</header>
