// ==========================================
// 1. CONFIGURATION
// ==========================================
// Since index.html is in 'family/', we just point to the 'api/' folder next to it.
const API_URL = "api/"; 
const REFRESH_RATE = 2000; // Check for new messages every 2 seconds

let currentUser = localStorage.getItem("arham_user");
let syncInterval = null;

// ==========================================
// 2. INITIALIZATION (Run on Load)
// ==========================================
window.onload = function() {
    if (currentUser) {
        showChatScreen();
    } else {
        showLoginScreen();
    }

    // Add "Enter Key" support for login and chat
    document.getElementById("pass-in").addEventListener("keypress", function(e) {
        if (e.key === "Enter") doLogin();
    });
    document.getElementById("msg-in").addEventListener("keypress", function(e) {
        if (e.key === "Enter") sendMessage();
    });
};

// ==========================================
// 3. AUTHENTICATION (Login / Logout)
// ==========================================
async function doLogin() {
    const u = document.getElementById("user-in").value.trim();
    const p = document.getElementById("pass-in").value.trim();
    const btn = document.querySelector("button[onclick='doLogin()']");

    if (!u || !p) {
        alert("Please enter both Name and Password");
        return;
    }

    // Disable button to prevent double-click
    btn.disabled = true;
    btn.innerText = "Checking...";

    try {
        const res = await fetch(API_URL + "auth.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "login", username: u, password: p })
        });

        const data = await res.json();

        if (data.status === "success") {
            currentUser = u;
            localStorage.setItem("arham_user", u);
            showChatScreen();
        } else {
            alert(data.message || "Login Failed");
        }
    } catch (error) {
        console.error("Login Error:", error);
        alert("Connection Error. Check your internet.");
    } finally {
        btn.disabled = false;
        btn.innerText = "Login";
    }
}

function logout() {
    currentUser = null;
    localStorage.removeItem("arham_user");
    clearInterval(syncInterval); // Stop downloading messages
    showLoginScreen();
}

// ==========================================
// 4. CHAT FUNCTIONS
// ==========================================
async function sendMessage() {
    const input = document.getElementById("msg-in");
    const text = input.value.trim();

    if (!text) return;

    // 1. Clear input immediately (for fast feel)
    input.value = "";

    try {
        await fetch(API_URL + "send.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                sender: currentUser,
                text: text,
                type: "text"
            })
        });

        // 2. Refresh messages immediately to see your own msg
        syncMessages();
        
    } catch (error) {
        console.error("Send Error:", error);
        alert("Message not sent. Check connection.");
    }
}

async function syncMessages() {
    try {
        const res = await fetch(API_URL + "sync.php");
        const messages = await res.json();
        
        const chatBox = document.getElementById("chat-box");
        let html = "";
        
        // Loop through messages and build HTML
        messages.forEach(msg => {
            // Check if message is mine or others
            const isMe = (msg.sender === currentUser);
            const msgClass = isMe ? "me" : "other";
            
            html += `
                <div class="msg ${msgClass}">
                    <div class="sender">${isMe ? "You" : msg.sender}</div>
                    <div class="text">${msg.text}</div>
                    <div class="time">${msg.time || ""}</div>
                </div>
            `;
        });

        // Only update HTML if new messages arrived (prevents flickering)
        if (chatBox.innerHTML !== html) {
            chatBox.innerHTML = html;
            // Scroll to bottom
            chatBox.scrollTop = chatBox.scrollHeight;
        }

    } catch (error) {
        console.error("Sync Error:", error);
    }
}

// ==========================================
// 5. UI SWITCHING
// ==========================================
function showLoginScreen() {
    document.getElementById("login-screen").classList.remove("hidden");
    document.getElementById("chat-screen").classList.add("hidden");
}

function showChatScreen() {
    document.getElementById("login-screen").classList.add("hidden");
    document.getElementById("chat-screen").classList.remove("hidden");
    
    // Start the auto-refresh loop
    syncMessages(); // Run once immediately
    syncInterval = setInterval(syncMessages, REFRESH_RATE);
}