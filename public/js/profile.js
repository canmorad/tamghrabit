
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

async function showEditOrg(orgId) {
    document.querySelectorAll('.tab-content, .tab-btn').forEach(el => el.classList.remove('active'));

    const editView = document.getElementById('edit-org-view');
    editView.classList.add('active');

    try {
        const res = await fetch(`/Tamghrabit/organisation/get?id=${orgId}`);
        const org = await res.json();

        editView.innerHTML = `
            <form action="" method="POST" enctype="multipart/form-data" id="form-edit-org">
                <input type="hidden" name="id" value="${orgId}">

                <div class="org-header-flex" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <h3 class="section-title">Modifier l'organisation</h3>
                        <span style="color: #64748B; font-size: 13px;">${org.nom}</span>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" class="btn-submit outline" onclick="switchTab(null, 'orgs')">Annuler</button>
                        <button type="submit" class="btn-submit">Mettre à jour</button>
                    </div>
                </div>

                <div class="grid-row">
                    <div class="form-group">
                        <label>Nom de l'ONG</label>
                        <input type="text" name="nom" value="${org.nom || ''}" placeholder="Nom officiel" required>
                    </div>
                    <div class="form-group">
                        <label>Identifiant Fiscal (IF)</label>
                        <input type="text" name="identifiantFiscal" value="${org.identifiantFiscal || ''}" placeholder="N° IF (ex: 12345678)">
                    </div>
                </div>

                <div class="grid-row">
                    <div class="form-group full">
                        <label>Adresse du siège</label>
                        <input type="text" name="adresse" value="${org.adresse || ''}" placeholder="Ville, Quartier...">
                    </div>
                </div>

                <div class="grid-row">
                    <div class="form-group full">
                        <label>RIB de l'organisation (24 chiffres)</label>
                        <input type="text" name="ribAssociation" value="${org.ribAssociation || ''}" placeholder="RIB Bancaire">
                    </div>
                </div>

                <h4 style="margin: 20px 0 10px 0; color: #64748B; font-size: 14px;">Documents de vérification</h4>
                <div class="docs-upload-grid">
                    ${renderDocBox('Récépissé de dépôt', 'recepisse', 'edit_01', 'fa-file-contract', org.recepisse)}
                    ${renderDocBox("PV d'élection", 'pvElection', 'edit_o_pv', 'fa-users-gear', org.pvElection)}
                    ${renderDocBox('Statuts', 'statuts', 'edit_o2', 'fa-file-lines', org.statuts)}
                    ${renderDocBox('Attestation RIB', 'attestationRib', 'edit_o3', 'fa-building-columns', org.attestationRib)}
                    ${renderDocBox('CIN Président (Recto)', 'cniPresidentRecto', 'edit_o4', 'fa-id-card', org.cniPresidentRecto)}
                    ${renderDocBox('CIN Président (Verso)', 'cniPresidentVerso', 'edit_o5', 'fa-address-card', org.cniPresidentVerso)}
                </div>
            </form>`;
        renderFileUploads();
        updateOrg();

    } catch (e) {
        console.error("Erreur loading org data", e);
        showAlert("Erreur lors du chargement des données", "error");
    }
}

function renderDocBox(label, name, id, icon, file) {
    const style = file ? `style="background-image: url('${file}'); overflow: hidden; background-size: cover; border-color: #C5F82A;"` : "";
    const iconClass = file ? "fa-solid fa-file-circle-check" : `fa-solid ${icon}`;
    const iconOpacity = file ? "style='opacity: 0.8; color: #C5F82A;'" : "";

    return `
        <div class="doc-box" >
            <label>${label}</label>
            <div class="upload-square" ${style}>
                <input type="file" name="${name}" id="${id}" hidden>
                <label for="${id}" style="cursor: pointer;">
                    <i class="${iconClass}" ${iconOpacity}></i>
                    <span>${file.split('/').pop()}</span>
                </label>
            </div>
        </div>`;
}

function updateOrg() {
    const formEditOng = document.getElementById('form-edit-org');
    if (!formEditOng) return;

    formEditOng.addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(formEditOng);
        try {
            const res = await fetch("/Tamghrabit/organisation/update", {
                method: "POST",
                body: formData
            });

            const data = await res.json();

            showAlert(data.message, data.type);
            if (data.type === 'success') {
                switchTab(null, 'orgs');
                renderOrgs();
            }
        } catch (e) {
            showAlert("Erreur serveur", "error");
            console.error(e);
        }
    });
}

function createOrg() {
    const formAddOng = document.getElementById('form-add-ong');

    formAddOng.addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(formAddOng);
        try {
            const res = await fetch("/Tamghrabit/organisation/store", {
                method: "POST",
                body: formData
            });

            const data = await res.json();

            showAlert(data.message, data.type);

            if (data.type === 'success') {
                switchTab(null, 'orgs');
                renderOrgs();
            }
        } catch (e) {
            showAlert("Erreur serveur", "error");
            console.log(e)
        }
    });
}

