<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Medical Assistant — SHIFA</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg, #0f3460 0%, #16213e 50%, #533483 100%);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

/* ── Back Button ── */
.back-btn {
  position: fixed;
  top: 20px;
  left: 20px;
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(10px);
  color: white;
  padding: 10px 20px;
  border-radius: 50px;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  display: none;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
  border: 1px solid rgba(255,255,255,0.2);
  z-index: 100;
}
.back-btn:hover {
  background: rgba(255,255,255,0.25);
  transform: translateX(-3px);
}

/* ── Main chat card ── */
.chat-card {
  width: 100%;
  max-width: 780px;
  background: white;
  border-radius: 24px;
  box-shadow: 0 32px 80px rgba(0,0,0,0.4);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  height: 88vh;
  max-height: 700px;
}

/* ── Header ── */
.chat-header {
  background: linear-gradient(135deg, #0f3460 0%, #533483 100%);
  padding: 22px 28px;
  display: flex;
  align-items: center;
  gap: 16px;
  flex-shrink: 0;
}
.chat-header-avatar {
  width: 52px;
  height: 52px;
  border-radius: 16px;
  background: rgba(255,255,255,0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  color: white;
  position: relative;
  flex-shrink: 0;
}
.online-dot {
  position: absolute;
  bottom: 3px;
  right: 3px;
  width: 12px;
  height: 12px;
  background: #10b981;
  border-radius: 50%;
  border: 2px solid #1a3a6b;
  animation: pulse-dot 2s infinite;
}
@keyframes pulse-dot {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.2); opacity: 0.8; }
}
.chat-header-info { flex: 1; }
.chat-header-info h2 {
  color: white;
  font-size: 17px;
  font-weight: 700;
  margin-bottom: 3px;
}
.chat-header-info p {
  color: rgba(255,255,255,0.65);
  font-size: 12.5px;
}
.header-badge {
  background: rgba(255,255,255,0.15);
  color: rgba(255,255,255,0.9);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 20px;
  padding: 5px 12px;
  font-size: 11px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 5px;
}

/* ── Messages area ── */
.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  background: #f8fafc;
  scroll-behavior: smooth;
}
.chat-messages::-webkit-scrollbar { width: 5px; }
.chat-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

