<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\Chat;
use App\Models\ProjectRequest;
use App\Models\Queue;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\SystemEmailNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isClient()) {
            $conversations = ChatConversation::where('client_id', $user->id)
                ->with(['developer', 'projectRequest', 'queue'])
                ->latest()
                ->paginate(15);
        } else {
            // Admin / Super Admin (Staff)
            $conversations = ChatConversation::with(['client', 'developer', 'projectRequest', 'queue'])
                ->latest()
                ->paginate(15);
        }

        // Return JSON for AJAX requests
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'conversations' => $conversations->map(function($conv) use ($user) {
                    $unreadCount = $conv->getUnreadMessagesCount($user->id);
                    
                    // If user is NOT client (i.e. Developer/Admin), they generally want notifications from Clients.
                    // However, getUnreadMessagesCount already excludes own messages.
                    // The user requested "Notifikasi chat hanya untuk update dari Client"
                    if (!$user->isClient()) {
                        $unreadCount = $conv->messages()
                            ->where('user_id', '!=', $user->id)
                            ->where('is_read', false)
                            ->whereHas('user', function($q) {
                                $q->where('role', 'client');
                            })
                            ->count();
                    }

                    return [
                        'id' => $conv->id,
                        'subject' => $conv->subject,
                        'status' => $conv->status,
                        'unread_count' => $unreadCount,
                        'last_message_at' => $conv->last_message_at?->diffForHumans(),
                        'participant' => $user->isClient() 
                            ? ($conv->developer ? $conv->developer->name : 'Admin/Petugas')
                            : $conv->client->name,
                    ];
                })
            ]);
        }

        return view('chat.index', compact('conversations'));
    }

    public function show(ChatConversation $conversation)
    {
        $user = auth()->user();

        // Authorization check
        if ($user->isClient() && $conversation->client_id !== $user->id) {
            abort(403);
        }
        if ($user->isDeveloper() && $conversation->developer_id !== $user->id) {
            abort(403);
        }

        $conversation->load(['client', 'developer', 'projectRequest', 'queue']);
        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        $conversation->markAllAsRead($user->id);

        return view('chat.show', compact('conversation', 'messages'));
    }

    public function create(Request $request)
    {
        $projectRequestId = $request->project_request_id;
        $queueId = $request->queue_id;
        
        $clients = [];
        $projectRequests = [];

        // If Admin/SuperAdmin creating a chat
        if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) {
            $clients = User::where('role', 'client')->where('status', 'active')->get();
            $projectRequests = ProjectRequest::with('client')->latest()->get();
        } elseif (auth()->user()->isClient()) {
            $projectRequests = auth()->user()->projectRequests;
        }

        return view('chat.create', compact('projectRequestId', 'queueId', 'clients', 'projectRequests'));
    }

    public function store(Request $request)
    {
        $rules = [
            'project_request_id' => 'nullable|exists:project_requests,id',
            'queue_id' => 'nullable|exists:queues,id',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ];

        $user = auth()->user();

        if ($user->isClient()) {
            // Client creates a chat, developer_id will be null or assigned to an admin later
            // No need for developer_id in validation for client
        } elseif ($user->isDeveloper()) {
            $rules['client_id'] = 'required|exists:users,id';
        } else {
             // Admin can likely choose both (or just Client if Admin is acting as staff)
            $rules['client_id'] = 'required|exists:users,id';
        }

        $validated = $request->validate($rules);

        $clientId = null;
        $developerId = null;

        if ($user->isClient()) {
            $clientId = $user->id;
            // Developer ID is now irrelevant or should be assigned to an Admin/System?
            // We can leave it null or assign to a default Admin if the schema requires it.
            // But we removed the 'Developer' role, so 'developer_id' column on `chat_conversations` might be misleading.
            // Let's assume for now it stays NULL or we assign a generic internal ID if needed.
            // But the user said "Use Admin instead".
            // So queries for Admin dashboard should look for where developer_id IS NULL or ignored.
            $developerId = null; 
            
            // If project request is supplied, maybe get its assigned 'developer' (which is now Admin?)
            // If we removed developer_id from ProjectRequest, link is gone.
        } else {
            // Admin creating chat
            $developerId = $user->id; // Admin acts as developer
            $clientId = $validated['client_id'];
        }

        // Logic check: The chat system relies on 'developer_id' field in DB?
        // If we didn't drop the column in DB, we can still use it to store the Admin ID who handles the chat.
        
        $conversation = ChatConversation::create([
            'client_id' => $clientId,
            // 'developer_id' => $developerId, // Using Admin ID here
            // Wait, if Client initiates, who is the 'developer'? 
            // If Null, we need to ensure Admins can see it.
            'developer_id' => $developerId,
            'project_request_id' => $validated['project_request_id'] ?? null,
            'queue_id' => $validated['queue_id'] ?? null,
            'subject' => $validated['subject'] ?? 'New Conversation',
            'status' => 'active',
            'last_message_at' => now(),
        ]);

        Chat::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
            'message_type' => 'text',
            'is_read' => false,
        ]);

        ActivityLog::log('create_conversation', 'Started new conversation', $conversation);

        return redirect()->route('chat.show', $conversation)
            ->with('success', 'Percakapan berhasil dimulai.');
    }

    public function sendMessage(Request $request, ChatConversation $conversation)
    {
        $request->validate([
            'message' => 'required_without:file|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $messageType = 'text';
        $filePath = null;
        $message = $request->message;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('chat-files', 'public');
            $messageType = 'file';
            $message = $message ?? 'Sent a file: ' . $file->getClientOriginalName();
        }

        $chat = Chat::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'message' => $message,
            'file_path' => $filePath,
            'message_type' => $messageType,
            'is_read' => false,
        ]);

        // Update conversation last_message_at
        $conversation->update(['last_message_at' => now()]);

        $sender = auth()->user();
        $receiver = null;

        if ($sender->id === $conversation->client_id) {
            $receiver = $conversation->developer;
        } else {
            $receiver = $conversation->client;
        }

        $subject = $conversation->subject ?: 'Percakapan Dukungan';
        $messagePreview = Str::limit(trim(strip_tags((string) $message)), 180, '...');

        if ($receiver) {
            SystemEmailNotifier::sendToUser(
                $receiver,
                'Pesan Chat Baru: ' . $subject,
                'Anda menerima pesan chat baru',
                "{$sender->name} mengirim pesan baru pada percakapan '{$subject}'.\nPreview: {$messagePreview}",
                route('chat.show', $conversation),
                'Buka Chat',
                'Notifikasi ini dikirim karena ada pesan baru yang belum dibaca.'
            );
        } elseif ($sender->isClient()) {
            // Fallback when conversation has no assigned staff yet.
            SystemEmailNotifier::sendToAddress(
                (string) \App\Helpers\SettingsHelper::get('admin_email'),
                'Pesan Chat Baru dari Client: ' . $subject,
                'Ada pesan baru yang membutuhkan tindak lanjut',
                "{$sender->name} mengirim pesan pada percakapan '{$subject}', tetapi belum ada petugas yang terpasang.\nPreview: {$messagePreview}",
                route('chat.show', $conversation),
                'Buka Chat',
                'Admin',
                'Silakan assign petugas untuk percakapan ini.'
            );
        }

        if ($request->ajax() || $request->wantsJson()) {
            $chat->load('user');

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $chat->id,
                    'message' => $chat->message,
                    'user_id' => $chat->user_id,
                    'user_name' => $chat->user->name,
                    'avatar_url' => $chat->user->avatar_url,
                    'is_sent' => true,
                    'created_at' => $chat->created_at->format('H:i'),
                    'created_at_full' => $chat->created_at->format('d M Y H:i'),
                    'has_file' => $chat->hasFile(),
                    'file_url' => $chat->hasFile() ? $chat->getFileUrl() : null,
                ],
            ]);
        }

        return back()->with('success', 'Pesan berhasil dikirim.');
    }

    public function uploadFile(Request $request, ChatConversation $conversation)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'message' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $filePath = $file->store('chat-files', 'public');

        Chat::create([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
            'message' => $request->message ?? 'Sent a file: ' . $file->getClientOriginalName(),
            'file_path' => $filePath,
            'message_type' => 'file',
            'is_read' => false,
        ]);

        return back()->with('success', 'Berkas berhasil diunggah.');
    }

    public function markAsRead(ChatConversation $conversation)
    {
        $conversation->markAllAsRead(auth()->id());

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    public function close(ChatConversation $conversation)
    {
        $conversation->close();

        ActivityLog::log('close_conversation', 'Closed conversation', $conversation);

        return back()->with('success', 'Percakapan ditutup.');
    }

    public function reopen(ChatConversation $conversation)
    {
        $conversation->reopen();

        ActivityLog::log('reopen_conversation', 'Reopened conversation', $conversation);

        return back()->with('success', 'Percakapan dibuka kembali.');
    }

    public function getMessages(Request $request, ChatConversation $conversation)
    {
        $user = auth()->user();
        
        // Authorization check
        if ($user->isClient() && $conversation->client_id !== $user->id) {
            abort(403);
        }
        if ($user->isDeveloper() && $conversation->developer_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'since_id' => 'nullable|integer|min:1',
        ]);

        $query = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc');

        if ($request->filled('since_id')) {
            $query->where('id', '>', (int) $request->since_id);
        }

        $messages = $query->get();

        $conversation->markAllAsRead($user->id);
        $latestMessageId = (int) ($conversation->messages()->max('id') ?? 0);

        return response()->json([
            'messages' => $messages->map(function($msg) use ($user) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'user_id' => $msg->user_id,
                    'user_name' => $msg->user->name,
                    'avatar_url' => $msg->user->avatar_url,
                    'is_sent' => $msg->user_id === $user->id,
                    'created_at' => $msg->created_at->format('H:i'),
                    'created_at_full' => $msg->created_at->format('d M Y H:i'),
                    'has_file' => $msg->hasFile(),
                    'file_url' => $msg->hasFile() ? $msg->getFileUrl() : null,
                    'file_name' => $msg->hasFile() ? basename($msg->file_path) : null,
                    'is_read' => $msg->is_read,
                ];
            }),
            'unread_count' => $conversation->getUnreadMessagesCount($user->id),
            'latest_message_id' => $latestMessageId,
            'conversation_status' => $conversation->status,
        ]);
    }
}
