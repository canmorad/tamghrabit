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
    renderImageProfile();
}

initApp();