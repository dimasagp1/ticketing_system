<!-- Chat Widget -->
<div id="chat-widget" class="chat-widget">
    <!-- Chat Button -->
    <div class="chat-toggle" id="chat-toggle" title="Chat Support">
        <i class="fas fa-comments"></i>
        <span class="badge badge-danger chat-notification-badge" id="chat-notification-badge" style="display: none;">0</span>
    </div>

    <!-- Chat List Panel -->
    <div class="chat-panel" id="chat-panel" style="display: none;">
        <div class="chat-panel-header">
            <h5 class="font-fredoka font-black text-base uppercase mb-0">💬 Chat Support</h5>
            <button class="btn btn-sm" id="close-chat-panel">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="chat-panel-search">
            <input type="text" class="form-control form-control-sm border-2 border-black rounded-xl" placeholder="Cari percakapan...">
        </div>
        <div class="chat-panel-body" id="chat-conversations-list">
            <!-- Conversations will be loaded here -->
            <div class="text-center p-3 font-jakarta font-extrabold text-muted">
                <i class="fas fa-spinner fa-spin"></i> Memuat percakapan...
            </div>
        </div>
    </div>

    <!-- Chat Windows Container -->
    <div class="chat-windows-container" id="chat-windows-container">
        <!-- Individual chat windows will be appended here -->
    </div>
</div>

<style>
/* TOONWORLD Neo-Brutalist Floating Chat Styles */
.chat-widget {
    --chat-right: 24px;
    --chat-bottom: 24px;
    --chat-toggle-size: 58px;
    --chat-gap: 14px;
    --chat-panel-width: 340px;
    --chat-panel-height: 480px;
    --chat-window-width: 330px;
    --chat-window-height: 420px;
    position: fixed;
    bottom: 0;
    right: 0;
    z-index: 9999;
}

/* Chat Toggle Floating Button */
.chat-toggle {
    position: fixed;
    bottom: var(--chat-bottom);
    right: var(--chat-right);
    width: var(--chat-toggle-size);
    height: var(--chat-toggle-size);
    background: #0055FF;
    border: 4px solid #000000;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 4px 4px 0px 0px #000000;
    transition: all 0.2s ease;
    z-index: 10020;
}

body.dark-mode .chat-toggle, .dark .chat-toggle {
    background: #FFE600;
    border: 4px solid #ffffff;
    box-shadow: 4px 4px 0px 0px #FF007A;
}

body.dark-mode .chat-toggle i, .dark .chat-toggle i {
    color: #000000 !important;
}

.chat-toggle:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 6px 6px 0px 0px #000000;
}

body.dark-mode .chat-toggle:hover, .dark .chat-toggle:hover {
    box-shadow: 6px 6px 0px 0px #FFE600;
    background: #FF007A;
}

body.dark-mode .chat-toggle:hover i, .dark .chat-toggle:hover i {
    color: #ffffff !important;
}

.chat-toggle i {
    color: #ffffff;
    font-size: 24px;
    margin: 0;
    line-height: 1;
}

.chat-notification-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    min-width: 22px;
    height: 22px;
    border-radius: 9999px;
    background: #FF007A !important;
    color: #ffffff !important;
    border: 2px solid #000000;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-family: 'Fredoka', cursive;
    font-weight: 800;
}

