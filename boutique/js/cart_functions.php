<?php
/**
 * Fonctions PHP pour gérer le panier d'achat
 */

/**
 * Ajoute un produit au panier
 */
function ajouterAuPanier($produit_id, $quantite = 1) {
    global $bdd;

    try {
        // Vérifier que le produit existe et est en stock
        $stmt = $bdd->prepare("SELECT nom, prix, stock FROM produits WHERE id = ?");
        $stmt->execute([$produit_id]);
        $produit = $stmt->fetch();

        if (!$produit) {
            return ['success' => false, 'message' => 'Produit introuvable'];
        }

        if ($produit['stock'] <= 0) {
            return ['success' => false, 'message' => 'Produit en rupture de stock'];
        }

        $session_id = session_id();

        // Vérifier si le produit est déjà dans le panier
        $stmt = $bdd->prepare("SELECT id, quantite FROM panier WHERE session_id = ? AND produit_id = ?");
        $stmt->execute([$session_id, $produit_id]);
        $panier_item = $stmt->fetch();

        if ($panier_item) {
            // Mettre à jour la quantité
            $nouvelle_quantite = min($panier_item['quantite'] + $quantite, $produit['stock']);
            $stmt = $bdd->prepare("UPDATE panier SET quantite = ? WHERE id = ?");
            $stmt->execute([$nouvelle_quantite, $panier_item['id']]);
        } else {
            // Ajouter au panier
            $stmt = $bdd->prepare("INSERT INTO panier (session_id, produit_id, quantite) VALUES (?, ?, ?)");
            $stmt->execute([$session_id, $produit_id, $quantite]);
        }

        return [
            'success' => true,
            'message' => $produit['nom'] . ' ajouté au panier',
            'produit' => $produit['nom']
        ];

    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout au panier'];
    }
}

/**
 * Modifie la quantité d'un produit dans le panier
 */
function modifierQuantitePanier($produit_id, $nouvelle_quantite) {
    global $bdd;

    try {
        $session_id = session_id();

        if ($nouvelle_quantite <= 0) {
            // Supprimer du panier
            supprimerDuPanier($produit_id);
            return ['success' => true, 'message' => 'Produit retiré du panier'];
        }

        // Vérifier le stock disponible
        $stmt = $bdd->prepare("
            SELECT p.stock, pa.quantite
            FROM produits p
            LEFT JOIN panier pa ON pa.produit_id = p.id AND pa.session_id = ?
            WHERE p.id = ?
        ");
        $stmt->execute([$session_id, $produit_id]);
        $result = $stmt->fetch();

        if (!$result) {
            return ['success' => false, 'message' => 'Produit introuvable'];
        }

        $nouvelle_quantite = min($nouvelle_quantite, $result['stock']);

        $stmt = $bdd->prepare("UPDATE panier SET quantite = ? WHERE session_id = ? AND produit_id = ?");
        $stmt->execute([$nouvelle_quantite, $session_id, $produit_id]);

        return ['success' => true, 'message' => 'Quantité mise à jour'];

    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la modification'];
    }
}

/**
 * Supprime un produit du panier
 */
function supprimerDuPanier($produit_id) {
    global $bdd;

    try {
        $session_id = session_id();
        $stmt = $bdd->prepare("DELETE FROM panier WHERE session_id = ? AND produit_id = ?");
        $stmt->execute([$session_id, $produit_id]);

        return ['success' => true, 'message' => 'Produit retiré du panier'];

    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors de la suppression'];
    }
}

/**
 * Récupère le contenu du panier
 */
function getPanier() {
    global $bdd;

    try {
        $session_id = session_id();
        $stmt = $bdd->prepare("
            SELECT
                pa.id,
                pa.produit_id,
                pa.quantite,
                p.nom,
                p.prix,
                p.image,
                p.stock,
                (pa.quantite * p.prix) as sous_total
            FROM panier pa
            JOIN produits p ON pa.produit_id = p.id
            WHERE pa.session_id = ?
            ORDER BY pa.date_ajout DESC
        ");
        $stmt->execute([$session_id]);

        return $stmt->fetchAll();

    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Calcule le total du panier
 */
function calculerTotalPanier() {
    $panier = getPanier();
    return array_reduce($panier, function($total, $item) {
        return $total + $item['sous_total'];
    }, 0);
}

/**
 * Compte le nombre total d'articles dans le panier
 */
function compterArticlesPanier() {
    $panier = getPanier();
    return array_reduce($panier, function($total, $item) {
        return $total + $item['quantite'];
    }, 0);
}

/**
 * Vide complètement le panier
 */
function viderPanier() {
    global $bdd;

    try {
        $session_id = session_id();
        $stmt = $bdd->prepare("DELETE FROM panier WHERE session_id = ?");
        $stmt->execute([$session_id]);

        return ['success' => true, 'message' => 'Panier vidé'];

    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Erreur lors du vidage du panier'];
    }
}

/**
 * Formate un prix en FCFA
 */
function formatPrix($prix) {
    return number_format($prix, 0, ',', ' ') . ' FCFA';
}
?>
