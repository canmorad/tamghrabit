<header class="header">
    <div class="header-conteneur">
        <div class="header-logo">
            <img src="../../public/images/logo.png" alt="Logo" class="header-logo-image">
        </div>

        <nav class="navigation-menu">

            <span class="menu">
                <i class="fa-solid fa-bars-staggered"></i>
            </span>

            <div class="x">
                <i class="fa-solid fa-x"></i>
            </div>

            <ul class="navigation-liste">
                <li class="navigation-item">
                    <a href="#" class="navigation-lien ">Accueil</a>
                </li>
                <li class="navigation-item">
                    <a href="#" class="navigation-lien">Voir les collectes</a>
                </li>
                <li class="navigation-item">
                    <a href="#" class="navigation-lien">Qui sommes-nous ?</a>
                </li>
                <li class="navigation-item">
                    <a href="#" class="navigation-lien">Comment ça marche ?</a>
                </li>

                <li class="navigation-item">
                    <a href="#" class="navigation-lien">Se connecter</a>
                </li>

                <li class="navigation-item header-actions">
                    <a href="#" class="navigation-lien bouton bouton-noir">Démarrer une cagnotte</a>
                </li>
            </ul>
        </nav>

        <!-- <div class="header-actions">
            <button class="bouton bouton-noir">Démarrer une cagnotte</button>
        </div> -->
    </div>
</header>

<script>
    const nav = document.querySelector('.navigation-menu');
    const menu = document.querySelector('.menu');
    const close = document.querySelector('.x');

    menu.addEventListener('click', () => {
        nav.classList.add('active');
    });

    close.addEventListener('click', () => {
        nav.classList.remove('active');
    });
</script>