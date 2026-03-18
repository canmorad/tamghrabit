<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tamghrabit - Plateforme de Dons</title>
    <link rel="stylesheet" href="<?= url('public/css/dashboard.css') ?>">
    <link rel="stylesheet" href="<?= url('public/css/adherent/campagne/create.css') ?>">
    <link rel="stylesheet" href="<?= url('public/css/all.min.css') ?>">
</head>

<body>

    <div class="main-wrapper">
        

        <main>
            <header>
                <div class="search-bar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search campaign, donor and more...">
                </div>

                <div class="action-buttons">
                    <button class="icon-btn"><i class="fa-solid fa-moon"></i></button>
                    <button class="icon-btn"><i class="fa-solid fa-message"></i></button>
                    <button class="icon-btn"><i class="fa-solid fa-bell"></i></button>

                    <div class="profile-container">
                        <img src="<?= url('public/images/profile-cv.png') ?>" alt="Profile" class="avatar">
                        <div class="user-info">
                            <span class="nom">Morad Benaissa</span>
                            <span class="email">moradbenaissa@gmail.com</span>
                        </div>

                    </div>
                </div>
            </header>

            <section class="dashboard-body">
                <div class="form-card">
                    <div class="form-header">
                        <h2><i class="fa-solid fa-plus-circle"></i> Ajouter une Campagne</h2>
                        <p>Remplissez les informations pour lancer votre collecte de dons.</p>
                    </div>

                    <div class="stepper">
                        <div class="step active">
                            <span class="step-icon"><i class="fa-solid fa-circle-info"></i></span>
                            <span class="step-label">Général</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step active">
                            <span class="step-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                            <span class="step-label">Financier</span>
                        </div>
                        <div class="step-line"></div>
                        <div class="step active">
                            <span class="step-icon"><i class="fa-solid fa-id-card"></i></span>
                            <span class="step-label">Documents</span>
                        </div>
                    </div>

                    <form action="#" method="POST" enctype="multipart/form-data">

                        <div class="form-section">
                            <h3 class="section-title">Informations Générales</h3>
                            <div class="grid-row">
                                <div class="form-group full">
                                    <label>Titre de la campagne</label>
                                    <input type="text" placeholder="Ex: Construction d'une école...">
                                </div>
                            </div>
                            <div class="grid-row">
                                <div class="form-group">
                                    <label>Catégorie</label>
                                    <select>
                                        <option>Éducation</option>
                                        <option>Santé</option>
                                        <option>Social</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Numéro de téléphone</label>
                                    <input type="tel" placeholder="+212 600-000000">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea rows="4" placeholder="Décrivez votre projet en détail..."></textarea>
                            </div>
                            <div class="grid-row">
                                <div class="form-group">
                                    <label>Date de début</label>
                                    <input type="date">
                                </div>
                                <div class="form-group">
                                    <label>Date de fin</label>
                                    <input type="date">
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3 class="section-title">Objectif & Coordonnées Bancaires</h3>
                            <div class="grid-row">
                                <div class="form-group">
                                    <label>Montant Objectif (DH)</label>
                                    <input type="number" placeholder="50 000">
                                </div>
                                <div class="form-group">
                                    <label>RIB (24 chiffres)</label>
                                    <input type="text" placeholder="000 000 000...">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Image de la campagne</label>
                                <div class="file-upload">
                                    <input type="file" id="comp-img">
                                    <label for="comp-img"><i class="fa-solid fa-cloud-arrow-up"></i> Choisir une
                                        image</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h3 class="section-title">Documents d'identité (CNI)</h3>
                            <div class="grid-row">
                                <div class="form-group">
                                    <label>Carte d'identité (Face)</label>
                                    <input type="file" class="input-file">
                                </div>
                                <div class="form-group">
                                    <label>Carte d'identité (Dos)</label>
                                    <input type="file" class="input-file">
                                </div>
                            </div>
                        </div>

                        <div class="form-footer">
                            <button type="button" class="btn-cancel">Annuler</button>
                            <button type="submit" class="btn-submit">Continuer</button>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>

</body>

</html>