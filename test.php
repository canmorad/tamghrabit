afficher les donner en  view : <!DOCTYPE html>
<html lang="fr">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Tamghrabit - Mon Profil</title>
		<link rel="stylesheet" href="{{url('public/css/dashboard.css')}}">
		<link rel="stylesheet" href="{{url('public/css/profile.css')}}">
		<link rel="stylesheet" href="{{url('public/css/all.min.css')}}">
	</head>

	<body>

		<div class="main-wrapper">
			{% include 'layouts/sidebar.twig' %}

			<main>
				<header>
					<div class="search-bar">
						<i class="fa-solid fa-magnifying-glass"></i>
						<input type="text" placeholder="Rechercher...">
					</div>

					<div class="action-buttons">
						<button class="icon-btn">
							<i class="fa-solid fa-moon"></i>
						</button>
						<button class="icon-btn">
							<i class="fa-solid fa-bell"></i>
						</button>
						<div class="profile-container">
							<img src="{{ url('public/storage/profiles/') ~ user.imageProfile }}" alt="Profile" class="avatar">
							<div class="user-info">
								<span class="nom">{{ user.nom ~ ' ' ~ user.prenom }}</span>
								<span class="email">{{user.email}}</span>
							</div>
						</div>
					</div>
				</header>

				<section class="dashboard-body">
					<div id="profile-main-container" class="form-card">
						<nav class="profile-tabs">
							<button class="tab-btn active" onclick="switchTab(event, 'info')">
								<i class="fa-solid fa-user-pen"></i>
								Mes Informations
							</button>
							<button class="tab-btn" onclick="switchTab(event, 'docs')">
								<i class="fa-solid fa-file-shield"></i>
								Mes Documents
							</button>
							<button class="tab-btn" onclick="switchTab(event, 'orgs')">
								<i class="fa-solid fa-building-ngo"></i>
								Mes Organisations
							</button>
						</nav>

						<div class="tab-separator"></div>

						<section id="info" class="tab-content active">

							<div class="avatar-section">
								<div class="avatar-wrapper">
									<img src="{{ url('public/storage/profiles/') ~ user.imageProfile }}" id="imagePreview" class="avatar-img">
									<label for="avatarInput" class="upload-badge">
										<i class="fa-solid fa-camera"></i>
									</label>

								</div>
								<input type="file" name="imageProfile" id="avatarInput" hidden>
							</div>

							<form action="" method="POST" id="profileForm" enctype="multipart/form-data">

								<div class="gender-selection">
									<label>Vous êtes :</label>
									<div class="radio-group">
										<label class="radio-item">
											<input type="radio" name="sexe" value="homme" {{ user.sexe=='homme' ? 'checked' : '' }}>
											<span>Homme</span>
										</label>
										<label class="radio-item">
											<input type="radio" name="sexe" value="femme" {{ user.sexe=='femme' ? 'checked' : '' }}>
											<span>Femme</span>
										</label>
									</div>
								</div>

								<div class="grid-row">
									<div class="form-group">
										<label>Prénom</label><input type="text" name="prenom" value="{{user.prenom}}"></div>
									<div class="form-group">
										<label>Nom</label><input type="text" name="nom" value="{{user.nom}}"></div>
								</div>

								<div class="grid-row">
									<div class="form-group">
										<label>Email</label><input type="email" name="email" value="{{user.email}}">
									</div>
									<div class="form-group">
										<label>Date de naissance</label><input type="date" name="dateNaissance" value="{{user.dateNaissance}}"></div>
								</div>

								<div class="grid-row">
									<div class="form-group">
										<label>Pays</label>
										<div class="custom-dropdown" id="countryDropdown">
											<div class="dropdown-selected" onclick="toggleDropdown()">
												<img src="" id="current-flag" class="flag-icon">
												<span id="current-country-name"></span>
												<i class="fa-solid fa-chevron-down"></i>
											</div>

											<div class="dropdown-list" id="countryList"></div>

											<input type="hidden" name="pays" id="countryValue" value="{{ user.pays ?: 'ma' }}">
										</div>
									</div>
									<div class="form-group">
										<label>Téléphone</label>
										<div class="dual-input">
											<div class="custom-dropdown phone-code-dropdown" id="phoneDropdown">
												<div class="dropdown-selected" onclick="togglePhoneDropdown()">
													<img src="https://flagcdn.com/w40/{{ user.pays ?: 'ma' }}.png" id="current-phone-flag" class="flag-icon">
													<span id="current-phone-code">{{ user.telephoneCode ?: '+212' }}</span>
													<i class="fa-solid fa-chevron-down" style="font-size: 8px;"></i>
												</div>
												<div class="dropdown-list" id="phoneList"></div>
												<input type="hidden" name="telephoneCode" id="phoneCodeValue" value="{{ user.telephoneCode ?: '+212' }}">
											</div>

											<input type="tel" name="telephone" value="{{user.telephone}}" placeholder="600-000000">
										</div>
									</div>
								</div>

								<div class="grid-row">
									<div class="form-group">
										<label>Ville</label><input type="text" name="ville" value="{{user.ville}}" placeholder="Votre ville"></div>
									<div class="form-group">
										<label>Adresse</label><input type="text" name="adresse" value="{{user.adresse}}" placeholder="Votre adresse complète"></div>
								</div>

								<div class="form-footer">
									<button type="submit" class="btn-submit">Sauvegarder</button>
								</div>
							</form>
						</section>

						<section id="docs" class="tab-content">
							<form action="" method="POST" enctype="multipart/form-data" id="identifierForm">
								<h3 class="section-title">Pièces d'identité</h3>

								<select class="identity-document-select" name="idType" id="idType" onchange="toggleIdUploads()" style="margin-bottom: 20px;">
									<option value="cni">Carte Nationale</option>
									<option value="passport">Passeport</option>
								</select>

								<div id="cni-wrapper" class="grid-row docs-grid">
									<div class="file-upload-box">
										<input type="file" name="cniRecto" id="cni-recto-input" hidden>
										<label for="cni-recto-input">
											<i class="fa-solid fa-upload"></i><br>
											<span>Recto (Bout face)</span>
										</label>
									</div>
									<div class="file-upload-box">
										<input type="file" name="cniVerso" id="cni-verso-input" hidden>
										<label for="cni-verso-input">
											<i class="fa-solid fa-upload"></i><br>
											<span>Verso (Dos)</span>
										</label>
									</div>
								</div>

								<div id="passport-wrapper" class="grid-row docs-grid" style="display: none;">
									<div class="file-upload-box full-box">
										<input type="file" name="passport" id="passport-input" hidden>
										<label for="passport-input">
											<i class="fa-solid fa-upload"></i><br>
											<span>Page principale du Passeport</span>
										</label>
									</div>
								</div>

								<div class="form-footer" style="margin-bottom: 40px;">
									<button type="submit" class="btn-submit">Sauvegarder les documents</button>
								</div>
							</form>

							<div class="tab-separator"></div>

							<form action="" method="POST" id="bankForm" enctype="multipart/form-data">
								<h3 class="section-title">Informations Bancaires</h3>
								<div class="form-group full" style="margin-bottom: 15px;">
									<label>RIB (24 chiffres)</label>
									<input type="text" name="rib" placeholder="000 000 000000000000000 00">
								</div>
								<div class="file-upload-box full-box">
									<input type="file" name="attestationRib" id="rib-doc" hidden>
									<label for="rib-doc">
										<i class="fa-solid fa-file-invoice-dollar"></i><br>
										<span>Attestation de RIB</span>
									</label>
								</div>

								<div class="form-footer">
									<button type="submit" class="btn-submit">Sauvegarder les infos bancaires</button>
								</div>
							</form>
						</section>

						<section id="orgs" class="tab-content">
							<div class="org-header-flex" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
								<h3 class="section-title">Mes Organisations</h3>
								<button class="btn-add-small" onclick="showAddOrg()">
									<i class="fa-solid fa-plus"></i>
									Ajouter une ONG</button>
							</div>
							<div class="org-card-item">
								<div class="org-info">
									<span class="org-name">Association Al Khair</span>
									<br>
									<span class="org-status">Statut :
										<strong style="color: #C5F82A;">Agréée</strong>
									</span>
								</div>
								<div class="org-btns">
									<button class="icon-action-btn">
										<i class="fa-solid fa-eye"></i>
									</button>
									<button class="icon-action-btn delete">
										<i class="fa-solid fa-trash"></i>
									</button>
								</div>
							</div>
						</section>

						<section id="add-org-view" class="tab-content">
							<div class="org-header-flex" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
								<h3 class="section-title">Nouvelle Organisation</h3>
								<button class="btn-submit" onclick="switchTab(event, 'orgs')">Enregistrer</button>
							</div>
							<div class="grid-row">
								<div class="form-group">
									<label>Nom de l'ONG</label><input type="text" placeholder="Nom officiel"></div>
								<div class="form-group">
									<label>Adresse du siège</label><input type="text" placeholder="Ville, Quartier..."></div>
							</div>
							<div class="grid-row">
								<div class="form-group full">
									<label>RIB de l'organisation (24 chiffres)</label><input type="text" placeholder="RIB Bancaire"></div>
							</div>
							<div class="docs-upload-grid">
								<div class="doc-box">
									<label>Logo</label>
									<div class="upload-square"><input type="file" id="o1" hidden><label for="o1">
											<i class="fa-solid fa-upload"></i>
										</label>
									</div>
								</div>
								<div class="doc-box">
									<label>Statuts</label>
									<div class="upload-square"><input type="file" id="o2" hidden><label for="o2">
											<i class="fa-solid fa-upload"></i>
										</label>
									</div>
								</div>
								<div class="doc-box">
									<label>RIB Doc</label>
									<div class="upload-square"><input type="file" id="o3" hidden><label for="o3">
											<i class="fa-solid fa-upload"></i>
										</label>
									</div>
								</div>
								<div class="doc-box">
									<label>CIN P.(R)</label>
									<div class="upload-square"><input type="file" id="o4" hidden><label for="o4">
											<i class="fa-solid fa-upload"></i>
										</label>
									</div>
								</div>
								<div class="doc-box">
									<label>CIN P.(V)</label>
									<div class="upload-square"><input type="file" id="o5" hidden><label for="o5">
											<i class="fa-solid fa-upload"></i>
										</label>
									</div>
								</div>
							</div>
						</section>
					</div>
				</section>
			</main>
		</div>
		<script src="{{url('public/js/profile.js')}}"></script>

	</body>
