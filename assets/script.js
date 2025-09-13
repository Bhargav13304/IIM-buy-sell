function loadMessages(productId) {
    fetch(`chat.php?product_id=${productId}`)
        .then(res => res.json())
        .then(data => {
            let messages = document.getElementById('messages');
            messages.innerHTML = '';
            data.forEach(msg => {
                messages.innerHTML += `<div><b>${msg.sender}:</b> ${msg.message}</div>`;
            });
        })
        .catch(err => console.error('Error loading messages:', err));
}

function sendMessage(productId) {
    let input = document.getElementById('chat-input');
    let message = input.value.trim();
    if (!message) return;

    let user = localStorage.getItem('chat_user');
    if (!user) {
        user = prompt("Enter your name:") || "Anonymous";
        localStorage.setItem('chat_user', user);
    }

    fetch('chat.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `product_id=${productId}&message=${encodeURIComponent(message)}&sender=${encodeURIComponent(user)}`
    })
    .then(res => res.json())
    .then(() => {
        input.value = '';
        loadMessages(productId);
    })
    .catch(err => console.error('Error sending message:', err));
}
