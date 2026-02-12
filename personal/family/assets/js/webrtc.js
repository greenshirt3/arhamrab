// assets/js/webrtc.js

let localStream;
let peerConnection;
const config = { 'iceServers': [{ 'urls': 'stun:stun.l.google.com:19302' }] };

async function startCall() {
    // 1. Get Camera/Mic
    localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    // Show local video (add <video> tag in HTML to see self)
    
    // 2. Create Connection
    peerConnection = new RTCPeerConnection(config);
    localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

    // 3. Create Offer
    const offer = await peerConnection.createOffer();
    await peerConnection.setLocalDescription(offer);

    // 4. Send Offer to PHP (Signal File)
    fetch(API_URL + "signaling.php", {
        method: "POST",
        body: JSON.stringify({
            type: "offer",
            target: currentChat, // Who we are calling
            sdp: offer
        })
    });
    
    alert("Calling... Waiting for answer.");
}

// Note: You need to add the 'Answer' logic here similar to the Offer logic
// to complete the handshake.