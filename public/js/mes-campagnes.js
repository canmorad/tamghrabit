document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelector('.filter-tab.active').classList.remove('active');
        tab.classList.add('active');
        const status = tab.dataset.status;
        document.querySelectorAll('.premium-card').forEach(card => {
            card.style.display = (status === 'all' || card.dataset.status === status) ? 'block' : 'none';
        });
    });
});

async function terminerCampagne(id) {
    if (confirm("Voulez-vous vraiment marquer cette campagne comme terminée ?")) {
        try {
            const formData = new FormData();
            formData.append('id', id);

            const response = await fetch("/Tamghrabit/campagne/terminer", {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            showAlert(data.message, data.type);

            if (data.type === 'success') {
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }
        } catch (error) {
            showAlert("Erreur de connexion au serveur", "error");
            console.error("Erreur:", error);
        }
    }
}

async function deleteCampagne(id) {
    if (confirm("Voulez-vous vraiment supprimer cette campagne ?")) {
        const formData = new FormData();
        formData.append('id', id);

        try {
            const response = await fetch(`/Tamghrabit/campagne/delete`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();
            showAlert(data.message, data.type);

            if (data.type === 'success') {
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            }

        } catch (error) {
            showAlert("Erreur de connexion au serveur", "error");
            console.error("Erreur:", error);
        }
    }
}

function rechercher() {
    const searchInput = document.getElementById('userSearch');
    const cards = document.querySelectorAll('.premium-card');

    if (searchInput) {
        searchInput.addEventListener('input', function (e) {
            const searchTerm = e.target.value.toLowerCase();

            cards.forEach(card => {
                const campagneName = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
                const amount = card.querySelector('.target')?.textContent.toLowerCase() || '';

                if (campagneName.includes(searchTerm) || amount.includes(searchTerm)) {
                    card.style.display = '';
                    card.style.animation = 'fadeIn 0.3s ease';
                } else {
                    card.style.display = 'none';
                }
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
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 4000);
}

function initApp() {
    rechercher();
}

initApp();