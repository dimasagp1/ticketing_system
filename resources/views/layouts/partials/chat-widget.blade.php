<!-- Chat Widget -->
<div id="chat-widget" class="chat-widget">
    <!-- Chat Button -->
    <div class="chat-toggle" id="chat-toggle">
        <i class="fas fa-comments"></i>
        <span class="badge badge-danger chat-notification-badge" id="chat-notification-badge" style="display: none;">0</span>
    </div>

    <!-- Chat List Panel -->
    <div class="chat-panel" id="chat-panel" style="display: none;">
        <div class="chat-panel-header">
            <h5>Pesan</h5>
            <button class="btn btn-sm" id="close-chat-panel">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="chat-panel-search">
            <input type="text" class="form-control form-control-sm" placeholder="Cari percakapan...">
        </div>
        <div class="chat-panel-body" id="chat-conversations-list">
            <!-- Conversations will be loaded here -->
            <div class="text-center p-3">
                <i class="fas fa-spinner fa-spin"></i> Memuat...
            </div>
        </div>
    </div>

    <!-- Chat Windows Container -->
    <div class="chat-windows-container" id="chat-windows-container">
        <!-- Individual chat windows will be appended here -->
    </div>
</div>

<style>
/* Chat Widget Styles */
.chat-widget {
    --chat-right: 20px;
    --chat-bottom: 20px;
    --chat-toggle-size: 60px;
    --chat-gap: 12px;
    --chat-primary: #3b82f6;
    --chat-secondary: #06b6d4;
    --chat-accent-gradient: linear-gradient(135deg, var(--chat-primary) 0%, var(--chat-secondary) 100%);
    position: fixed;
    bottom: 0;
    right: 0;
    z-index: 9999;
}

/* Chat Toggle Button */
.chat-toggle {
    position: fixed;
    bottom: var(--chat-bottom);
    right: var(--chat-right);
    width: var(--chat-toggle-size);
    height: var(--chat-toggle-size);
    background: var(--chat-accent-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    z-index: 10020;
}

.chat-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0,0,0,0.2);
}

.chat-widget.panel-open .chat-toggle {
    box-shadow: 0 6px 18px rgba(0,0,0,0.22);
}

.chat-toggle i {
    color: white;
    font-size: 24px;
}

.chat-notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    min-width: 20px;
    height: 20px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
}

/* Chat Panel */
.chat-panel {
    position: fixed;
    bottom: calc(var(--chat-bottom) + var(--chat-toggle-size) + var(--chat-gap));
    right: var(--chat-right);
    width: 320px;
    height: min(450px, calc(100vh - 120px));
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 10030;
}

.chat-panel-header {
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--chat-accent-gradient);
    color: white;
    border-radius: 10px 10px 0 0;
}

.chat-panel-header h5 {
    margin: 0;
    font-size: 16px;
}

.chat-panel-header button {
    color: white;
    padding: 0;
    background: none;
    border: none;
}

.chat-panel-search {
    padding: 10px;
    border-bottom: 1px solid #e0e0e0;
}

.chat-panel-body {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
}

.conversation-item {
    display: flex;
    align-items: center;
    padding: 10px;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.2s;
    margin-bottom: 5px;
}

.conversation-item:hover {
    background: #f5f5f5;
}

