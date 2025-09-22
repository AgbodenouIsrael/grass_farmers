<?php
session_start();

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $motdepasse = trim($_POST["mot_de_passe"]);

    try {
        // Récupérer l'utilisateur
        $sql = "SELECT * FROM user LIMIT 1";
        $stmt = $bdd->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetch();

        if ($users && ($motdepasse === $users['mot_de_passe'])) {
            $_SESSION['user_id'] = $users['id_admin'];
            $_SESSION['authenticated'] = true;
            $_SESSION['login_time'] = time();

            header("Location: admin.php");
            exit;
        }

        // Échec de connexion
        header("Location: login.php?error=1");
        exit;

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
