<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tamghrabit - Plateforme de Dons</title>
    <link rel="stylesheet" href="../../public/css/register.css">
    <link rel="stylesheet" href="../../public/css/footer.css">
    <link rel="stylesheet" href="../../public/css/header.css">
    <link rel="stylesheet" href="../../public/css/all.min.css">
</head>

<body>

    <?php include '../layouts/header.php'; ?>

    <div class="register-page">
        <div class="register-container">
            <div class="register-form-side">
                <div class="register-header">
                    <div class="logo-circle">
                        <img src="../../public/images/logo.png" alt="Tamghrabit Logo">
                    </div>
                    <h1>Créer un compte</h1>
                    <p>Inscrivez-vous pour commencer votre aventure</p>
                </div>

                <form class="form-container">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom</label>
                            <input type="text" placeholder="Votre nom" required>
                        </div>
                        <div class="form-group">
                            <label>Prénom</label>
                            <input type="text" placeholder="Votre prénom" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" placeholder="exemple@mail.com" required>
                    </div>

                    <div class="form-group">
                        <label>Date de naissance</label>
                        <input type="date" required>
                    </div>

                    <div class="form-group">
                        <label>Mot de passe</label>
                        <div class="input-wrapper">
                            <input type="password" placeholder="••••••••" required>
                            <i class="fa-regular fa-eye-slash"></i>
                        </div>
                        <span class="hint">Au moins 8 caractères</span>
                    </div>

                    <div class="form-group">
                        <label>Confirmer le mot de passe</label>
                        <input type="password" placeholder="••••••••" required>
                    </div>

                    <div class="form-checkbox">
                        <input type="checkbox" id="terms" required>
                        <label for="terms">J'accepte la <strong>Politique de confidentialité</strong> et les
                            <strong>Conditions d'utilisation</strong>.</label>
                    </div>

                    <button type="submit" class="bouton-submit">Continuer</button>

                    <p class="login-link">Vous avez déjà un compte ? <a href="#">Se connecter</a></p>
                </form>
            </div>

            <div class="register-image-side">
                <div class="image-overlay">
                    <h2>BÂTIR. CHANGER.</h2>
                </div>
            </div>
        </div>
    </div>

    <?php include '../layouts/footer.php'; ?>

</body>

</html>