.conversation-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
    background: var(--chat-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

.conversation-info {
    flex: 1;
    min-width: 0;
}

.conversation-name {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.conversation-preview {
    font-size: 12px;
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.conversation-badge {
    background: #dc3545;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 11px;
    min-width: 18px;
    text-align: center;
}

/* Chat Windows */
.chat-windows-container {
    position: fixed;
    bottom: 0;
    right: calc(var(--chat-right) + var(--chat-toggle-size) + var(--chat-gap));
    display: flex;
    gap: 10px;
    flex-direction: row-reverse;
    z-index: 10010;
}

.chat-window {
    width: 320px;
    height: 400px;
    background: white;
    border-radius: 10px 10px 0 0;
    box-shadow: 0 -2px 20px rgba(0,0,0,0.15);
    display: flex;
    flex-direction: column;
    margin-bottom: 0;
}

.chat-window.minimized {
    height: 50px;
}

.chat-window-header {
    padding: 12px 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px 10px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.chat-window-title {
    font-weight: 600;
    font-size: 14px;
    flex: 1;
}

.chat-window-actions {
    display: flex;
    gap: 10px;
}

.chat-window-actions button {
    background: none;
    border: none;
    color: white;
    padding: 0;
    cursor: pointer;
    font-size: 14px;
}

.chat-window-body {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    background: #f8f9fa;
}

.chat-window.minimized .chat-window-body,
.chat-window.minimized .chat-window-footer {
    display: none;
}

.chat-message {
    margin-bottom: 15px;
    display: flex;
}

.chat-message.sent {
    justify-content: flex-end;
}

.chat-message-bubble {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 18px;
    word-wrap: break-word;
}

.chat-message.received .chat-message-bubble {
    background: white;
    border: 1px solid #e0e0e0;
}

.chat-message.sent .chat-message-bubble {
    background: var(--chat-accent-gradient);
    color: white;
}

.chat-message-time {
    font-size: 10px;
    color: #999;
    margin-top: 5px;
}

.chat-window-footer {
    padding: 10px;
    border-top: 1px solid #e0e0e0;
    background: white;
}

.chat-input-group {
    display: flex;
    gap: 5px;
}

.chat-input-group input {
    flex: 1;
    border: 1px solid #e0e0e0;
    border-radius: 20px;
    padding: 8px 15px;
    font-size: 13px;
}

.chat-input-group button {
    background: var(--chat-accent-gradient);
    color: white;
    border: none;
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

/* Scrollbar */
.chat-panel-body::-webkit-scrollbar,
.chat-window-body::-webkit-scrollbar {
    width: 6px;
}

.chat-panel-body::-webkit-scrollbar-track,
.chat-window-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-panel-body::-webkit-scrollbar-thumb,
.chat-window-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.chat-panel-body::-webkit-scrollbar-thumb:hover,
.chat-window-body::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Response Media Queries */
@media (max-width: 768px) {
    .chat-widget {
        --chat-right: 15px;
        --chat-bottom: 15px;
    }

    .chat-panel {
        width: 300px;
        height: min(450px, calc(100vh - 120px));
    }

    .chat-window {
        width: 280px;
    }

    .chat-windows-container {
        right: calc(var(--chat-right) + var(--chat-toggle-size) + var(--chat-gap));
    }
}

@media (max-width: 576px) {
    .chat-widget {
        --chat-right: 15px;
        --chat-bottom: 15px;
        --chat-toggle-size: 50px;
        --chat-gap: 10px;
    }

    .chat-toggle {
        width: var(--chat-toggle-size);
        height: var(--chat-toggle-size);
    }

    .chat-toggle i {
        font-size: 20px;
    }

    .chat-panel {
        width: calc(100vw - 30px);
        height: min(58vh, calc(100vh - 90px));
        bottom: calc(var(--chat-bottom) + var(--chat-toggle-size) + var(--chat-gap));
        z-index: 10000;
    }

    .chat-windows-container {
        right: 0;
        left: 0;
        bottom: 0;
        gap: 0;
        justify-content: center;
        z-index: 10001;
        pointer-events: none; /* Let clicks pass through empty space */
        flex-direction: row; /* Stack normally on mobile */
    }

    .chat-window {
        width: 100%;
        max-width: 100%;
        height: 100vh; /* Full screen on mobile */
        margin: 0;
        border-radius: 0;
        pointer-events: auto; /* Re-enable clicks */
    }

    .chat-window.minimized {
        width: 200px;
        height: 40px;
        position: fixed;
        bottom: 75px;
        right: 70px;
        border-radius: 5px;
        display: none; /* Hide minimized windows on very small screens or handle differently */
    }

    /* Only show one active chat window on mobile */
    .chat-window:not(:last-child) {
        display: none;
    }

    .chat-window-header {
        border-radius: 0;
        padding: 15px;
    }
    
    .chat-widget {
        z-index: 10002; /* Ensure it's on top of everything on mobile */
    }
}
</style>

@push('scripts')
<script>
$(document).ready(function() {
    let openWindows = [];
    const maxWindows = 3;
    const csrfToken = '{{ csrf_token() }}';

    // Toggle chat panel
    $('#chat-toggle').click(function() {
        $('#chat-panel').toggle();
        $('#chat-widget').toggleClass('panel-open', $('#chat-panel').is(':visible'));

        if ($('#chat-panel').is(':visible') && window.innerWidth <= 992) {
            $('.chat-window').addClass('minimized');
        }

        if ($('#chat-panel').is(':visible')) {
            loadConversations();
        }
    });

    // Close chat panel
    $('#close-chat-panel').click(function() {
        $('#chat-panel').hide();
        $('#chat-widget').removeClass('panel-open');
    });

    // Load conversations using API
    function loadConversations() {
        $.ajax({
            url: '/api/chat/conversations',
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(data) {
                console.log('Conversations loaded:', data);
                renderConversations(data.conversations);
                
                // Update total unread count
                const totalUnread = data.conversations.reduce((sum, conv) => sum + conv.unread_count, 0);
                if (totalUnread > 0) {
                    $('#chat-notification-badge').text(totalUnread).show();
                } else {
                    $('#chat-notification-badge').hide();
                }
            },
            error: function(xhr, status, error) {
                console.error('Load conversations error:', error);
                $('#chat-conversations-list').html('<div class="text-center text-danger p-3">Gagal memuat percakapan</div>');
            }
        });
    }

    // Render conversations list
    function renderConversations(conversations) {
        const $list = $('#chat-conversations-list');
        $list.empty();

        if (conversations.length === 0) {
            $list.html('<div class="text-center text-muted p-3">Belum ada percakapan</div>');
            return;
        }

        conversations.forEach(conv => {
            const initials = conv.subject.substring(0, 2).toUpperCase();
            const unreadBadge = conv.unread_count > 0 ? `<span class="conversation-badge">${conv.unread_count}</span>` : '';
            
            const $item = $(`
                <div class="conversation-item" data-id="${conv.id}">
                    <div class="conversation-avatar">${initials}</div>
                    <div class="conversation-info">
                        <div class="conversation-name">${conv.participant}</div>
                        <div class="conversation-preview">${conv.subject}</div>
                    </div>
                    ${unreadBadge}
                </div>
            `);

            $item.click(function() {
                openChatWindow(conv.id, conv.subject);
            });

            $list.append($item);
        });
    }

    // Open chat window
    function openChatWindow(id, title) {
        // Close panel when opening a room to avoid overlap with chat windows
        $('#chat-panel').hide();
        $('#chat-widget').removeClass('panel-open');

        // Check if already open
        if (openWindows.includes(id)) {
            $(`#chat-window-${id}`).removeClass('minimized');
            return;
        }

        // Limit max windows
        if (openWindows.length >= maxWindows) {
            closeChatWindow(openWindows[0]);
        }

        openWindows.push(id);
        
        // Mark as read immediately when opening
        markAsRead(id);

        const $window = $(`
            <div class="chat-window" id="chat-window-${id}" data-conversation-id="${id}">
                <div class="chat-window-header">
                    <div class="chat-window-title">${title}</div>
                    <div class="chat-window-actions">
                        <button class="minimize-chat"><i class="fas fa-minus"></i></button>
                        <button class="close-chat"><i class="fas fa-times"></i></button>
                    </div>
                </div>
                <div class="chat-window-body" id="chat-body-${id}">
                    <div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>
                </div>
                <div class="chat-window-footer">
                    <div class="chat-input-group">
                        <label class="btn btn-sm btn-light mb-0" style="cursor: pointer;" title="Unggah Berkas">
                            <i class="fas fa-paperclip"></i>
                            <input type="file" class="chat-file-input" data-id="${id}" style="display: none;">
                        </label>
                        <input type="text" placeholder="Ketik pesan..." class="chat-message-input" data-id="${id}">
                        <button class="send-message" data-id="${id}"><i class="fas fa-paper-plane"></i></button>
                    </div>
                    <div class="file-preview-container" data-id="${id}" style="display: none; font-size: 11px; padding: 5px; color: #666;">
                        <i class="fas fa-file"></i> <span class="file-name"></span> <i class="fas fa-times remove-file" style="cursor: pointer; margin-left: 5px;"></i>
                    </div>
                </div>
            </div>
        `);

        // Minimize
        $window.find('.minimize-chat').click(function(e) {
            e.stopPropagation();
            $window.toggleClass('minimized');
        });

        // Close
        $window.find('.close-chat').click(function(e) {
            e.stopPropagation();
            closeChatWindow(id);
        });
        
        // Restore on header click
        $window.find('.chat-window-header').click(function() {
            $window.removeClass('minimized');
            markAsRead(id); // Mark read when restoring
        });
        
        // Mark read when clicking inside body/input (focus)
        $window.on('click focusin', function() {
            markAsRead(id);
        });

        // Initialize file input handlers
        $window.find('.chat-file-input').change(function() {
            const file = this.files[0];
            if (file) {
                const $preview = $window.find(`.file-preview-container[data-id="${id}"]`);
                $preview.find('.file-name').text(file.name);
                $preview.show();
            }
        });

        $window.find('.remove-file').click(function() {
            const $fileInput = $window.find(`.chat-file-input[data-id="${id}"]`);
            $fileInput.val('');
            $window.find(`.file-preview-container[data-id="${id}"]`).hide();
        });

        // Send message
        $window.find('.send-message').click(function() {
            sendMessage(id);
        });

        $window.find('.chat-message-input').keypress(function(e) {
            if (e.which === 13) {
                sendMessage(id);
            }
        });

        $('#chat-windows-container').append($window);
        loadMessages(id);
    }
    
    // Mark conversation as read
    function markAsRead(id) {
        $.ajax({
            url: `/chat/${id}/mark-read`, // Using existing web route
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(data) {
                // If successful, reload conversations to update total badge
                // We limit this to run not too often if we wanted, but for now it's fine
                // to keep UI in sync
                loadConversations();
            }
        });
    }

    // Close chat window
    function closeChatWindow(id) {
        $(`#chat-window-${id}`).remove();
        openWindows = openWindows.filter(wid => wid !== id);
    }

    // Load messages using API
    function loadMessages(id) {
        $.ajax({
            url: `/api/chat/${id}/messages`,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(data) {
                renderMessages(id, data.messages);
                // If there are unread messages in the response (based on current user), mark read
                if (data.unread_count && data.unread_count > 0 && !$(`#chat-window-${id}`).hasClass('minimized')) {
                     markAsRead(id);
                }
            },
            error: function(xhr, status, error) {
                console.error('Load messages error:', error);
                $(`#chat-body-${id}`).html('<div class="text-center text-danger">Gagal memuat pesan</div>');
            }
        });
    }

    // Render messages
    function renderMessages(id, messages) {
        const $body = $(`#chat-body-${id}`);
        $body.empty();

        if (messages.length === 0) {
            $body.html('<div class="text-center text-muted">Belum ada pesan</div>');
            return;
        }

        messages.forEach(msg => {
            let content = msg.message;
            if (msg.has_file && msg.file_url) {
                const extension = msg.file_url.split('.').pop().toLowerCase();
                const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension);
                
                if (isImage) {
                    content += `<div class="mt-2"><a href="${msg.file_url}" target="_blank"><img src="${msg.file_url}" style="max-width: 100%; border-radius: 5px;"></a></div>`;
                } else {
                    content += `<div class="mt-2"><a href="${msg.file_url}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download"></i> Unduh Berkas</a></div>`;
                }
            }

            const statusIcon = msg.is_sent ? (msg.is_read ? '<i class="fas fa-check-double text-primary"></i>' : '<i class="fas fa-check"></i>') : '';
            
            const $message = $(`
                <div class="chat-message ${msg.is_sent ? 'sent' : 'received'}">
                    <div class="chat-message-bubble">
                        ${content}
                        <div class="chat-message-time">
                            ${msg.created_at} 
                            ${msg.is_sent ? `<span class="ml-1">${statusIcon}</span>` : ''}
                        </div>
                    </div>
                </div>
            `);
            $body.append($message);
        });

        $body.scrollTop($body[0].scrollHeight);
    }

    // Send message using API
    function sendMessage(id) {
        const $input = $(`.chat-message-input[data-id="${id}"]`);
        const $fileInput = $(`.chat-file-input[data-id="${id}"]`);
        const message = $input.val().trim();
        const file = $fileInput[0].files[0];

        if (!message && !file) return;

        // Disable input while sending
        $input.prop('disabled', true);
        
        const formData = new FormData();
        if (message) formData.append('message', message);
        if (file) formData.append('file', file);
        
        $.ajax({
            url: `/api/chat/${id}/send`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                // Clear inputs
                $input.val('');
                $fileInput.val('');
                $(`.file-preview-container[data-id="${id}"]`).hide();
                $input.prop('disabled', false).focus();
                
                // Reload messages to show the new one
                loadMessages(id);
            },
            error: function(xhr, status, error) {
                console.error('Send message error:', error);
                alert('Gagal mengirim pesan. Silakan coba lagi.');
                $input.prop('disabled', false);
            }
        });
    }

    // Auto-refresh messages every 10 seconds for open windows
    setInterval(function() {
        openWindows.forEach(id => {
            if (!$(`#chat-window-${id}`).hasClass('minimized')) {
                loadMessages(id);
            }
        });
    }, 10000);

    // Auto-refresh conversations every 30 seconds if panel is open
    setInterval(function() {
        if ($('#chat-panel').is(':visible')) {
            loadConversations();
        }
    }, 30000);
});
</script>
@endpush