/* ── Welcome screen ── */
.welcome-screen {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex: 1;
  text-align: center;
  padding: 20px;
}
.welcome-icon {
  width: 80px;
  height: 80px;
  border-radius: 24px;
  background: linear-gradient(135deg, #0f3460, #533483);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 34px;
  color: white;
  margin: 0 auto 20px;
  box-shadow: 0 8px 24px rgba(15,52,96,0.3);
}
.welcome-screen h3 {
  font-size: 20px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 8px;
}
.welcome-screen p {
  font-size: 13.5px;
  color: #64748b;
  max-width: 400px;
  line-height: 1.6;
  margin-bottom: 24px;
}

/* ── Suggested questions ── */
.suggestions {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: center;
}
.suggestion-chip {
  background: white;
  border: 1.5px solid #e2e8f0;
  border-radius: 20px;
  padding: 8px 16px;
  font-size: 12.5px;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 500;
}
.suggestion-chip:hover {
  border-color: #0f3460;
  background: #eff6ff;
  color: #0f3460;
  transform: translateY(-1px);
  box-shadow: 0 3px 8px rgba(15,52,96,0.12);
}

/* ── Message bubbles ── */
.message-row {
  display: flex;
  gap: 10px;
  align-items: flex-end;
  animation: slideIn 0.3s ease;
}
@keyframes slideIn {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}
.message-row.user { flex-direction: row-reverse; }

.msg-avatar {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
  color: white;
}
.msg-avatar.ai   { background: linear-gradient(135deg, #0f3460, #533483); }
.msg-avatar.user { background: linear-gradient(135deg, #e94560, #f43f5e); }

.message-bubble {
  max-width: 70%;
  padding: 12px 16px;
  border-radius: 18px;
  font-size: 13.5px;
  line-height: 1.65;
}
.message-row.ai .message-bubble {
  background: white;
  color: #1e293b;
  border-bottom-left-radius: 4px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.07);
}
.message-row.user .message-bubble {
  background: linear-gradient(135deg, #0f3460, #533483);
  color: white;
  border-bottom-right-radius: 4px;
}
.message-time {
  font-size: 10.5px;
  color: #94a3b8;
  margin-top: 4px;
  padding: 0 4px;
}
.message-row.user .message-time { text-align: right; }

/* ── Typing indicator ── */
.typing-indicator {
  display: none;
}
.typing-indicator.visible { display: flex; }
.typing-dots {
  background: white;
  border-radius: 18px;
  border-bottom-left-radius: 4px;
  padding: 14px 18px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.07);
  display: flex;
  gap: 5px;
  align-items: center;
}
.typing-dot {
  width: 7px;
  height: 7px;
  background: #94a3b8;
  border-radius: 50%;
  animation: bounce 1.2s infinite;
}
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes bounce {
  0%, 80%, 100% { transform: translateY(0); }
  40% { transform: translateY(-8px); }
}

/* ── Disclaimer banner ── */
.disclaimer-bar {
  background: #fffbeb;
  border-top: 1px solid #fde68a;
  padding: 9px 20px;
  font-size: 11.5px;
  color: #92400e;
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

/* ── Input area ── */
.chat-input-area {
  padding: 16px 20px;
  background: white;
  border-top: 1px solid #f1f5f9;
  display: flex;
  gap: 10px;
  align-items: flex-end;
  flex-shrink: 0;
}
.input-wrapper {
  flex: 1;
  position: relative;
}
#chat-input {
  width: 100%;
  padding: 12px 16px;
  border: 1.5px solid #e2e8f0;
  border-radius: 14px;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  color: #1e293b;
  background: #f8fafc;
  resize: none;
  min-height: 48px;
  max-height: 120px;
  line-height: 1.5;
  transition: border-color 0.2s, box-shadow 0.2s;
  overflow-y: auto;
}
#chat-input:focus {
  outline: none;
  border-color: #0f3460;
  background: white;
  box-shadow: 0 0 0 3px rgba(15,52,96,0.08);
}
.send-btn {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: linear-gradient(135deg, #0f3460, #533483);
  color: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  transition: all 0.2s;
  flex-shrink: 0;
}
.send-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(15,52,96,0.4);
}
.send-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Markdown-style formatting in AI replies */
.message-bubble strong { font-weight: 700; }
.message-bubble em { font-style: italic; }
.message-bubble ul { padding-left: 18px; margin: 6px 0; }
.message-bubble li { margin-bottom: 3px; }
.message-bubble p { margin-bottom: 8px; }
.message-bubble p:last-child { margin-bottom: 0; }
.message-row.user .message-bubble a { color: rgba(255,255,255,0.8); }

@media (max-width: 600px) {
  body { padding: 0; align-items: stretch; justify-content: stretch; }
  .chat-card { border-radius: 0; height: 100vh; max-height: none; }
  .back-btn { top: 12px; left: 12px; padding: 8px 14px; font-size: 12px; }
  .chat-header { padding: 16px 20px; }
  .chat-messages { padding: 16px; }
  .message-bubble { max-width: 85%; }
  .header-badge { display: none; }
}
</style>
</head>
<body>

<!-- Back button (JS-driven, role-aware) -->
<a id="back-btn" href="/login" class="back-btn">
  <i class="fas fa-arrow-left" id="back-icon"></i>
  <span id="back-label">Back</span>
</a>

<div class="chat-card">
  <!-- Header -->
  <div class="chat-header">
    <div class="chat-header-avatar">
      <i class="fas fa-robot"></i>
      <span class="online-dot"></span>
    </div>
    <div class="chat-header-info">
      <h2>AI Medical Assistant</h2>
      <p>Powered by Google Gemini · Always available</p>
    </div>
    <div class="header-badge">
      <i class="fas fa-shield-alt"></i> Private & Secure
    </div>
  </div>

  <!-- Messages -->
  <div class="chat-messages" id="chat-messages">
    <!-- Welcome screen shown when no messages -->
    <div class="welcome-screen" id="welcome-screen">
      <div class="welcome-icon"><i class="fas fa-robot"></i></div>
      <h3>Hello! I'm your Medical Assistant</h3>
      <p>Ask me anything about symptoms, medications, or general health information. I provide general guidance only — always consult a qualified doctor for medical decisions.</p>
      <div class="suggestions">
        <div class="suggestion-chip" onclick="sendSuggestion(this)">🤒 What causes headaches?</div>
        <div class="suggestion-chip" onclick="sendSuggestion(this)">💊 How does ibuprofen work?</div>
        <div class="suggestion-chip" onclick="sendSuggestion(this)">🩺 When to see a doctor?</div>
        <div class="suggestion-chip" onclick="sendSuggestion(this)">💤 Tips for better sleep</div>
        <div class="suggestion-chip" onclick="sendSuggestion(this)">🫀 Signs of high blood pressure</div>
        <div class="suggestion-chip" onclick="sendSuggestion(this)">🍎 Healthy diet tips</div>
      </div>
    </div>

    <!-- Typing indicator -->
    <div class="message-row ai typing-indicator" id="typing-indicator">
      <div class="msg-avatar ai"><i class="fas fa-robot"></i></div>
      <div class="typing-dots">
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
      </div>
    </div>
  </div>

  <!-- Disclaimer -->
  <div class="disclaimer-bar">
    <i class="fas fa-exclamation-triangle"></i>
    <span><strong>Disclaimer:</strong> This AI provides general information only. It cannot diagnose. Always consult a qualified healthcare professional for medical advice.</span>
  </div>

  <!-- Input -->
  <div class="chat-input-area">
    <div class="input-wrapper">
      <textarea id="chat-input" placeholder="Ask a medical question..." rows="1"></textarea>
    </div>
    <button class="send-btn" id="send-btn" onclick="sendMessage()" title="Send message">
      <i class="fas fa-paper-plane"></i>
    </button>
  </div>
</div>

<script>
// ── Back button: JS-driven, role-aware ──────────────────────
(function() {
  var btn = document.getElementById('back-btn');
  var label = document.getElementById('back-label');
  var icon = document.getElementById('back-icon');
  var user = null;
  try { user = JSON.parse(localStorage.getItem('auth_user')); } catch(e) {}

  if (user && user.role) {
    var urls = { admin: '/dashboard', doctor: '/dashboard', nurse: '/nurse/dashboard', pharmacy: '/pharmacy-dashboard', lab: '/lab-dashboard' };
    var labels = { nurse: 'Nurse Dashboard', pharmacy: 'Pharmacy Portal', lab: 'Lab Portal' };
    btn.href = urls[user.role] || '/dashboard';
    label.textContent = labels[user.role] || 'Back to Dashboard';
  } else {
    btn.href = '/login';
    icon.className = 'fas fa-sign-in-alt';
    label.textContent = 'Login';
  }
  btn.style.display = 'flex';
})();

// ── Chat logic ──────────────────────────────────────────────
const messagesEl   = document.getElementById('chat-messages');
const inputEl      = document.getElementById('chat-input');
const sendBtn      = document.getElementById('send-btn');
const typingEl     = document.getElementById('typing-indicator');
const welcomeEl    = document.getElementById('welcome-screen');
let messageCount   = 0;

// Auto-resize textarea
inputEl.addEventListener('input', function() {
  this.style.height = 'auto';
  this.style.height = Math.min(this.scrollHeight, 120) + 'px';
});

// Send on Enter (Shift+Enter for new line)
inputEl.addEventListener('keydown', function(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
});

function now() {
  return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function appendMessage(content, role) {
  // Hide welcome screen on first message
  if (messageCount === 0) welcomeEl.style.display = 'none';
  messageCount++;

  const row = document.createElement('div');
  row.className = 'message-row ' + role;

  const avatar = document.createElement('div');
  avatar.className = 'msg-avatar ' + role;
  avatar.innerHTML = role === 'ai'
    ? '<i class="fas fa-robot"></i>'
    : '<i class="fas fa-user"></i>';

  const right = document.createElement('div');

  const bubble = document.createElement('div');
  bubble.className = 'message-bubble';
  bubble.innerHTML = formatMessage(content);

  const time = document.createElement('div');
  time.className = 'message-time';
  time.textContent = now();

  right.appendChild(bubble);
  right.appendChild(time);
  row.appendChild(avatar);
  row.appendChild(right);

  // Insert before typing indicator
  messagesEl.insertBefore(row, typingEl);
  scrollToBottom();
}

function formatMessage(text) {
  // Basic markdown-like formatting
  return text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/^- (.+)$/gm, '<li>$1</li>')
    .replace(/(<li>.*<\/li>)/gs, '<ul>$1</ul>')
    .replace(/\n\n/g, '</p><p>')
    .replace(/\n/g, '<br>')
    .replace(/^(.+)$/, '<p>$1</p>');
}

function scrollToBottom() {
  messagesEl.scrollTop = messagesEl.scrollHeight;
}

function showTyping() {
  typingEl.classList.add('visible');
  scrollToBottom();
}
function hideTyping() {
  typingEl.classList.remove('visible');
}

function sendSuggestion(el) {
  const text = el.textContent.replace(/^[\u{1F300}-\u{1F9FF}]\s*/u, '').trim();
  inputEl.value = text;
  sendMessage();
}

async function sendMessage() {
  const message = inputEl.value.trim();
  if (!message || sendBtn.disabled) return;

  inputEl.value = '';
  inputEl.style.height = 'auto';
  sendBtn.disabled = true;

  appendMessage(message, 'user');
  showTyping();

  try {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const token = localStorage.getItem('auth_token') || '';

    const resp = await fetch('/api/medical-chat', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'Authorization': 'Bearer ' + token,
      },
      body: JSON.stringify({ message })
    });

    const data = await resp.json();
    hideTyping();
    appendMessage(data.reply || 'Sorry, I could not process your request. Please try again.', 'ai');
  } catch(e) {
    hideTyping();
    appendMessage('⚠️ Connection error. Please check your network and try again.', 'ai');
  } finally {
    sendBtn.disabled = false;
    inputEl.focus();
  }
}
</script>
</body>
</html>
