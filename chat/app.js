import { initializeApp } from "https://www.gstatic.com/firebasejs/12.9.0/firebase-app.js";
import { getFirestore, collection, addDoc, onSnapshot, query, orderBy, serverTimestamp, getDocs, where, deleteDoc, doc } from "https://www.gstatic.com/firebasejs/12.9.0/firebase-firestore.js";

// --- CONFIGURATION ---
const firebaseConfig = {
    apiKey: "AIzaSyAW6siu7Fc7lA3Mb_FWH3VglSRroY14mI8",
    authDomain: "arhamfamilychat.firebaseapp.com",
    projectId: "arhamfamilychat",
    storageBucket: "arhamfamilychat.firebasestorage.app",
    messagingSenderId: "969996257602",
    appId: "1:969996257602:web:03057a63578d659ec520db",
    measurementId: "G-E04XGZR4KY"
};

const app = initializeApp(firebaseConfig);
const db = getFirestore(app);

// --- MAIN SYSTEM ---
document.addEventListener('DOMContentLoaded', () => {

    // 1. SAFE ELEMENT SELECTOR (Prevents Null Errors)
    const get = (id) => document.getElementById(id);
    let currentUser = null;

    // 2. FIRST RUN CHECK
    async function checkFirstRun() {
        try {
            const usersRef = collection(db, "users");
            const snapshot = await getDocs(usersRef);
            if (snapshot.empty) {
                await addDoc(usersRef, {
                    name: "Saif", pin: "1122", role: "admin",
                    avatar: "https://ui-avatars.com/api/?name=Saif&background=6366f1&color=fff"
                });
                console.log("Admin Created: Saif / 1122");
            }
        } catch (e) { console.error("DB Error", e); }
    }
    checkFirstRun();

    // 3. LOGIN LOGIC
    const btnEnter = get('btn-enter');
    if (btnEnter) {
        btnEnter.addEventListener('click', async () => {
            const name = get('inp-username').value.trim();
            const pin = get('inp-pin').value.trim();
            
            if (!name || !pin) return alert("Required fields missing");

            const q = query(collection(db, "users"), where("name", "==", name), where("pin", "==", pin));
            const snap = await getDocs(q);

            if (!snap.empty) {
                const data = snap.docs[0].data();
                currentUser = { ...data, id: snap.docs[0].id };
                localStorage.setItem('arhamUser', JSON.stringify(currentUser));
                loadUI();
            } else {
                alert("Access Denied");
            }
        });
    }

    // Auto Login
    const saved = localStorage.getItem('arhamUser');
    if (saved) {
        currentUser = JSON.parse(saved);
        loadUI();
    }

    function loadUI() {
        get('view-login').classList.add('hidden');
        get('view-header').classList.remove('hidden');
        get('view-chat').classList.remove('hidden');
        get('view-input').classList.remove('hidden');
        
        get('img-avatar').src = currentUser.avatar;
        
        if (currentUser.role === 'admin') {
            get('btn-open-admin').classList.remove('hidden');
        }
        startChat();
    }

    // 4. CHAT ENGINE
    function startChat() {
        const q = query(collection(db, "messages"), orderBy("timestamp", "asc"));
        onSnapshot(q, (snap) => {
            const box = get('view-chat');
            box.innerHTML = "";
            snap.forEach(doc => {
                const msg = doc.data();
                const div = document.createElement('div');
                const isMe = msg.sender === currentUser.name;
                div.className = `message ${isMe ? 'sent' : 'received'}`;
                
                let content = `<div style="font-size:10px; opacity:0.7">${msg.sender}</div>`;
                if(msg.image) content += `<img src="${msg.image}" style="max-width:100%; border-radius:10px; margin-top:5px;">`;
                if(msg.text) content += `<div>${msg.text}</div>`;
                
                div.innerHTML = content;
                box.appendChild(div);
            });
            box.scrollTop = box.scrollHeight;
        });
    }

    // 5. SENDING
    const btnSend = get('btn-send');
    if(btnSend) {
        btnSend.addEventListener('click', () => sendMsg());
    }

    async function sendMsg(img = null) {
        const txt = get('inp-msg').value.trim();
        if(!txt && !img) return;

        await addDoc(collection(db, "messages"), {
            sender: currentUser.name,
            text: txt,
            image: img,
            timestamp: serverTimestamp()
        });
        get('inp-msg').value = "";
    }

    // 6. UPLOADS
    const inpFile = get('inp-file');
    if(inpFile) {
        inpFile.addEventListener('change', async () => {
            const file = inpFile.files[0];
            if(!file) return;
            const form = new FormData();
            form.append('file', file);
            try {
                const res = await fetch('upload.php', { method:'POST', body:form });
                const d = await res.json();
                if(d.success) sendMsg(d.url);
            } catch(e) { console.error(e); }
        });
    }

    // 7. ADMIN TOOLS
    const btnAdmin = get('btn-open-admin');
    if(btnAdmin) btnAdmin.addEventListener('click', () => {
        get('view-admin').classList.remove('hidden');
        loadUsers();
    });

    const btnCloseAdmin = get('btn-close-admin');
    if(btnCloseAdmin) btnCloseAdmin.addEventListener('click', () => {
        get('view-admin').classList.add('hidden');
    });

    const btnAddUser = get('btn-add-user');
    if(btnAddUser) btnAddUser.addEventListener('click', async () => {
        const n = get('new-name').value;
        const p = get('new-pin').value;
        const r = get('new-role').value;
        if(n && p) {
            await addDoc(collection(db, "users"), {
                name: n, pin: p, role: r,
                avatar: `https://ui-avatars.com/api/?name=${n}&background=random`
            });
            loadUsers();
            alert("User Added");
        }
    });

    async function loadUsers() {
        const box = get('list-users');
        box.innerHTML = "Loading...";
        const snap = await getDocs(collection(db, "users"));
        box.innerHTML = "";
        snap.forEach(d => {
            const u = d.data();
            const row = document.createElement('div');
            row.style.padding = "10px";
            row.style.borderBottom = "1px solid #eee";
            row.innerHTML = `<b>${u.name}</b> (${u.pin})`;
            box.appendChild(row);
        });
    }

});