/* Chat Panel Container */
.chat-panel {
    position: fixed;
    bottom: calc(var(--chat-bottom) + var(--chat-toggle-size) + var(--chat-gap));
    right: var(--chat-right);
    width: var(--chat-panel-width);
    height: var(--chat-panel-height);
    max-height: calc(100vh - 120px);
    background: #ffffff;
    border: 4px solid #000000;
    border-radius: 1.5rem;
    box-shadow: 8px 8px 0px 0px #000000;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 10030;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

body.dark-mode .chat-panel, .dark .chat-panel {
    background: #121212;
    border: 4px solid #FFE600;
    box-shadow: 8px 8px 0px 0px #FFE600;
    color: #ffffff;
}

.chat-panel-header {
    padding: 14px 16px;
    border-bottom: 3px solid #000000;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #0055FF;
    color: #ffffff;
    border-radius: 1.25rem 1.25rem 0 0;
}

body.dark-mode .chat-panel-header, .dark .chat-panel-header {
    background: #FF007A;
    border-bottom: 3px solid #ffffff;
}

.chat-panel-header h5 {
    margin: 0;
    font-family: 'Fredoka', cursive;
    font-weight: 800;
    font-size: 1.05rem;
    color: #ffffff;
    text-transform: uppercase;
}

.chat-panel-header button {
    color: #ffffff;
    padding: 0;
    background: #000000;
    border: 2px solid #ffffff;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.chat-panel-header button:hover {
    background: #FFE600;
    color: #000000;
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
    max-width: calc(100vw - (var(--chat-right) * 2) - var(--chat-toggle-size) - var(--chat-gap));
    overflow-x: auto;
    scrollbar-width: thin;
    z-index: 10010;
}

.chat-window {
    width: var(--chat-window-width);
    height: var(--chat-window-height);
    min-height: 280px;
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
    overflow-wrap: anywhere;
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
        --chat-panel-width: min(300px, calc(100vw - 30px));
        --chat-window-width: min(300px, calc(100vw - 30px));
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
        --chat-panel-width: calc(100vw - 30px);
        --chat-window-width: calc(100vw - 20px);
    }

    .chat-toggle {
        width: var(--chat-toggle-size);
        height: var(--chat-toggle-size);
    }

    .chat-toggle i {
        font-size: 20px;
    }

    .chat-panel {
        width: var(--chat-panel-width);
        height: min(58vh, var(--chat-panel-height));
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
        width: var(--chat-window-width);
        max-width: 100%;
        height: var(--chat-window-height);
        margin: 0;
        border-radius: 12px 12px 0 0;
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
        border-radius: 12px 12px 0 0;
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
    let maxWindows = 3;
    const csrfToken = '{{ csrf_token() }}';

    function computeMaxWindows() {
        if (window.innerWidth <= 576) return 1;
        if (window.innerWidth <= 1200) return 2;
        return 3;
    }

    function adjustChatLayout() {
        const widget = document.getElementById('chat-widget');
        if (!widget) {
            return;
        }

        const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
        const isMobile = viewportWidth <= 576;

        const panelWidth = isMobile ? Math.max(260, viewportWidth - 30) : Math.min(340, Math.max(300, Math.round(viewportWidth * 0.26)));
        const panelHeight = isMobile ? Math.max(300, Math.min(520, viewportHeight - 105)) : Math.max(360, Math.min(460, viewportHeight - 130));
        const windowWidth = isMobile ? Math.max(280, viewportWidth - 20) : Math.min(360, Math.max(300, Math.round(viewportWidth * 0.25)));
        const windowHeight = isMobile ? Math.max(320, Math.min(620, viewportHeight - 85)) : Math.max(300, Math.min(430, viewportHeight - 130));

        widget.style.setProperty('--chat-panel-width', `${panelWidth}px`);
        widget.style.setProperty('--chat-panel-height', `${panelHeight}px`);
        widget.style.setProperty('--chat-window-width', `${windowWidth}px`);
        widget.style.setProperty('--chat-window-height', `${windowHeight}px`);

        maxWindows = computeMaxWindows();
        while (openWindows.length > maxWindows) {
            closeChatWindow(openWindows[0]);
        }
    }

    adjustChatLayout();
    $(window).on('resize orientationchange', adjustChatLayout);

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
        adjustChatLayout();

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

    // Auto-refresh messages every 5 seconds for open windows to feel more real-time.
    setInterval(function() {
        if (document.hidden) {
            return;
        }

        openWindows.forEach(id => {
            if (!$(`#chat-window-${id}`).hasClass('minimized')) {
                loadMessages(id);
            }
        });
    }, 5000);

    // Auto-refresh conversations every 30 seconds if panel is open
    setInterval(function() {
        if ($('#chat-panel').is(':visible')) {
            loadConversations();
        }
    }, 30000);
});
</script>
@endpush
