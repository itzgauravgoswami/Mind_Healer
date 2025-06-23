let localStream;
const videoCall = document.getElementById('videoCall');
async function startVideoCall(userId) {
    videoCall.classList.remove('d-none');
    try {
        localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
        document.getElementById('localVideo').srcObject = localStream;
        // TODO: Implement WebRTC signaling with a server (e.g., Socket.IO)
        console.log('Starting video call with user:', userId);
    } catch (error) {
        console.error('Error accessing media devices:', error);
    }
}

function toggleMic() {
    if (localStream) {
        localStream.getAudioTracks()[0].enabled = !localStream.getAudioTracks()[0].enabled;
    }
}

function toggleCamera() {
    if (localStream) {
        localStream.getVideoTracks()[0].enabled = !localStream.getVideoTracks()[0].enabled;
    }
}

function endCall() {
    if (localStream) {
        localStream.getTracks().forEach(track => track.stop());
    }
    videoCall.classList.add('d-none');
}