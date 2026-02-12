<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Tamghrabit</title>
    <link rel="stylesheet" href="../../public/css/login.css">
    <link rel="stylesheet" href="../../public/css/footer.css">
    <link rel="stylesheet" href="../../public/css/header.css">
    <link rel="stylesheet" href="../../public/css/all.min.css">
</head>
<body>

    <?php include '../layouts/header.php'; ?>

    <div class="login-page">
        <div class="login-container">
            <div class="login-form-side">
                <div class="login-header">
                    <div class="logo-circle">
                        <img src="../../public/images/logo.png" alt="Logo">
                    </div>
                    <h1>Bon retour !</h1>
                    <p>Connectez-vous pour accéder à votre espace.</p>
                </div>

                <form class="form-container">
                    <div class="form-group">
                        <label>E-mail</label>
                        <input type="email" placeholder="exemple@mail.com" required>
                    </div>

                    <div class="form-group">
                        <div class="label-row">
                            <label>Mot de passe</label>
                            <a href="#" class="forgot-password">Oublié ?</a>
                        </div>
                        <div class="input-wrapper">
                            <input type="password" placeholder="••••••••" id="passInput" required>
                            <i class="fa-regular fa-eye-slash" id="togglePass"></i>
                        </div>
                    </div>

                    <div class="form-checkbox">
                        <input type="checkbox" id="remember">
                        <label for="remember">Se souvenir de moi</label>
                    </div>

                    <button type="submit" class="bouton-submit">Se connecter</button>

                    <p class="register-link">Nouveau ici ? <a href="register.php">Créer un compte</a></p>
                </form>
            </div>

            <div class="login-image-side">
                <div class="image-overlay">
                    <h2>BÂTIR. CHANGER.</h2>
                </div>
            </div>
        </div>
    </div>

    <?php include '../layouts/footer.php'; ?>

    <script>
        const togglePass = document.querySelector('#togglePass');
        const passInput = document.querySelector('#passInput');

        togglePass.addEventListener('click', () => {
            const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passInput.setAttribute('type', type);
            togglePass.classList.toggle('fa-eye');
            togglePass.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>