async function renderOrgs() {
    try {
        const orgsContainer = document.getElementById('orgs-container');
        const res = await fetch("/Tamghrabit/organisation/index");
        const data = await res.json();

        orgsContainer.innerHTML = "";

        if (data.orgs && data.orgs.length > 0) {
            data.orgs.forEach(org => {
                let statusText = 'En attente';
                let statusColor = '#EAB308'; 

                if (org.status === 'approuvee') {
                    statusText = 'Agréée';
                    statusColor = '#C5F82A'; //
                } else if (org.status === 'refusee') {
                    statusText = 'Refusée';
                    statusColor = '#EF4444';
                }

                orgsContainer.innerHTML += `
                <div class="org-card-item">
                    <div class="org-info">
                        <span class="org-name">${org.nom}</span><br>
                        <span class="org-status">Statut : 
                            <strong style="color: ${statusColor};">
                                ${statusText}
                            </strong>
                        </span>
                    </div>
                    <div class="org-btns">
                        <button class="icon-action-btn" onclick="showEditOrg('${org.id}')">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                        <button class="icon-action-btn delete" onclick="deleteOrg('${org.id}')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>`;
            });
        } else {
            orgsContainer.innerHTML = `
                <div style="text-align: center; padding: 20px; color: #64748B;">
                    <i class="fa-solid fa-folder-open" style="font-size: 24px; margin-bottom: 10px; display: block;"></i>
                    Aucune organisation trouvée.
                </div>`;
        }
    } catch (e) {
        console.error("Erreur render orgs:", e);
        document.getElementById('orgs-container').innerHTML = "<p>Erreur lors du chargement des données.</p>";
    }
}

function backToOrgs() {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.getElementById('orgs').classList.add('active');

    const orgTabBtn = document.querySelector('.tab-btn[onclick*="orgs"]');
    if (orgTabBtn) orgTabBtn.classList.add('active');
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

async function renderIdentifier() {
    const identifierForm = document.getElementById("identifierForm");
    const select = identifierForm.querySelector('select');

    if (!identifierForm) return;

    try {
        const res = await fetch("/Tamghrabit/identifier/show");
        const data = await res.json();

        if (data.passport) {
            select.value = "passport"
        } else if(data.passport) {
            select.value = "cni"
        }

        toggleIdUploads()

        identifierForm.querySelectorAll("input").forEach(input => {
            const box = input.closest('.file-upload-box');
            const label = box.querySelector("span");
            const icon = box.querySelector("i");

            if (data[input.name]) {
                const fileUrl = data[input.name];

                box.style.backgroundImage = `url(${fileUrl})`;
                box.style.backgroundSize = "cover";
                box.style.borderColor = "#C5F82A";

                if (icon) {
                    icon.className = "fa-solid fa-file-circle-check";
                    icon.style.opacity = "1";
                }

                if (label) {
                    label.innerText = data[input.name];
                }
            }
        });
    } catch (err) {
        console.error("Erreur render identifier:", err);
    }
}

function updateIdentifier() {
    const identifierForm = document.getElementById("identifierForm");

    if (identifierForm) {
        identifierForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const formData = new FormData(identifierForm);
            try {
                const res = await fetch("/Tamghrabit/identifier/update", {
                    method: "POST",
                    body: formData
                });

                const data = await res.json(); 
                console.log(data)

                showAlert(data.message, data.type);

            } catch (e) {
                showAlert("Erreur serveur", "error");
                console.error(e);
            }

        });
    }
}

async function renderBankInfos() {
    const bankForm = document.getElementById("bankForm");
    if (!bankForm) return;

    try {
        const res = await fetch("/Tamghrabit/bank/index");
        const data = await res.json();

        bankForm.querySelector("[name=rib]").value = data.rib ?? "";
        const fileInput = bankForm.querySelector("[name=attestationRib]");
        const box = fileInput.closest('.file-upload-box');
        const label = box.querySelector("span");
        const icon = box.querySelector("i");

        if (data.attestationRib) {
            const fileUrl = data.attestationRib;

            box.style.backgroundImage = `url(${fileUrl})`;
            box.style.backgroundSize = "cover";
            box.style.borderColor = "#C5F82A";

            if (icon) {
                icon.className = "fa-solid fa-file-circle-check";
                icon.style.opacity = "1";
            }

            if (label) {
                label.innerText = data.attestationRib;
            }
        }
    } catch (err) {
        console.error("Erreur render bank:", err);
    }
}

async function updateBankInfos() {
    const bankForm = document.getElementById("bankForm");

    if (bankForm) {
        bankForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const formData = new FormData(bankForm);

            try {
                const response = await fetch("/Tamghrabit/bank/update", {
                    method: "POST",
                    body: formData
                });

                if (!response.ok) throw new Error("Erreur réseau");

                const data = await response.json();

                if (data.type = "success")
                    renderBankInfos();

                showAlert(data.message, data.type);

            } catch (err) {
                console.error("Erreur:", err);
                showAlert("Erreur serveur ou connexion impossible", "error");
            }
        });
    }
}

async function renderImageProfile() {
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

async function renderCountries() {
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

async function renderPhones() {
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

function renderFileUploads() {
    const allInputs = document.querySelectorAll('input[type="file"]');
    allInputs.forEach(input => {
        if (input.name != "imageProfile") {
            input.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                const box = this.closest('.file-upload-box, .upload-square');
                const label = box.querySelector('span') || box.querySelector('label');
                const icon = box.querySelector('i');

                if (box) {
                    box.style.borderColor = '#E2E8F0';
                    box.style.backgroundImage = 'none';
                }

                if (icon) {
                    icon.className = file.type === 'application/pdf' ? "fa-solid fa-file-pdf" : "fa-solid fa-file";
                    icon.style.opacity = '1';
                    icon.style.color = "#C5F82A";
                }

                if (label) label.innerText = file.name.split('/').pop();

            });
        }
    });
}

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

                if (label) label.innerText = file.name.split('/').pop();

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
    renderCountries();
    renderPhones();
    renderFileUploads();
    renderImageProfile();
    updateProfile();
    updateIdentifier();
    updateBankInfos();
    renderBankInfos();
    renderIdentifier();
    createOrg();
    renderOrgs();
}

initApp();