</html>

function switchTab(event, tabId) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    if (event && event.currentTarget.classList.contains('tab-btn')) {
        event.currentTarget.classList.add('active');
    }
}

function showAddOrg() {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
    document.getElementById('add-org-view').classList.add('active');
}

function toggleIdUploads() {
    const type = document.getElementById('idType').value;
    const cniWrapper = document.getElementById('cni-wrapper');
    const passportWrapper = document.getElementById('passport-wrapper');

    if (type === 'passport') {
        cniWrapper.style.display = 'none';
        passportWrapper.style.display = 'grid';
    } else {
        cniWrapper.style.display = 'grid';
        passportWrapper.style.display = 'none';
    }
}

function updateIdentifier() {
    const identifierForm = document.getElementById("identifierForm");

    if (identifierForm) {
        identifierForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(identifierForm);

            fetch("/Tamghrabit/identifier/update", {
                method: "POST",
                body: formData
            })
                .then(res => res.text())
                .then(data => {
                    showAlert(data.message, data.type);
                })
                .catch(err => {
                    showAlert("Erreur serveur", "error");
                    console.error(err);
                });
        });
    }
}

async function updateBankInfos() {
    const bankForm = document.getElementById("bankForm");

    if (bankForm) {
        bankForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const formData = new FormData(bankForm);

            const submitBtn = bankForm.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

            try {
                const response = await fetch("/Tamghrabit/bank/update", {
                    method: "POST",
                    body: formData
                });

                if (!response.ok) throw new Error("Erreur réseau");

                const data = await response.json();
                console.log(data);

                showAlert(data.message, data.type);

            } catch (err) {
                console.error("Erreur:", err);
                showAlert("Erreur serveur ou connexion impossible", "error");
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    }
}

async function renduImageProfile() {
    const input = document.querySelector('#avatarInput');
    const img = document.querySelector('#imagePreview');

    if (!input) return;

    input.addEventListener("change", async function () {
        if (this.files && this.files[0]) {
            const formData = new FormData();
            formData.append("imageProfile", this.files[0]);

            try {
                const response = await fetch("/Tamghrabit/profile/image/update", {
                    method: "POST",
                    body: formData
                });

                if (!response.ok) throw new Error("Erreur réseau");

                const data = await response.json();

                if (data.type === "success") {
                    img.src = URL.createObjectURL(this.files[0]);

                    const headerAvatar = document.querySelector('.profile-container .avatar');
                    if (headerAvatar) {
                        headerAvatar.src = img.src;
                    }
                }

                showAlert(data.message, data.type);

            } catch (err) {
                showAlert("Erreur serveur", "error");
                console.error(err);
            }
        }
    });
}

async function renduCountries() {
    try {
        const res = await fetch("../public/data/pays.json");
        const countries = await res.json();
        const listContainer = document.querySelector("#countryList");
        const currentCode = document.querySelector("#countryValue").value;

        listContainer.innerHTML = "";

        countries.forEach(c => {
            const item = document.createElement("div");
            item.className = "dropdown-item";
            item.innerHTML = `
                <img src="${c.flag}" alt="${c.country}">
                    <span>${c.country}</span>
            `;
            item.onclick = () => {
                selectCountry(c.country, c.flag, c.code);
                toggleDropdown();
            };

            listContainer.appendChild(item);
            const activeCountry = countries.find(c => c.code === currentCode);
            if (activeCountry) {
                selectCountry(activeCountry.country, activeCountry.flag, activeCountry.code);
            } else {

                selectCountry("Maroc", "https://flagcdn.com/w40/ma.png", "ma");
            }
        });
    } catch (error) {
        console.error("Erreur chargement pays:", error);
    }
}

function selectCountry(name, flag, code) {
    document.querySelector("#current-flag").src = flag;
    document.querySelector("#current-country-name").innerText = name;
    document.querySelector("#countryValue").value = code;
}

function toggleDropdown() {
    document.querySelector("#countryList").classList.toggle("show");
}

async function renduPhones() {
    try {
        const res = await fetch("../public/data/telephones.json");
        const phones = await res.json();
        const listContainer = document.querySelector("#phoneList");

        listContainer.innerHTML = "";

        phones.forEach(p => {
            const item = document.createElement("div");
            item.className = "dropdown-item";
            item.innerHTML = `
                <img src="https://flagcdn.com/w40/${p.iso.toLowerCase()}.png" alt="${p.iso}">
                    <span style="flex:1">${p.nicename || p.iso}</span>
                    <span style="color: #062121; font-weight: bold;">+${p.code}</span>
            `;

            item.onclick = () => {
                selectPhoneCode(p.iso.toLowerCase(), p.code);
                togglePhoneDropdown();
            };

            listContainer.appendChild(item);
        });

    } catch (error) {
        console.error("Erreur chargement code téléphone:", error);
    }
}

function selectPhoneCode(iso, code) {
    document.querySelector("#current-phone-flag").src = `https://flagcdn.com/w40/${iso}.png`;
    document.querySelector("#current-phone-code").innerText = `+${code}`;
    document.querySelector("#phoneCodeValue").value = `+${code}`;
}

function togglePhoneDropdown() {
    document.querySelector("#phoneList").classList.toggle("show");
}

window.addEventListener("click", (e) => {
    if (!document.getElementById("countryDropdown").contains(e.target)) {
        document.querySelector("#countryList").classList.remove("show");
    }
    if (!document.getElementById("phoneDropdown").contains(e.target)) {
        document.querySelector("#phoneList").classList.remove("show");
    }
});

function renduFileUploads() {
    const allInputs = document.querySelectorAll('input[type="file"]');

    allInputs.forEach(input => {
        if (input.name != "imageProfile") {
            input.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                const box = this.closest('.file-upload-box, .upload-square');
                const label = box.querySelector('span') || box.querySelector('label');
                const icon = box.querySelector('i');

                const oldRemove = document.querySelector('.remove-file');
                if (oldRemove) oldRemove.remove();

                const removeBtn = document.createElement('div');
                removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                removeBtn.className = 'remove-file';

                box.appendChild(removeBtn);

                if (file.type.startsWith('image/')) {
                    const imageUrl = URL.createObjectURL(file);
                    box.style.backgroundImage = `url(${imageUrl})`;
                    box.style.backgroundSize = 'cover';
                    if (icon) icon.style.opacity = '0';
                } else {
                    box.style.backgroundImage = 'none';
                    if (icon) {
                        icon.className = file.type === 'application/pdf' ? "fa-solid fa-file-pdf" : "fa-solid fa-file";
                        icon.style.opacity = '1';
                        icon.style.color = "#C5F82A";
                    }
                }

                if (label) label.innerText = file.name.substring(0, 15) + "...";

                removeBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.value = "";
                    box.style.backgroundImage = 'none';
                    if (icon) icon.style.opacity = '1';
                    if (label) label.innerText = "Télécharger";
                    removeBtn.remove();
                });

            });
        }
    });
}
function updateProfile() {
    const profileForm = document.getElementById("profileForm");

    if (profileForm) {
        profileForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(profileForm);

            fetch("/Tamghrabit/profile/update", {
                method: "POST",
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    showAlert(data.message, data.type);
                })
                .catch(err => {
                    showAlert("Erreur serveur", "error");
                    // console.error(err);
                });
        });
    }
}

function showAlert(message, type) {
    const oldAlert = document.querySelector('.alert');
    if (oldAlert) oldAlert.remove();

    const alert = document.createElement("div");
    alert.className = `alert alert-${type}`;

    if (typeof message === 'object' && message !== null) {
        let htmlContent = "";
        Object.values(message).forEach(msg => {
            htmlContent += `<div style="margin-bottom: 5px;">• ${msg}</div>`;
        });
        alert.innerHTML = htmlContent;
    } else {
        alert.textContent = message;
    }

    document.body.prepend(alert);
    setTimeout(() => alert.remove(), 4000);
}

function initApp() {
    renduCountries();
    renduPhones();
    renduFileUploads();
    renduImageProfile();
    updateProfile();
    updateIdentifier();
    updateBankInfos();
}

initApp();
