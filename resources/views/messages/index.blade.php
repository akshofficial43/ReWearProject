@extends('layouts.app')

@section('title', 'Messages - ReWear')

@section('styles')
<style>
    /* Messages Index Page Styles */
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    }

    .messages-wrapper {
        min-height: 80vh;
        padding: 30px 20px;
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }

    .messages-container {
        background: #fff;
        width: 100%;
        max-width: 900px;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .messages-header {
        background: linear-gradient(135deg, #20b2aa 0%, #17a2b8 100%);
        color: white;
        padding: 25px 30px;
        box-shadow: 0 4px 20px rgba(32, 178, 170, 0.3);
    }

    .messages-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
    }

    .search-box {
        position: relative;
        margin: 25px 0;
    }

    .search-box input {
        width: 100%;
        padding: 15px 20px 15px 50px;
        border-radius: 30px;
        border: 2px solid #e9ecef;
        font-size: 16px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .search-box input:focus {
        border-color: #20b2aa;
        background: white;
        box-shadow: 0 0 0 3px rgba(32, 178, 170, 0.1);
        outline: none;
    }

    .search-box i {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-size: 18px;
    }

    .messages-content {
        padding: 25px 30px;
        background: #f8f9fa;
    }

    .empty-messages {
        text-align: center;
        padding: 40px 20px;
        border-radius: 15px;
        background: white;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
        animation: fadeIn 0.5s ease;
    }

    .empty-messages i {
        font-size: 60px;
        color: #20b2aa;
        opacity: 0.7;
        margin-bottom: 20px;
    }

    .empty-messages h4 {
        font-size: 22px;
        color: #495057;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .empty-messages p {
        color: #6c757d;
        font-size: 16px;
        margin-bottom: 25px;
    }

    .browse-btn {
        display: inline-block;
        background: linear-gradient(135deg, #20b2aa 0%, #17a2b8 100%);
        color: white;
        padding: 12px 30px;
        border-radius: 30px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 5px 15px rgba(32, 178, 170, 0.4);
    }

    .browse-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 7px 20px rgba(32, 178, 170, 0.5);
        color: white;
    }

    /* Conversation list styling */
    .conversations-list {
        margin-bottom: 30px;
    }
    
    .conversation-item {
        display: flex;
        align-items: center;
        padding: 16px;
        background: white;
        border-radius: 15px;
        margin-bottom: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        position: relative;
    }
    
    .conversation-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .conversation-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        margin-right: 15px;
        flex-shrink: 0;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #20b2aa;
        font-weight: bold;
    }
    
    .conversation-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .conversation-details {
        flex: 1;
        min-width: 0;
    }
    
    .conversation-top {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        align-items: flex-start;
    }
    
    .conversation-name {
        font-weight: 600;
        font-size: 16px;
        color: #495057;
        margin-right: 15px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .conversation-time {
        font-size: 12px;
        color: #6c757d;
        flex-shrink: 0;
    }
    
    .conversation-product {
        display: flex;
        align-items: center;
        font-size: 14px;
        color: #20b2aa;
        margin-bottom: 6px;
    }
    
    .conversation-product-icon {
        margin-right: 6px;
        font-size: 12px;
    }
    
    .conversation-message {
        font-size: 14px;
        color: #6c757d;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .unread-badge {
        position: absolute;
        top: 16px;
        right: 16px;
        background: #20b2aa;
        color: white;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    /* Start conversation card */
    .conversation-help {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .conversation-help h5 {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 18px;
        color: #20b2aa;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .conversation-help h5 i {
        font-size: 20px;
    }

    .conversation-help p {
        color: #6c757d;
        margin-bottom: 15px;
        font-size: 15px;
    }

    .start-conversation-btn {
        color: #20b2aa;
        background: none;
        border: none;
        padding: 0;
        font-weight: 600;
        font-size: 16px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .start-conversation-btn i {
        font-size: 14px;
        transition: transform 0.3s ease;
    }

    .start-conversation-btn:hover {
        color: #17a2b8;
    }

    .start-conversation-btn:hover i {
        transform: translateX(4px);
    }

    .message-form-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .message-form-card .card-body {
        padding: 25px;
    }

    .message-form-card label {
        color: #495057;
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 16px;
    }

    .message-form-card label span {
        color: #20b2aa;
        font-weight: 700;
    }

    .message-textarea {
        width: 100%;
        border: 2px solid #e9ecef;
        border-radius: 15px;
        padding: 15px;
        font-size: 15px;
        resize: none;
        min-height: 100px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .message-textarea:focus {
        border-color: #20b2aa;
        background: white;
        outline: none;
        box-shadow: 0 0 0 3px rgba(32, 178, 170, 0.1);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 20px;
    }

    .btn-cancel {
        padding: 12px 25px;
        border-radius: 30px;
        border: none;
        background: #e9ecef;
        color: #6c757d;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #dee2e6;
    }

    .btn-send {
        padding: 12px 30px;
        border-radius: 30px;
        border: none;
        background: linear-gradient(135deg, #20b2aa 0%, #17a2b8 100%);
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(32, 178, 170, 0.3);
    }

    .btn-send:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 20px rgba(32, 178, 170, 0.4);
    }

    .btn-send i {
        margin-left: 8px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .messages-wrapper {
            padding: 20px 15px;
        }
        
        .messages-container {
            border-radius: 15px;
        }
        
        .messages-header {
            padding: 20px 25px;
        }
        
        .messages-header h1 {
            font-size: 24px;
        }
        
        .messages-content {
            padding: 20px;
        }
    }
</style>
@endsection

@section('content')
<div class="messages-wrapper">
    <div class="messages-container">
        <!-- Messages Header -->
        <div class="messages-header">
            <h1>My Messages</h1>
        </div>
        
        <div class="messages-content">
            <!-- Search Box -->
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search conversations..." class="search-input" id="search-conversations">
            </div>
            
            @if(isset($conversations) && count($conversations) > 0)
                <!-- Conversations List -->
                <div class="conversations-list">
                    @foreach($conversations as $conversation)
                        <a href="{{ route('messages.show', [$conversation->productId, $conversation->userId]) }}" 
                           class="conversation-item">
                            <!-- User Avatar -->
                            <div class="conversation-avatar">
                                @if($conversation->profile_image)
                                    <img src="{{ asset('storage/' . $conversation->profile_image) }}" alt="{{ $conversation->name }}">
                                @else
                                    {{ strtoupper(substr($conversation->name, 0, 1)) }}
                                @endif
                            </div>
                            
                            <!-- Conversation Details -->
                            <div class="conversation-details">
                                <div class="conversation-top">
                                    <div class="conversation-name">{{ $conversation->name }}</div>
                                    <div class="conversation-time">
                                        {{ \Carbon\Carbon::parse($conversation->last_message_time)->diffForHumans(null, true) }}
                                    </div>
                                </div>
                                
                                <div class="conversation-product">
                                    <i class="fas fa-tag conversation-product-icon"></i>
                                    {{ $conversation->productName }}
                                </div>
                                
                                <div class="conversation-message">
                                    {{ \Illuminate\Support\Str::limit($conversation->last_message, 50) }}
                                </div>
                            </div>
                            
                            <!-- Unread Badge -->
                            @if($conversation->unread_count > 0)
                                <div class="unread-badge">{{ $conversation->unread_count }}</div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="empty-messages">
                    <i class="far fa-comment-alt"></i>
                    <h4>No messages yet</h4>
                    <p>When you contact sellers or receive inquiries about your products, you'll see them here.</p>
                    <a href="{{ route('products.index') }}" class="browse-btn">Browse Products</a>
                </div>
            @endif
            
            <!-- New Conversation Form -->
            @if(isset($selectedProduct) && isset($selectedUser))
                <div class="message-form-card">
                    <div class="card-body">
                        <form method="POST" action="{{ url('/start-message') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $selectedProduct->productId }}">
                            <input type="hidden" name="receiver_id" value="{{ $selectedUser->userId }}">
                            
                            <label class="form-label">
                                Send a message to {{ $selectedUser->name }} about 
                                <span>{{ $selectedProduct->name }}</span>
                            </label>
                            
                            <textarea name="content" class="message-textarea" rows="3" 
                                      placeholder="Hi, is this still available?" required></textarea>
                            
                            <div class="form-actions">
                                <a href="{{ route('products.show', $selectedProduct->productId) }}" class="btn-cancel">Cancel</a>
                                <button type="submit" class="btn-send">
                                    Send Message
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-conversations');
    const conversationItems = document.querySelectorAll('.conversation-item');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            conversationItems.forEach(item => {
                const name = item.querySelector('.conversation-name').textContent.toLowerCase();
                const product = item.querySelector('.conversation-product').textContent.toLowerCase();
                const message = item.querySelector('.conversation-message').textContent.toLowerCase();
                
                if (name.includes(searchTerm) || product.includes(searchTerm) || message.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Auto-refresh conversations every 30 seconds
    if (conversationItems.length > 0) {
        setInterval(function() {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newConversations = doc.querySelector('.conversations-list');
                    if (newConversations) {
                        document.querySelector('.conversations-list').innerHTML = newConversations.innerHTML;
                    }
                });
        }, 30000); // 30 seconds
    }
});
</script>
@endpush