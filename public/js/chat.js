const conn = new WebSocket('ws://localhost:8080');

conn.onmessage = function (e) {
    const data = JSON.parse(e.data);
    const currentConvId = document.getElementById('currentConvId').value;

    if (data.idConversation == currentConvId) {
        renderMessage(data.contenu, 'received');
        scrollBottom();
    }
};

async function openChat(idDestinataire, nomDestinataire) {
    const modal = document.getElementById('chatModal');
    const chatBody = document.getElementById('chatBody');
    const convInput = document.getElementById('currentConvId');

    document.querySelector('.user-status strong').innerText = nomDestinataire;
    chatBody.innerHTML = '<div class="loading">Chargement...</div>';
    modal.style.display = 'flex';

    try {
        const response = await fetch(`/Tamghrabit/messages/getOrCreate?idDestinataire=${idDestinataire}`);
        const data = await response.json();

        if (data.idConversation) {
            convInput.value = data.idConversation;
            await loadHistory(data.idConversation);
        }
    } catch (error) {
        console.error(error);
    }
    scrollBottom();
}

async function loadHistory(idConv) {
    try {
        const response = await fetch(`/Tamghrabit/messages/history?idConversation=${idConv}`);
        const data = await response.json();
        const chatBody = document.getElementById('chatBody');
        chatBody.innerHTML = '';

        if (data.messages) {
            data.messages.forEach(msg => {
                const type = msg.estLeMien ? 'sent' : 'received';
                renderMessage(msg.contenu, type, msg.date);
            });
        }
        scrollBottom();
    } catch (error) {
        console.error(error);
    }
}

async function sendMessage() {
    const input = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
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

                renderMessage(messageText, 'sent', data.data.date);
                input.value = "";
                sendBtn.disabled = true;
                sendBtn.classList.remove('active');
                scrollBottom();
            }
        } catch (error) {
            console.error(error);
        }
    }
}

function renderMessage(text, type, date = "À l'instant") {
    const chatBody = document.getElementById('chatBody');
    const msgDiv = document.createElement('div');
    msgDiv.className = `message ${type}`;
    msgDiv.innerHTML = `${text}<span class="meta">${date}</span>`;
    chatBody.appendChild(msgDiv);
}

function toggleChat() {
    const modal = document.getElementById('chatModal');
    modal.style.display = (modal.style.display === 'flex') ? 'none' : 'flex';
}

function scrollBottom() {
    const chatBody = document.getElementById('chatBody');
    chatBody.scrollTop = chatBody.scrollHeight;
}

document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.getElementById('chatInput');
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

    document.getElementById('sendBtn').addEventListener('click', sendMessage);

    textarea.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
});