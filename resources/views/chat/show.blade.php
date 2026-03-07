@extends('layouts.app')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('chat.index') }}">Chat</a></li>
    <li class="breadcrumb-item active">{{ $conversation->subject }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card support-shell-card mb-4 direct-chat direct-chat-primary">
            <div class="card-header border-0 bg-white pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 font-weight-bold" style="font-size: 1.15rem;">
                    @if(auth()->user()->isClient())
                        Chat with {{ $conversation->developer ? $conversation->developer->name : 'Developer' }}
                    @else
                        Chat with {{ $conversation->client->name }}
                    @endif
                </h3>
                <div class="card-tools">
                    @if($conversation->status == 'active')
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-secondary">Closed</span>
                    @endif
                </div>
            </div>

            <div class="card-body px-4 pb-4 pt-2">
                <div class="direct-chat-messages" id="chat-messages" style="height: 400px; padding: 10px;">
                    @foreach($messages as $message)
                        <div class="direct-chat-msg {{ $message->user_id == auth()->id() ? 'right' : '' }}" data-message-id="{{ $message->id }}">
                            <div class="direct-chat-infos clearfix">
                                <span class="direct-chat-name {{ $message->user_id == auth()->id() ? 'float-right' : 'float-left' }}">
                                    {{ $message->user->name }}
                                </span>
                                <span class="direct-chat-timestamp {{ $message->user_id == auth()->id() ? 'float-left' : 'float-right' }}">
                                    {{ $message->created_at->format('d M H:i') }}
                                </span>
                            </div>
                            <img class="direct-chat-img" src="{{ $message->user->avatar_url }}" alt="User Image">
                            <div class="direct-chat-text">
                                {{ $message->message }}
                                @if($message->hasFile())
                                    <br><br>
                                    <a href="{{ $message->getFileUrl() }}" target="_blank" class="btn btn-sm btn-light">
                                        <i class="fas fa-download"></i> Download File
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($conversation->status == 'active')
                <div class="card-footer bg-light" style="border-radius: 0 0 1.25rem 1.25rem;">
                    <form action="{{ route('chat.send', $conversation) }}" method="POST" id="chat-form">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="message" placeholder="Type Message ..." class="form-control" style="border-radius: 0.5rem 0 0 0.5rem;" required>
                            <span class="input-group-append">
                                <button type="submit" class="btn btn-primary" style="border-radius: 0 0.5rem 0.5rem 0; font-weight: 500;">Send</button>
                            </span>
                        </div>
                    </form>
                    
                    <form action="{{ route('chat.upload', $conversation) }}" method="POST" enctype="multipart/form-data" class="mt-2">
                        @csrf
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="file" class="custom-file-input" id="file-upload" required>
                                <label class="custom-file-label" for="file-upload">Choose file</label>
                            </div>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-secondary">
                                    <i class="fas fa-paperclip"></i> Upload
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="card-footer bg-light">
                    <p class="text-muted mb-0">This conversation is closed.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function escapeHtml(text) {
        return $('<div>').text(text ?? '').html();
    }

    $(document).ready(function() {
        const conversationId = {{ $conversation->id }};
        const currentUserId = {{ auth()->id() }};
        const messagesUrl = "{{ route('chat.messages', $conversation) }}";
        const sendUrl = "{{ route('chat.send', $conversation) }}";
        const markReadUrl = "{{ route('chat.mark-read', $conversation) }}";

        const $chatMessages = $('#chat-messages');
        const $chatForm = $('#chat-form');
        const $messageInput = $chatForm.find('input[name="message"]');
        const $sendButton = $chatForm.find('button[type="submit"]');

        let isFetching = false;
        let isSending = false;
        let lastMessageId = 0;

        $chatMessages.find('.direct-chat-msg').each(function() {
            const nodeId = parseInt($(this).attr('data-message-id'), 10);
            if (!isNaN(nodeId) && nodeId > lastMessageId) {
                lastMessageId = nodeId;
            }
        });

        function scrollToBottom() {
            $chatMessages.scrollTop($chatMessages[0].scrollHeight);
        }

        function renderMessage(msg) {
            const isSent = !!msg.is_sent;
            const sideClass = isSent ? 'right' : '';
            const nameClass = isSent ? 'float-right' : 'float-left';
            const timeClass = isSent ? 'float-left' : 'float-right';

            const fileBlock = msg.has_file
                ? `<br><br><a href="${escapeHtml(msg.file_url)}" target="_blank" class="btn btn-sm btn-light"><i class="fas fa-download"></i> Download File</a>`
                : '';

            return `
                <div class="direct-chat-msg ${sideClass}" data-message-id="${msg.id}">
                    <div class="direct-chat-infos clearfix">
                        <span class="direct-chat-name ${nameClass}">${escapeHtml(msg.user_name)}</span>
                        <span class="direct-chat-timestamp ${timeClass}">${escapeHtml(msg.created_at_full)}</span>
                    </div>
                    <img class="direct-chat-img" src="${escapeHtml(msg.avatar_url || '{{ auth()->user()->avatar_url }}')}" alt="User Image">
                    <div class="direct-chat-text">
                        ${escapeHtml(msg.message)}
                        ${fileBlock}
                    </div>
                </div>
            `;
        }

        function appendMessage(msg) {
            if ($chatMessages.find(`[data-message-id="${msg.id}"]`).length > 0) {
                return;
            }

            $chatMessages.append(renderMessage(msg));

            if (msg.id > lastMessageId) {
                lastMessageId = msg.id;
            }
        }

        function markAsRead() {
            $.post(markReadUrl).fail(function() {
                console.warn('Failed to mark messages as read.');
            });
        }

        function fetchNewMessages() {
            if (isFetching) {
                return;
            }

            isFetching = true;

            $.ajax({
                url: messagesUrl,
                method: 'GET',
                data: {
                    since_id: lastMessageId > 0 ? lastMessageId : undefined,
                },
                success: function(response) {
                    const messages = response.messages || [];

                    if (messages.length > 0) {
                        messages.forEach(function(msg) {
                            appendMessage(msg);
                        });

                        scrollToBottom();
                        markAsRead();
                    }

                    if (response.latest_message_id && response.latest_message_id > lastMessageId) {
                        lastMessageId = response.latest_message_id;
                    }
                },
                complete: function() {
                    isFetching = false;
                }
            });
        }

        $chatForm.on('submit', function(e) {
            e.preventDefault();

            if (isSending) {
                return;
            }

            const message = ($messageInput.val() || '').trim();
            if (!message.length) {
                return;
            }

            isSending = true;
            $sendButton.prop('disabled', true);

            $.ajax({
                url: sendUrl,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    message: message,
                },
                success: function(response) {
                    if (response.success && response.message) {
                        appendMessage(response.message);
                        $messageInput.val('');
                        scrollToBottom();
                        markAsRead();
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal mengirim pesan',
                        text: 'Silakan coba lagi beberapa saat.',
                    });
                },
                complete: function() {
                    isSending = false;
                    $sendButton.prop('disabled', false);
                    $messageInput.focus();
                }
            });
        });

        $('#file-upload').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        scrollToBottom();
        markAsRead();
        setInterval(fetchNewMessages, 2500);
    });
</script>
@endpush
@endsection
