const chatBox = document.getElementById('chatBox');
const messageInput = document.getElementById('messageInput');
const sendBtn = document.getElementById('sendBtn');
// Remove the exposed API key and URL - now using backend API
const API_URL = 'chatbot_api.php';

// Add debugging
console.log('ChatBot JavaScript loaded');
console.log('Elements found:', {
    chatBox: !!chatBox,
    messageInput: !!messageInput,
    sendBtn: !!sendBtn
});

// Function to send message
async function sendMessage() {
    const message = messageInput.value.trim();
    if (!message) return;

    console.log('Sending message:', message);

    const userMessage = document.createElement('div');
    userMessage.classList.add('chat-message', 'user-message');
    userMessage.textContent = message;
    chatBox.appendChild(userMessage);

    const thinkingMessage = document.createElement('div');
    thinkingMessage.classList.add('chat-message', 'bot-message');
    thinkingMessage.textContent = 'Thinking...';
    chatBox.appendChild(thinkingMessage);
    chatBox.scrollTop = chatBox.scrollHeight;

    try {
        console.log('Making API request to:', API_URL);
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message })
        });
        
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.error) {
            thinkingMessage.textContent = 'Error: ' + data.error;
        } else {
            thinkingMessage.textContent = data.candidates[0].content.parts[0].text;
        }
    } catch (error) {
        console.error('Fetch error:', error);
        thinkingMessage.textContent = 'Error: Could not connect to chatbot service';
    }
    chatBox.scrollTop = chatBox.scrollHeight;
    messageInput.value = '';
}

// Event listeners
sendBtn.addEventListener('click', sendMessage);

// Add Enter key support
messageInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});