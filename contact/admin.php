<?php
// adminContact.php
// Fichier de configuration et de connexion à la base de données
require_once '../boutique/js/db.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'administrateur est connecté

// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//     header('Location: login.php');
//     exit;
// }

// Récupérer les messages de contact depuis la base de données
try {
    $stmt = $bdd->query("SELECT * FROM messages_contact ORDER BY created_at  DESC");
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur lors de la récupération des messages : " . $e->getMessage());
}

// Modifier les messages pour marquer comme lus 
try {
    $stmt = $bdd->prepare("UPDATE messages_contact SET statut = 'lu' WHERE statut = 'non lu'");
    $stmt->execute();
} catch (PDOException $e) {
    die("Erreur lors de la mise à jour des messages : " . $e->getMessage());
}

// Gérer la suppression d'un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message_id'])) {
    $message_id = intval($_POST['delete_message_id']);

    try {
        $stmt = $bdd->prepare("DELETE FROM messages_contact WHERE id = ?");
        $stmt->execute([$message_id]);
        header('Location: admin.php');
        exit;
    } catch (PDOException $e) {
        die("Erreur lors de la suppression du message : " . $e->getMessage());
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion des Messages de Contact</title>
    <link rel="stylesheet" href="../boutique/js/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #5C3A21;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            padding: 12px 15px;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .logout-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            float: right;
        }
        .logout-btn:hover {
            background-color: #2980b9;
        }
        .header {
            overflow: hidden;
            margin-bottom: 20px;
        }
        .header h1 {
            float: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gestion des Messages de Contact</h1>
        <form method="POST" action="logout.php" style="display:inline;">
            <button type="submit" class="logout-btn">Déconnexion</button>
        </form>
    </div>

    <?php if (empty($messages)): ?>
        <p>Aucun message de contact trouvé.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Sujet</th>
                    <th>Message</th>
                    <th>Date d'Envoi</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($message['id']); ?></td>
                        <td><?php echo htmlspecialchars($message['nom']); ?></td>
                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                        <td><?php echo htmlspecialchars($message['telephone']); ?></td>
                        <td><?php echo htmlspecialchars($message['sujet']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($message['message'])); ?></td>
                        <td><?php echo htmlspecialchars($message['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($message['statut']); ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?');">
                                <input type="hidden" name="delete_message_id" value="<?php echo $message['id']; ?>">
                                <button type="submit" class="delete-btn">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>