const messagesContainer = document.getElementById('messagesContainer');
const messageInput = document.getElementById('messageInput');
const sendButton = document.getElementById('sendButton');
const chatToggle = document.getElementById('chatToggle');
const chatbotWrapper = document.getElementById('chatbotWrapper');
const minimizeBtn = document.getElementById('minimizeBtn');
const chatIcon = chatToggle.querySelector('.chat-icon');
const closeIcon = chatToggle.querySelector('.close-icon');

let isTyping = false;
let isOpen = false;

// Open/close chatbot
chatToggle.addEventListener('click', () => {
    isOpen = !isOpen;
    chatbotWrapper.style.display = isOpen ? 'flex' : 'none';
    chatIcon.style.display = isOpen ? 'none' : 'block';
    closeIcon.style.display = isOpen ? 'block' : 'none';
});

// Minimaliseer chatbot
minimizeBtn.addEventListener('click', () => {
    chatbotWrapper.style.display = 'none';
    chatIcon.style.display = 'block';
    closeIcon.style.display = 'none';
    isOpen = false;
});

// Automatisch hoogte aanpassen
messageInput.addEventListener('input', () => {
    messageInput.style.height = 'auto';
    messageInput.style.height = messageInput.scrollHeight + 'px';
    sendButton.disabled = !messageInput.value.trim();
});

// Verstuur bericht bij enter
messageInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendUserMessage();
    }
});

sendButton.addEventListener('click', sendUserMessage);

// Bericht toevoegen aan chat
function addMessage(text, sender) {
    const wrapper = document.createElement('div');
    wrapper.className = `message-wrapper ${sender}`;

    const message = document.createElement('div');
    message.className = `message ${sender}-message`;

    const avatar = document.createElement('div');
    avatar.className = `message-avatar ${sender === 'user' ? 'user-avatar' : ''}`;
    avatar.textContent = sender === 'user' ? '👤' : '🤖';

    const content = document.createElement('div');
    content.className = "message-content";

    const p = document.createElement('p');
    p.textContent = text;

    const time = document.createElement('span');
    time.className = "message-time";
    time.textContent = getCurrentTime();

    content.appendChild(p);
    content.appendChild(time);

    if (sender === 'user') {
        message.appendChild(content);
        message.appendChild(avatar);
    } else {
        message.appendChild(avatar);
        message.appendChild(content);
    }

    wrapper.appendChild(message);
    messagesContainer.appendChild(wrapper);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Typing indicator
function showTypingIndicator() {
    isTyping = true;
    const wrapper = document.createElement('div');
    wrapper.className = "message-wrapper bot";
    wrapper.id = "typingIndicator";
    wrapper.innerHTML = `<div class="typing-indicator"><span></span><span></span><span></span></div>`;
    messagesContainer.appendChild(wrapper);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function hideTypingIndicator() {
    isTyping = false;
    document.getElementById('typingIndicator')?.remove();
}

// Huidige tijd
function getCurrentTime() {
    const now = new Date();
    return now.toLocaleTimeString("nl-NL", {
        hour: "2-digit",
        minute: "2-digit"
    });
}

// --- Vraag-antwoord flow ---
const questions = [
    "Zijn er voedsel dat allergieën bevatten?",
    "Hoe moet je iemand rapporteren?"
];

const answers = [
    "Ja, onze voedsel kunnen sporen van noten en melk bevatten. Als je een allergie hebt laat het aan onze medewerkers weten!",
    "Als je iemand wilt rapporteren kan je ons online een bericht sturen of kan je bij de receptie het aanmelden."
];

let currentIndex = 0;

// Functie om de volgende vraag in de inputbalk te zetten
function showNextQuestion() {
    if (currentIndex >= questions.length) {
        messageInput.value = "";
        sendButton.disabled = true;
        return;
    }
    messageInput.value = questions[currentIndex];
    sendButton.disabled = false;
    messageInput.focus();
}

// Functie die wordt uitgevoerd als de gebruiker op Enter klikt
function sendUserMessage() {
    const text = messageInput.value.trim();
    if (!text) return;

    addMessage(text, 'user');
    messageInput.value = '';
    sendButton.disabled = true;

    showTypingIndicator();
    setTimeout(() => {
        hideTypingIndicator();
        addMessage(answers[currentIndex], 'bot');
        currentIndex++;
        showNextQuestion(); // Zet de volgende vraag in de inputbalk
    }, 1000);
}

// Start de flow bij het laden van de pagina
window.addEventListener('load', () => {
    showNextQuestion();
});
