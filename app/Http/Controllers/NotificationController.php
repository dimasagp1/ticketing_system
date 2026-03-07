<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use App\Models\ProjectApproval;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get notification counts for current user
     */
    public function getCounts()
    {
        $user = Auth::user();
        
        $counts = [
            'unread_messages' => 0,
            'pending_approvals' => 0,
            'new_activities' => 0,
            'total' => 0,
        ];

        // Count unread messages
        if ($user->isClient()) {
            $conversations = $user->clientConversations()->where('status', 'active')->get();
        } elseif ($user->isDeveloper()) {
            $conversations = $user->developerConversations()->where('status', 'active')->get();
        } else {
            $conversations = ChatConversation::where('status', 'active')->get();
        }

        foreach ($conversations as $conversation) {
            $counts['unread_messages'] += $conversation->getUnreadMessagesCount($user->id);
        }

        // Count pending approvals (for admins)
        if ($user->canApproveProjects()) {
            $counts['pending_approvals'] = ProjectApproval::pending()->count();
        }

        // Count new activities (last 24 hours)
        if ($user->isSuperAdmin()) {
            $counts['new_activities'] = ActivityLog::where('created_at', '>=', now()->subDay())->count();
        }

        // Total notifications
        $counts['total'] = $counts['unread_messages'] + $counts['pending_approvals'];

        return response()->json($counts);
    }

    /**
     * Get detailed notifications
     */
    public function getNotifications()
    {
        $user = Auth::user();
        $notifications = [];

        // Unread messages
        if ($user->isClient()) {
            $conversations = $user->clientConversations()
                ->where('status', 'active')
                ->with('developer')
                ->get();
        } elseif ($user->isDeveloper()) {
            $conversations = $user->developerConversations()
                ->where('status', 'active')
                ->with('client')
                ->get();
        } else {
            $conversations = ChatConversation::where('status', 'active')
                ->with(['client', 'developer'])
                ->get();
        }

        foreach ($conversations as $conversation) {
            $unreadCount = $conversation->getUnreadMessagesCount($user->id);
            if ($unreadCount > 0) {
                $from = $user->isClient() 
                    ? ($conversation->developer ? $conversation->developer->name : 'Developer')
                    : $conversation->client->name;

                $notifications[] = [
                    'type' => 'message',
                    'id' => $conversation->id,
                    'title' => 'New message from ' . $from,
                    'message' => $conversation->subject,
                    'count' => $unreadCount,
                    'url' => route('chat.show', $conversation),
                    'time' => $conversation->last_message_at->diffForHumans(),
                    'icon' => 'fas fa-comment',
                    'color' => 'primary',
                ];
            }
        }

        // Pending approvals
        if ($user->canApproveProjects()) {
            $pendingApprovals = ProjectApproval::with('projectRequest.client')
                ->pending()
                ->latest()
                ->take(5)
                ->get();

            foreach ($pendingApprovals as $approval) {
                $notifications[] = [
                    'type' => 'approval',
                    'title' => 'Approval needed',
                    'message' => $approval->projectRequest->project_name . ' by ' . $approval->projectRequest->client->name,
                    'count' => 1,
                    'url' => route('approvals.show', $approval),
                    'time' => $approval->created_at->diffForHumans(),
                    'icon' => 'fas fa-check-circle',
                    'color' => 'warning',
                ];
            }
        }

        return response()->json($notifications);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request)
    {
        $type = $request->input('type');
        $id = $request->input('id');

        if ($type === 'message') {
            $conversation = ChatConversation::find($id);
            if ($conversation) {
                $conversation->markAllAsRead(Auth::id());
            }
        }

        return response()->json(['success' => true]);
    }
}
