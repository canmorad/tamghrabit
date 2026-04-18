
function showForm(type) {
    document.getElementById('selection-container').style.display = 'none';
    document.getElementById('form-container-main').style.display = 'block';
    document.querySelectorAll('.form-type').forEach(f => f.style.display = 'none');
    const targetForm = document.getElementById('form-' + type);
    if (targetForm) targetForm.style.display = 'block';

    const titles = {
        'argent': '<i class="fa-solid fa-hand-holding-dollar"></i> Don d\'Argent',
        'association': '<i class="fa-solid fa-building-ngo"></i> Association / ONG',
        'parrainage': '<i class="fa-solid fa-user-graduate"></i> Parrainage',
        'nature': '<i class="fa-solid fa-box-open"></i> Dons & Services'
    };
    document.getElementById('form-title').innerHTML = titles[type] || 'Nouvelle Campagne';
    window.scrollTo(0, 0);
}

function goBack() {
    document.getElementById('form-container-main').style.display = 'none';
    document.getElementById('selection-container').style.display = 'block';
}

function previewImage(event, input) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();

    const parentGroup = input.closest('.form-group');
    const previewContainer = parentGroup.querySelector('.image-preview-container');
    const previewImg = parentGroup.querySelector('.image-preview-img');

    reader.onload = function () {
        if (previewImg) {
            previewImg.src = reader.result;
            previewContainer.style.display = 'block';
        }
    };
    reader.readAsDataURL(file);
}

async function deleteCampagne(id) {
    if (confirm("Voulez-vous vraiment supprimer cette campagne ?")) {
        try {
            const response = await fetch(`{{ url('campagne/delete?id=') }}${id}`, {
                method: 'GET'
            });
            const data = await response.json();

            if (data.type === 'success') {
                alert(data.message);
                window.location.href = "/Tamghrabit/campagnes"; 
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error("Erreur:", error);
        }
    }
}

function updateCampagne() {
    const editForm = document.getElementById('editForm');

    if (editForm) {
        editForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const actionUrl = '/Tamghrabit/campagne/update';

            try {
                const res = await fetch(actionUrl, {
                    method: "POST",
                    body: formData
                });

                const data = await res.json();
                showAlert(data.message, data.type);

                if (data.type === 'success') {
                    setTimeout(() => {
                        window.location.href = '/Tamghrabit/campagnes';
                    }, 1500);
                }

            } catch (err) {
                showAlert("Erreur de connexion au serveur", "error");
                console.error(err);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => initApp());

function createCampagne() {
    const forms = document.querySelectorAll('.form-type form');

    forms.forEach(form => {
        form.addEventListener("submit", async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const actionUrl = '/Tamghrabit' + this.getAttribute('action');

            try {
                const res = await fetch(actionUrl, {
                    method: "POST",
                    body: formData
                });

                const data = await res.json();
                showAlert(data.message, data.type);

                if (data.type == 'success') {
                    setTimeout(() => {
                        window.location.href = '/Tamghrabit/campagnes';
                    }, 1500);
                }

            } catch (err) {
                showAlert("Erreur de connexion au serveur", "error");
                console.error(err);
            }
        });
    });
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
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 4000);
}

function initApp() {
    createCampagne();
    updateCampagne();
}

document.addEventListener('DOMContentLoaded', initApp());
