
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

function showEditOrg(orgName) {
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    const editView = document.getElementById('edit-org-view');
    editView.classList.add('active');

    if (orgName) {
        document.getElementById('editing-org-name').innerText = orgName;
        const nameInput = document.getElementById('edit-org-input-name');
        if (nameInput) nameInput.value = orgName;
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
