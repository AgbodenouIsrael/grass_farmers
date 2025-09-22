<?php
$servername = "localhost";
$username = "root";
$dbname = "farmersdb";
$password = "";

try {
    $bdd = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $bdd->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Créer les tables si elles n'existent pas
    createTables();
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

function createTables() {
    global $bdd;

    // Table des utilisateurs admin (existante)
    $sql = "CREATE TABLE IF NOT EXISTS user (
        id_admin INT AUTO_INCREMENT PRIMARY KEY,
        mot_de_passe VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $bdd->exec($sql);

    // Insérer un utilisateur admin par défaut si la table est vide
    $stmt = $bdd->query("SELECT COUNT(*) as count FROM user");
    $result = $stmt->fetch();
    if ($result['count'] == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $bdd->prepare("INSERT INTO user (mot_de_passe) VALUES (?)");
        $stmt->execute([$hashedPassword]);
    }

    // Table des utilisateurs étendue
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(200) UNIQUE NOT NULL,
        mot_de_passe VARCHAR(255) NOT NULL,
        nom VARCHAR(100),
        prenom VARCHAR(100),
        telephone VARCHAR(20),
        role ENUM('admin', 'client') DEFAULT 'client',
        actif BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_email (email(100)),
        INDEX idx_role (role)
    )";
    $bdd->exec($sql);

    // Insérer un utilisateur admin étendu par défaut si la table est vide
    $stmt = $bdd->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
    $result = $stmt->fetch();
    if ($result['count'] == 0) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $bdd->prepare("INSERT INTO users (email, mot_de_passe, nom, prenom, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['admin@ayoubdecor.fr', $hashedPassword, 'Admin', 'AYOUBDECOR', 'admin']);
    }

    // Table des devis - SUPPRIMÉE
    // $sql = "CREATE TABLE IF NOT EXISTS devis (...";
    // $bdd->exec($sql);

    // Table de la galerie - SUPPRIMÉE
    // $sql = "CREATE TABLE IF NOT EXISTS galerie (...";
    // $bdd->exec($sql);

    // Table des produits (existante)
    $sql = "CREATE TABLE IF NOT EXISTS produits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(255) NOT NULL,
        prix DECIMAL(10,2) NOT NULL,
        categorie VARCHAR(50) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        description TEXT,
        image VARCHAR(500),
        actif BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_categorie (categorie),
        INDEX idx_actif (actif)
    )";
    $bdd->exec($sql);

    // Insérer des produits par défaut si la table est vide
    $stmt = $bdd->query("SELECT COUNT(*) as count FROM produits");
    $result = $stmt->fetch();
    if ($result['count'] == 0) {
        insertDefaultProducts();
    }

    // Table de la newsletter - SUPPRIMÉE
    // $sql = "CREATE TABLE IF NOT EXISTS newsletter (...";
    // $bdd->exec($sql);

    // Table des messages de contact - SUPPRIMÉE
    // $sql = "CREATE TABLE IF NOT EXISTS messages_contact (...";
    // $bdd->exec($sql);
}

function insertDefaultProducts() {
    global $bdd;
    $products = [
        [
            'nom' => 'Table basique',
            'prix' => 45000,
            'categorie' => 'tables',
            'stock' => 5,
            'description' => 'Table en chêne massif, finition naturelle. Dimensions : 120x80x75cm. Parfaite pour 4 personnes.',
            'image' => './assets/Table basique en chêne.png'
        ],
        [
            'nom' => 'Étagère moderne',
            'prix' => 32000,
            'categorie' => 'etageres',
            'stock' => 3,
            'description' => 'Étagère design en métal noir et bois de hêtre. 4 niveaux ajustables. Style industriel moderne.',
            'image' => './assets/Étagère moderne en métal et bois.png'
        ],
        [
            'nom' => 'Bureau compact',
            'prix' => 68000,
            'categorie' => 'bureaux',
            'stock' => 2,
            'description' => 'Bureau avec 3 tiroirs et tablette coulissante. En noyer massif. Idéal pour espaces réduits.',
            'image' => './assets/Bureau compact avec tiroirs.png'
        ],
        [
            'nom' => 'Table à manger extensible',
            'prix' => 89000,
            'categorie' => 'tables',
            'stock' => 1,
            'description' => 'Table extensible en chêne massif. De 4 à 8 personnes. Mécanisme à rallonges intégrées.',
            'image' => './assets/Table à manger extensible.png'
        ],
        [
            'nom' => 'Étagère murale flottante',
            'prix' => 18000,
            'categorie' => 'etageres',
            'stock' => 0,
            'description' => 'Étagère murale en bois de frêne. Fixation invisible. Design épuré et moderne.',
            'image' => './assets/Étagère murale flottante.png'
        ],
        [
            'nom' => 'Commode 3 tiroirs',
            'prix' => 52000,
            'categorie' => 'rangements',
            'stock' => 4,
            'description' => 'Commode en hêtre massif avec 3 tiroirs. Poignées en laiton. Style scandinave.',
            'image' => './assets/Commode 3 tiroirs.png'
        ]
    ];

    $stmt = $bdd->prepare("INSERT INTO produits (nom, prix, categorie, stock, description, image) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($products as $product) {
        $stmt->execute([
            $product['nom'],
            $product['prix'],
            $product['categorie'],
            $product['stock'],
            $product['description'],
            $product['image']
        ]);
    }
}

// Fonction insertDefaultGalerie() supprimée - table galerie supprimée
