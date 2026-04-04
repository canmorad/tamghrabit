const conn = new WebSocket('ws://localhost:8080');

conn.onmessage = function (e) {
    const data = JSON.parse(e.data);
    const currentConvId = document.getElementById('currentConvId').value;

    if (data.idConversation == currentConvId) {
        renderMessage(data.contenu, 'incoming');
        scrollBottom();
    }
};

async function openDiscussion(idConv, nomComplet, image) {
    const currentConvInput = document.getElementById('currentConvId');
    currentConvInput.value = idConv;

    document.querySelector('.chat-view-header h4').innerText = nomComplet;
    document.querySelector('.chat-view-header img').src = image;

    document.querySelectorAll('.chat-item').forEach(item => item.classList.remove('active'));
    event.currentTarget.classList.add('active');

    await loadHistory(idConv);
}

async function loadHistory(idConv) {
    const chatBody = document.getElementById('chatBody');
    chatBody.innerHTML = '<div class="loading">Chargement...</div>';

    try {
        const response = await fetch(`/Tamghrabit/messages/history?idConversation=${idConv}`);
        const data = await response.json();

        chatBody.innerHTML = '';
        if (data.messages) {
            data.messages.forEach(msg => {
                const type = msg.estLeMien ? 'outgoing' : 'incoming';
                renderMessage(msg.contenu, type, msg.date);
            });
        }
        scrollBottom();
    } catch (error) {
        console.error("Erreur loadHistory:", error);
    }
}

async function sendDashboardMessage() {
    const input = document.getElementById('messageInput');
    const idConv = document.getElementById('currentConvId').value;

    if (input.value.trim() !== "" && idConv !== "") {
        const messageText = input.value;
        const formData = new FormData();
        formData.append('idConversation', idConv);
        formData.append('contenu', messageText);

        try {
            const response = await fetch('/Tamghrabit/message/send', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            if (data.type === 'success') {
                conn.send(JSON.stringify({
                    idConversation: idConv,
                    contenu: messageText,
                }));

                renderMessage(messageText, 'outgoing', data.data.date);
                input.value = "";
                document.getElementById('sendBtn').disabled = true;
                document.getElementById('sendBtn').classList.remove('active');
                scrollBottom();
            }
        } catch (error) {
            console.error("Erreur send:", error);
        }
    }
}

function renderMessage(text, type, date = "À l'instant") {
    const chatBody = document.getElementById('chatBody');
    const msgDiv = document.createElement('div');
    msgDiv.className = `message ${type}`;

    msgDiv.innerHTML = `
        <div class="bubble">${text}</div>
        <span class="m-time">${date}</span>
    `;
    chatBody.appendChild(msgDiv);
}

function scrollBottom() {
    const chatBody = document.getElementById('chatBody');
    chatBody.scrollTop = chatBody.scrollHeight;
}

function toggleDropdown(event, button) {
    event.stopPropagation();

    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu !== button.nextElementSibling) {
            menu.classList.remove('show');
        }
    });

    button.nextElementSibling.classList.toggle('show');
}

async function deleteConversation(event, idConv) {
    event.stopPropagation();

    if (!confirm('Voulez-vous vraiment supprimer cette conversation ?')) {
        return;
    }

    try {
        const res = await fetch(`/Tamghrabit/conversation/delete?id=${idConv}`, {
            method: 'GET'
        });

        const data = await res.json();

        if (data.type === "success") {
            location.reload();
        } else {
            
        }
    } catch (error) {
        console.error("Erreur:", error);
    }

    document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.remove('show'));
}

window.onclick = function (event) {
    if (!event.target.matches('.action-dots') && !event.target.matches('.fa-ellipsis-vertical')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');

    textarea.addEventListener('input', function () {
        if (this.value.trim().length > 0) {
            sendBtn.disabled = false;
            sendBtn.classList.add('active');
        } else {
            sendBtn.disabled = true;
            sendBtn.classList.remove('active');
        }
    });

    textarea.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendDashboardMessage();
        }
    });

    document.getElementById('sendBtn').addEventListener('click', sendDashboardMessage);

    const firstChat = document.querySelector('.chat-item');
    if (firstChat) {
        firstChat.click();
    }
});