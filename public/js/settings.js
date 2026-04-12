function switchTab(evt, tabName) {
    const tabContents = document.querySelectorAll(".tab-content");
    tabContents.forEach(content => content.classList.remove("active"));
    const tabBtns = document.querySelectorAll(".tab-btn");
    tabBtns.forEach(btn => btn.classList.remove("active"));

    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}

function changePassword() {
    const passwordForm = document.getElementById('passwordForm');
    if (!passwordForm) return;

    passwordForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(passwordForm);
        try {
            const res = await fetch("/Tamghrabit/password/change", {
                method: "POST",
                body: formData
            });

            const data = await res.json();
            showAlert(data.message, data.type);

            if (data.type === 'success') {
                passwordForm.reset();
            }
        } catch (e) {
            showAlert("Erreur serveur", "error");
        }
    });
}

function changeEmail() {
    const emailForm = document.getElementById('emailForm');
    
    if (emailForm) {
        emailForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = this.querySelector('button');
            const originalText = btn.textContent;
            
            btn.disabled = true;
            btn.textContent = "Envoi en cours...";

            const formData = new FormData(this);

            try {
                const res = await fetch("/Tamghrabit/update/email", {
                    method: "POST",
                    body: formData
                });
                const data = await res.json();

                if (data.step === 'otp') {
                    showOtpInput();
                    showAlert(data.message, 'success');
                } else {
                    showAlert(data.message, data.type);
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            } catch (err) {
                showAlert("Erreur lors de l'envoi du code", "error");
                btn.disabled = false;
                btn.textContent = originalText;
            }
        });
    }
}

function showOtpInput() {
    const container = document.getElementById('emailForm');
    container.innerHTML = `
        <div class="section-header">
            <h2><i class="fa-solid fa-shield-halved"></i> Vérification</h2>
            <p>Entrez le code à 8 chiffres envoyé à votre nouvelle adresse.</p>
        </div>
        <div class="form-group">
            <label>Code de vérification (8 chiffres)</label>
            <input type="text" id="otp_code" class="otp-input" placeholder="00000000" maxlength="8" style="text-align:center; font-size: 24px; letter-spacing: 10px;" required>
        </div>
        <div class="form-footer">
            <button type="button" id="btnVerify" onclick="verifyOtp()" class="btn-submit">Vérifier et Enregistrer</button>
        </div>
    `;
}

async function verifyOtp() {
    const code = document.getElementById('otp_code').value;
    const formData = new FormData();
    formData.append('codeOtp', code);

    try {
        const res = await fetch("/Tamghrabit/confirm/email", {
            method: "POST",
            body: formData
        });
        const data = await res.json();

        showAlert(data.message, data.type);

        if (data.type === 'success') {
            setTimeout(() => location.reload(), 2000);
        }
    } catch (err) {
        showAlert("Erreur de confirmation", "error");
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
    changeEmail();
    changePassword();
}

document.addEventListener('DOMContentLoaded', () => {
    const eyeIcons = document.querySelectorAll('.toggle-password');
    eyeIcons.forEach(icon => {
        icon.addEventListener('click', function () {
            const input = this.parentElement.querySelector('input');
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                this.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    initApp();
});