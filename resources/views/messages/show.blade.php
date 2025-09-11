@extends('layouts.app')

@section('title', 'Chat with ' . $otherUser->name)

@section('styles')
<style>
/* Beautiful ReWear Chat Design */
body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
}

.chat-wrapper {
    min-height: 100vh;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-container {
    background: #fff;
    width: 100%;
    max-width: 900px;
    height: 80vh;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    position: relative;
}

.chat-header {
    background: linear-gradient(135deg, #20b2aa 0%, #17a2b8 100%);
    color: white;
    padding: 25px 30px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 20px rgba(32, 178, 170, 0.3);
}

.back-btn {
    color: white !important;
    font-size: 20px;
    text-decoration: none !important;
    padding: 10px;
    border-radius: 50%;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.1);
}

.back-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
    color: white !important;
    text-decoration: none !important;
}

.user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: bold;
    color: white;
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.user-info h2 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
}

.product-info {
    font-size: 14px;
    opacity: 0.9;
    margin-top: 5px;
}

.product-info a {
    color: rgba(255, 255, 255, 0.9) !important;
    text-decoration: none !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.product-info a:hover {
    color: white !important;
    border-bottom-color: white;
}

.messages-container {
    flex: 1;
    padding: 30px;
    background: #f8f9fa;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.messages-container::-webkit-scrollbar {
    width: 8px;
}

.messages-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.messages-container::-webkit-scrollbar-thumb {
    background: #20b2aa;
    border-radius: 10px;
}

.message {
    display: flex;
}

.message.sent {
    justify-content: flex-end;
}

.message.received {
    justify-content: flex-start;
}

.message-bubble {
    max-width: 70%;
    padding: 15px 20px;
    border-radius: 20px;
    position: relative;
    font-size: 15px;
    line-height: 1.5;
    animation: fadeInUp 0.3s ease;
}

.message-bubble.sent {
    background: linear-gradient(135deg, #20b2aa 0%, #17a2b8 100%);
    color: white;
    border-bottom-right-radius: 5px;
}

.message-bubble.received {
    background: white;
    color: #495057;
    border-bottom-left-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.message-time {
    font-size: 11px;
    opacity: 0.7;
    margin-top: 5px;
    display: block;
}

.message-bubble.sent .message-time {
    text-align: right;
}

.message-bubble.received .message-time {
    text-align: left;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    padding: 40px;
    text-align: center;
    color: #6c757d;
}

.empty-state i {
    font-size: 70px;
    color: #20b2aa;
    opacity: 0.5;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #495057;
}

.empty-state p {
    font-size: 16px;
    max-width: 400px;
    line-height: 1.6;
}

.message-form {
    padding: 20px;
    background: white;
    border-top: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 15px;
}

.message-input {
    flex: 1;
    border: 2px solid #e9ecef;
    border-radius: 30px;
    padding: 12px 20px;
    font-size: 15px;
    background: #f8f9fa;
    resize: none;
    height: 50px;
    max-height: 120px;
    overflow-y: auto;
    transition: all 0.3s ease;
}

.message-input:focus {
    border-color: #20b2aa;
    background: white;
    outline: none;
}

.send-button {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #20b2aa 0%, #17a2b8 100%);
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    flex-shrink: 0;
    box-shadow: 0 5px 15px rgba(32, 178, 170, 0.3);
}

.send-button:hover:not(:disabled) {
    transform: scale(1.1) rotate(10deg);
    box-shadow: 0 8px 20px rgba(32, 178, 170, 0.4);
}

.send-button:disabled {
    background: #e9ecef;
    color: #adb5bd;
    cursor: not-allowed;
    box-shadow: none;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .chat-wrapper {
        padding: 10px;
    }
    
    .chat-container {
        height: calc(100vh - 20px);
        border-radius: 15px;
    }
    
    .chat-header {
        padding: 15px 20px;
    }
    
    .user-avatar {
        width: 45px;
        height: 45px;
        font-size: 18px;
    }
    
    .user-info h2 {
        font-size: 20px;
    }
    
    .messages-container {
        padding: 20px;
    }
    
    .message-bubble {
        max-width: 85%;
        padding: 12px 15px;
    }
}
</style>
@endsection

@section('content')
<div class="chat-wrapper">
    <div class="chat-container">
        <!-- Chat Header -->
        <div class="chat-header">
            <a href="{{ route('messages.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
            
            <div class="user-avatar">
                @if($otherUser->profile_image)
                    <img src="{{ asset('storage/' . $otherUser->profile_image) }}" alt="{{ $otherUser->name }}">
                @else
                    {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                @endif
            </div>
            
            <div class="user-info">
                <h2>{{ $otherUser->name }}</h2>
                <div class="product-info">
                    About <a href="{{ route('products.show', $product->productId) }}">{{ $product->name }}</a>
                </div>
            </div>
        </div>
        
        <!-- Messages Container -->
        <div class="messages-container" id="messages-container">
            @if(count($messages) > 0)
                @foreach($messages as $message)
                    <div class="message {{ $message->sender_id == Auth::id() ? 'sent' : 'received' }}">
                        <div class="message-bubble {{ $message->sender_id == Auth::id() ? 'sent' : 'received' }}">
                            {{ $message->content }}
                            <span class="message-time">
                                {{ \Carbon\Carbon::parse($message->created_at)->format('g:i A') }}
                                @if($message->sender_id == Auth::id())
                                    @if($message->read)
                                        <i class="fas fa-check-double" style="margin-left: 5px; font-size: 10px;"></i>
                                    @else
                                        <i class="fas fa-check" style="margin-left: 5px; font-size: 10px;"></i>
                                    @endif
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <i class="far fa-comment-alt"></i>
                    <h3>Start the conversation</h3>
                    <p>Send a message to {{ $otherUser->name }} about {{ $product->name }}</p>
                </div>
            @endif
        </div>
        
        <!-- Message Form -->
        <form class="message-form" id="message-form" action="{{ route('messages.store', [$product->productId, $otherUser->userId]) }}" method="POST">
            @csrf
            <textarea class="message-input" id="message-input" name="content" placeholder="Type a message..." required></textarea>
            <button type="submit" class="send-button" id="send-button" disabled>
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const messageForm = document.getElementById('message-form');
    
    // Auto scroll to bottom
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Auto resize textarea
    function autoResize() {
        messageInput.style.height = 'auto';
        messageInput.style.height = Math.min(messageInput.scrollHeight, 120) + 'px';
    }
    
    // Initial scroll
    scrollToBottom();
    
    // Input event listeners
    messageInput.addEventListener('input', function() {
        autoResize();
        
        // Enable/disable send button
        const hasContent = this.value.trim().length > 0;
        sendButton.disabled = !hasContent;
    });
    
    // Enter key handling
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (this.value.trim() && !sendButton.disabled) {
                messageForm.submit();
            }
        }
    });
    
    // Form submission
    messageForm.addEventListener('submit', function(e) {
        const content = messageInput.value.trim();
        if (!content) {
            e.preventDefault();
            return;
        }
        
        // Clear form after submission
        setTimeout(() => {
            messageInput.value = '';
            autoResize();
            sendButton.disabled = true;
        }, 100);
    });
    
    // Auto-refresh messages every 5 seconds
    setInterval(function() {
        const url = window.location.href;
        
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newMessages = doc.querySelector('#messages-container');
                
                if (newMessages && newMessages.innerHTML !== messagesContainer.innerHTML) {
                    messagesContainer.innerHTML = newMessages.innerHTML;
                    scrollToBottom();
                }
            });
    }, 5000); // 5 seconds
});
</script>
@endpush