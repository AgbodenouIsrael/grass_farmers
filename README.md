# 🏗️ AYOUBDECOR - Site E-commerce de Meubles

Bienvenue dans le projet AYOUBDECOR ! Ce site web permet de présenter et vendre des meubles modernes fabriqués sur mesure.

## 📁 Structure du Projet

Le projet est organisé de manière claire et logique pour faciliter le développement et le débogage :

```
grass_farmers/
├── 📄 index.php              # Point d'entrée principal
├── 📄 README.md              # Documentation du projet
├── 📄 .gitignore             # Fichiers à ignorer par Git
│
├── ⚙️ config/                # Configuration du projet
│   └── 📄 config.php         # Paramètres de base de données, chemins, etc.
│
├── 🎨 styles/                # Tous les fichiers CSS
│   ├── 📄 acceuil.css        # Styles de la page d'accueil
│   ├── 📄 boutique.css       # Styles de la boutique
│   ├── 📄 contact.css        # Styles de la page contact
│   ├── 📄 galerie.css        # Styles de la galerie
│   └── 📄 service.css        # Styles de la page services
│
├── 🖼️ assets/                # Images et ressources statiques
│   ├── 📁 uploads/           # Images uploadées par les utilisateurs
│   └── 📄 *.jpg, *.png       # Toutes les images du site
│
├── 🔧 src/                   # Code source (logique métier)
│   ├── 📁 database/          # Connexion et requêtes base de données
│   │   └── 📄 db.php         # Configuration de la base de données
│   ├── 📁 controllers/       # Traitement des formulaires et actions
│   │   ├── 📄 traitementContact.php    # Gestion des messages contact
│   │   ├── 📄 traitementLogin.php      # Connexion admin
│   │   └── 📄 traitementDevis.php      # Gestion des devis
│   ├── 📁 models/            # Classes et modèles de données (vide pour l'instant)
│   ├── 📄 header.php         # En-tête HTML réutilisable
│   └── 📄 footer.php         # Pied de page HTML réutilisable
│
├── 📄 pages/                 # Pages principales du site
│   ├── 📄 acceuil.php        # Page d'accueil
│   ├── 📄 boutique.php       # Boutique en ligne
│   ├── 📄 contact.php        # Page de contact
│   ├── 📄 apropos.php        # À propos de l'entreprise
│   ├── 📄 devis.php          # Demande de devis
│   ├── 📄 galerie.html       # Galerie de réalisations
│   └── 📄 service.html       # Services proposés
│
└── 🔐 admin/                 # Pages d'administration
    ├── 📄 admin.php          # Interface admin unifiée
    ├── 📄 login.php          # Connexion administrateur
    └── 📄 admin_produits.php # Gestion des produits (ancien)
```

## 🚀 Installation et Configuration

### 1. Prérequis
- **Serveur web** : Apache, Nginx ou WAMP/XAMPP
- **PHP** : Version 7.4 ou supérieure
- **MySQL** : Version 5.7 ou supérieure
- **Navigateur web** : Chrome, Firefox, Safari, Edge

### 2. Installation
1. **Téléchargez** le projet dans votre serveur web
2. **Importez** la base de données depuis le fichier `database.sql`
3. **Modifiez** les paramètres dans `config/config.php` :
   ```php
   define('DB_HOST', 'localhost');     // Votre serveur MySQL
   define('DB_NAME', 'grass_farmers'); // Nom de votre base
   define('DB_USER', 'root');          // Votre utilisateur MySQL
   define('DB_PASS', '');              // Votre mot de passe
   ```

### 3. Accès au site
- **Site public** : `http://localhost/grass_farmers/`
- **Administration** : `http://localhost/grass_farmers/admin/admin.php`

## 🎯 Fonctionnalités Principales

### 🏠 Site Public
- **Page d'accueil** : Présentation de l'entreprise
- **Boutique** : Catalogue de produits avec panier
- **Devis** : Formulaire de demande de devis personnalisé
- **Contact** : Formulaire de contact avec WhatsApp
- **Galerie** : Photos des réalisations
- **Services** : Description des prestations

### 🔐 Administration
- **Dashboard** : Vue d'ensemble des statistiques
- **Gestion des messages** : Consultation des contacts
- **Gestion des produits** : CRUD complet (Créer, Lire, Modifier, Supprimer)
- **Gestion des devis** : Suivi des demandes clients

## 🛠️ Technologies Utilisées

- **Backend** : PHP 7.4+
- **Base de données** : MySQL 5.7+
- **Frontend** : HTML5, CSS3, JavaScript
- **UI Framework** : Design system personnalisé avec variables CSS
- **Icônes** : BoxIcons (https://boxicons.com/)

## 📝 Comment Contribuer

### Pour les Développeurs
1. **Respectez** la structure des dossiers
2. **Commentez** votre code en français
3. **Testez** vos modifications avant de pousser
4. **Utilisez** les constantes définies dans `config/config.php`

### Bonnes Pratiques
- ✅ **Sécurité** : Échappez toujours les données utilisateur
- ✅ **Performance** : Optimisez les images et les requêtes
- ✅ **Accessibilité** : Utilisez des balises sémantiques
- ✅ **Responsive** : Testez sur mobile et desktop

## 🔧 Dépannage

### Erreurs Courantes

#### "Fichier introuvable"
- Vérifiez que tous les dossiers ont été créés
- Vérifiez les chemins dans `config/config.php`

#### "Connexion base de données impossible"
- Vérifiez les paramètres dans `config/config.php`
- Assurez-vous que MySQL est démarré
- Vérifiez que la base de données existe

#### "Styles ne s'affichent pas"
- Vérifiez les chemins dans les balises `<link>`
- Vérifiez que les fichiers CSS sont dans `styles/`

### Debug Mode
Pour activer le mode debug, modifiez dans `config/config.php` :
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📞 Support

En cas de problème :
1. **Consultez** ce README
2. **Vérifiez** les logs d'erreur de PHP
3. **Testez** avec des données simples
4. **Contactez** l'équipe de développement

---

**Développé avec ❤️ pour AYOUBDECOR - Meubles modernes sur mesure**
