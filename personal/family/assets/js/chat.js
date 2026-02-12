// assets/js/chat.js

async function sendMessage() {
    const txt = document.getElementById("msg-input").value;
    if (!txt) return;

    // Clear input immediately
    document.getElementById("msg-input").value = "";

    // Send to PHP
    await fetch(API_URL + "send.php", {
        method: "POST",
        body: JSON.stringify({
            chat_id: currentChat,
            sender: currentUser.username,
            text: txt,
            type: "text"
        })
    });

    // Manually add to UI instantly (Optimistic UI)
    renderMessage({
        sender: currentUser.username,
        text: txt,
        type: "text"
    }, true);
}

// Render a single message bubble
function renderMessage(msg, isMine = false) {
    const box = document.getElementById("chat-box");
    const div = document.createElement("div");
    
    // Check if it's my message
    const mine = isMine || (msg.sender === currentUser.username);
    
    div.className = `message ${mine ? "sent" : "received"}`;
    div.innerHTML = `
        <div class="sender-name">${mine ? "" : msg.sender}</div>
        <div class="text">${msg.text}</div>
    `;
    
    box.appendChild(div);
    box.scrollTop = box.scrollHeight; // Auto scroll to bottom
}

// Polling loop
async function startSync() {
    setInterval(async () => {
        if (!currentChat) return;

        // Fetch new messages
        const res = await fetch(`${API_URL}sync.php?chat_id=${currentChat}`);
        const data = await res.json();
        
        // Clear box and re-render (Simple version - efficient version would use IDs)
        document.getElementById("chat-box").innerHTML = "";
        data.messages.forEach(msg => renderMessage(msg));
        
    }, 2000); // Check every 2 seconds
}