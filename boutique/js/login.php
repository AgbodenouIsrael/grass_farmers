<?php
session_start();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    header("Location: admin.php");
    exit;
}

// Vérifier les erreurs de connexion
$error = isset($_GET['error']) && $_GET['error'] == '1';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - AYOUBDECOR</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    <link rel="icon" type="image/x-icon" href="../assets/icons/favicon.ico">
</head>
<body>
    <!-- Header -->
    <header class="en-tete">
        <div class="conteneur">
            <div class="header-contenu">
                <a href="../boutique.php" class="logo">AYOUBDECOR</a>
                <nav class="menu-principal">
                    <ul>
                        <li><a href="../boutique.php">Retour à la boutique</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Section Connexion -->
    <section class="section">
        <div class="conteneur">
            <div class="login-container">
                <div class="login-card">
                    <div class="login-header">
                        <i class='bx bx-lock-alt'></i>
                        <h1>Accès Administration</h1>
                        <p>Entrez le mot de passe pour accéder au panneau d'administration</p>
                    </div>

                    <?php if ($error): ?>
                    <div class="error-message" style="background: #fee2e2; color: #dc2626; padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                        <i class='bx bx-error-circle'></i>
                        Mot de passe incorrect. Veuillez réessayer.
                    </div>
                    <?php endif; ?>

                    <form id="login-form" class="login-form" method="POST" action="traitementLogin.php">
                        <div class="form-group">
                            <label for="password">Mot de passe</label>
                            <div class="password-input-container">
                                <input type="password" id="password" name="mot_de_passe"
                                       placeholder="Entrez le mot de passe administrateur" required>
                                <button type="button" class="toggle-password" id="toggle-password">
                                    <i class='bx bx-show'></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primaire login-btn" style="padding: var(--espacement-sm) var(--espacement-lg);">
                            <i class='bx bx-log-in'></i>
                            Se connecter
                        </button>
                    </form>

                    <div class="login-info">
                        <p><strong>Note :</strong> Contactez l'administrateur pour obtenir le mot de passe.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 70vh;
    }

    .login-card {
        background: var(--neutral-100);
        padding: var(--espacement-3xl);
        border-radius: var(--rayon-lg);
        box-shadow: var(--ombre-forte);
        max-width: 400px;
        width: 100%;
    }

    .login-header {
        text-align: center;
        margin-bottom: var(--espacement-2xl);
    }

    .login-header i {
        font-size: var(--font-size-4xl);
        color: var(--wood-light);
        margin-bottom: var(--espacement-md);
    }

    .login-header h1 {
        color: var(--wood-dark);
        margin-bottom: var(--espacement-sm);
    }

    .login-header p {
        color: var(--muted);
    }

    .login-form {
        margin-bottom: var(--espacement-xl);
    }

    .form-group {
        margin-bottom: var(--espacement-lg);
    }

    .form-group label {
        display: block;
        margin-bottom: var(--espacement-xs);
        font-weight: 500;
        color: var(--wood-dark);
    }

    .password-input-container {
        position: relative;
    }

    .password-input-container input {
        width: 100%;
        padding: var(--espacement-sm) var(--espacement-lg);
        padding-right: 50px;
        border: 2px solid var(--neutral-200);
        border-radius: var(--rayon-md);
        font-family: var(--font-corps);
        font-size: var(--font-size-base);
        transition: border-color var(--transition-rapide);
    }

    .password-input-container input:focus {
        outline: none;
        border-color: var(--wood-light);
        box-shadow: 0 0 0 3px rgba(190, 138, 74, 0.1);
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--neutral-500);
        cursor: pointer;
        padding: 5px;
        border-radius: var(--rayon-sm);
        transition: color var(--transition-rapide);
    }

    .toggle-password:hover {
        color: var(--wood-light);
    }

    .login-btn {
        width: 100%;
        padding: var(--espacement-md);
        font-size: var(--font-size-lg);
        font-weight: 500;
    }

    .login-info {
        text-align: center;
        padding: var(--espacement-md);
        background: var(--neutral-50);
        border-radius: var(--rayon-md);
        border: 1px solid var(--neutral-200);
    }

    .login-info p {
        margin: 0;
        font-size: var(--font-size-sm);
        color: var(--neutral-600);
    }
    </style>

    <script>
    // Toggle password visibility
    document.getElementById('toggle-password').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.className = 'bx bx-hide';
        } else {
            passwordInput.type = 'password';
            icon.className = 'bx bx-show';
        }
    });

    // Auto-focus on password field
    document.getElementById('password').focus();
    </script>
</body>
</html>
