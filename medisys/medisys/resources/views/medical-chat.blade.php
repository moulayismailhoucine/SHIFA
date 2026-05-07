@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-user-md me-2"></i>
                    <h5 class="mb-0 text-white">Medical Assistant Chat</h5>
                </div>
                
                <div class="card-body chat-container" id="chatBox" style="height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                    <div class="text-center text-muted my-4" id="emptyMessage">
                        <p>Start chatting with your medical assistant!</p>
                    </div>
                </div>
                
                <div class="card-footer bg-white">
                    <form id="chatForm">
                        <div class="input-group">
                            <input type="text" id="messageInput" name="message" class="form-control" placeholder="Ask a medical question..." required autocomplete="off">
                            <button type="submit" class="btn btn-primary" id="sendBtn">
                                <i class="fas fa-paper-plane"></i> Send
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="alert alert-warning mt-3 small">
                <i class="fas fa-exclamation-triangle"></i> <strong>Disclaimer:</strong> This AI assistant provides general information only. It cannot provide a diagnosis. Always consult a qualified healthcare professional for medical advice.
            </div>
        </div>
    </div>
</div>

<style>
    .chat-container {
        display: flex;
        flex-direction: column;
    }
    
    .message-bubble {
        display: flex;
        align-items: flex-end;
        gap: 8px;
    }
    
    .message-content {
        position: relative;
        min-width: 100px;
        word-wrap: break-word;
    }
    
    .message-header {
        font-weight: 600;
        margin-bottom: 4px;
        color: #15803d;
    }
    
    .message-body {
        line-height: 1.4;
    }
    
    .message-timestamp {
        font-size: 11px;
        opacity: 0.7;
        margin-top: 4px;
    }
    
    .typing-dots {
        display: flex;
        gap: 4px;
        align-items: center;
    }
    
    .typing-dots span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #166534;
        animation: typing 1.4s infinite ease-in-out;
    }
    
    .typing-dots span:nth-child(1) {
        animation-delay: 0s;
    }
    
    .typing-dots span:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .typing-dots span:nth-child(3) {
        animation-delay: 0.4s;
    }
    
    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: 0.4;
        }
        30% {
            transform: translateY(-10px);
            opacity: 1;
        }
    }
    
    /* Smooth transitions for better UX */
    .message-content {
        transition: all 0.2s ease;
    }
    
    .message-avatar {
        transition: all 0.2s ease;
    }
    
    /* Hover effects */
    .message-content:hover {
        transform: scale(1.02);
    }
    
    /* Loading state */
    .chat-container.loading {
        opacity: 0.6;
        pointer-events: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const chatBox = document.getElementById('chatBox');
        const emptyMessage = document.getElementById('emptyMessage');
        const sendBtn = document.getElementById('sendBtn');

        function appendMessage(text, isUser) {
            if(emptyMessage) emptyMessage.style.display = 'none';

            const msgDiv = document.createElement('div');
            msgDiv.className = isUser 
                ? 'd-flex justify-content-end mb-3' 
                : 'd-flex justify-content-start mb-3';

            const messageContainer = document.createElement('div');
            messageContainer.className = 'message-bubble';
            messageContainer.style.maxWidth = '75%';

            // Avatar
            const avatar = document.createElement('div');
            avatar.className = 'message-avatar';
            avatar.style.width = '32px';
            avatar.style.height = '32px';
            avatar.style.borderRadius = '50%';
            avatar.style.display = 'flex';
            avatar.style.alignItems = 'center';
            avatar.style.justifyContent = 'center';
            avatar.style.fontSize = '14px';
            avatar.style.marginRight = isUser ? '0' : '8px';
            avatar.style.marginLeft = isUser ? '8px' : '0';

            // Message content
            const innerDiv = document.createElement('div');
            innerDiv.className = 'message-content p-3 rounded shadow-sm';
            innerDiv.style.position = 'relative';

            // Timestamp
            const timestamp = document.createElement('div');
            timestamp.className = 'message-timestamp';
            timestamp.style.fontSize = '11px';
            timestamp.style.opacity = '0.7';
            timestamp.style.marginTop = '4px';

            if (isUser) {
                // Patient message styling - Blue theme
                avatar.style.backgroundColor = '#3b82f6';
                avatar.style.color = 'white';
                avatar.innerHTML = '<i class="fas fa-user"></i>';
                
                innerDiv.style.backgroundColor = '#3b82f6';
                innerDiv.style.color = 'white';
                innerDiv.style.borderBottomRightRadius = '4px';
                innerDiv.style.borderTopLeftRadius = '16px';
                innerDiv.style.borderTopRightRadius = '16px';
                innerDiv.style.borderBottomLeftRadius = '16px';
                
                messageContainer.style.flexDirection = 'row-reverse';
                
                innerDiv.innerHTML = text;
                timestamp.style.textAlign = 'right';
                timestamp.style.color = 'rgba(255, 255, 255, 0.8)';
            } else {
                // Bot message styling - Green theme with error handling
                avatar.style.backgroundColor = '#10b981';
                avatar.style.color = 'white';
                avatar.innerHTML = '<i class="fas fa-robot"></i>';
                
                innerDiv.style.backgroundColor = '#f0fdf4';
                innerDiv.style.color = '#166534';
                innerDiv.style.border = '1px solid #22c55e';
                innerDiv.style.borderBottomLeftRadius = '4px';
                innerDiv.style.borderTopLeftRadius = '16px';
                innerDiv.style.borderTopRightRadius = '16px';
                innerDiv.style.borderBottomRightRadius = '16px';
                
                messageContainer.style.flexDirection = 'row';
                
                // Handle error messages with red styling
                if (text.includes('AI Explanation Unavailable') || text.includes('Unable to generate') || text.includes('Error')) {
                    avatar.style.backgroundColor = '#ef4444';
                    innerDiv.style.backgroundColor = '#fef2f2';
                    innerDiv.style.color = '#dc2626';
                    innerDiv.style.border = '1px solid #ef4444';
                    avatar.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                }
                
                innerDiv.innerHTML = '<div class="message-header"><strong>Medical Assistant</strong></div><div class="message-body">' + text + '</div>';
                timestamp.style.textAlign = 'left';
                timestamp.style.color = 'rgba(22, 101, 60, 0.7)';
            }

            // Add current timestamp
            const now = new Date();
            const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            timestamp.textContent = timeString;
            
            innerDiv.appendChild(timestamp);
            messageContainer.appendChild(avatar);
            messageContainer.appendChild(innerDiv);
            msgDiv.appendChild(messageContainer);
            chatBox.appendChild(msgDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function showTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.id = 'typingIndicator';
            typingDiv.className = 'd-flex justify-content-start mb-3';
            
            const typingContainer = document.createElement('div');
            typingContainer.className = 'message-bubble';
            typingContainer.style.maxWidth = '75%';
            
            const avatar = document.createElement('div');
            avatar.style.width = '32px';
            avatar.style.height = '32px';
            avatar.style.borderRadius = '50%';
            avatar.style.backgroundColor = '#10b981';
            avatar.style.color = 'white';
            avatar.style.display = 'flex';
            avatar.style.alignItems = 'center';
            avatar.style.justifyContent = 'center';
            avatar.style.fontSize = '14px';
            avatar.style.marginRight = '8px';
            avatar.innerHTML = '<i class="fas fa-robot"></i>';
            
            const typingContent = document.createElement('div');
            typingContent.className = 'message-content p-3 rounded shadow-sm';
            typingContent.style.backgroundColor = '#f0fdf4';
            typingContent.style.color = '#166534';
            typingContent.style.border = '1px solid #22c55e';
            typingContent.style.borderBottomLeftRadius = '4px';
            typingContent.style.borderTopLeftRadius = '16px';
            typingContent.style.borderTopRightRadius = '16px';
            typingContent.style.borderBottomRightRadius = '16px';
            typingContent.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
            
            typingContainer.appendChild(avatar);
            typingContainer.appendChild(typingContent);
            typingDiv.appendChild(typingContainer);
            chatBox.appendChild(typingDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function hideTypingIndicator() {
            const typingIndicator = document.getElementById('typingIndicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const message = messageInput.value.trim();
            if(!message) return;

            appendMessage(message, true);
            messageInput.value = '';
            sendBtn.disabled = true;
            
            // Show typing indicator
            showTypingIndicator();

            try {
                // Determine if we are using the API route or web route. The user added POST /api/medical-chat
                const response = await fetch('/api/medical-chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                
                // Hide typing indicator
                hideTypingIndicator();
                
                if(data.reply) {
                    appendMessage(data.reply, false);
                    
                    // Show error details in console for debugging
                    if(data.error) {
                        console.error('Chat Bot Error:', data.error);
                    }
                } else {
                    appendMessage("Sorry, no response from AI.", false);
                    if(data.error) {
                        console.error('Chat Bot Error:', data.error);
                    }
                }
            } catch (err) {
                console.error('Chat Bot Exception:', err);
                appendMessage("Error communicating with AI: " + err.message, false);
            }

            sendBtn.disabled = false;
            messageInput.focus();
        });
    });
</script>
@endsection
