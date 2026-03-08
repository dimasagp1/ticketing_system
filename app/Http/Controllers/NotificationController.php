<?php

namespace App\Http\Controllers;

use App\Helpers\SettingsHelper;
use App\Models\ActivityLog;
use App\Models\ChatConversation;
use App\Models\NotificationRead;
use App\Models\ProjectApproval;
use App\Models\ProjectProgressLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    /**
     * Get notification counts for current user
     */
    public function getCounts()
    {
        $user = Auth::user();
        $windowStart = $this->getNotificationWindowStart();

        $counts = [
            'unread_messages' => 0,
            'pending_approvals' => 0,
            'project_updates' => 0,
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

        // Count unread pending approvals (for admins)
        if ($user->canApproveProjects()) {
            $counts['pending_approvals'] = ProjectApproval::pending()
                ->whereNotIn('id', function ($query) use ($user) {
                    $query->select('reference_id')
                        ->from('notification_reads')
                        ->where('user_id', $user->id)
                        ->where('notification_type', 'approval_pending')
                        ->where('reference_type', ProjectApproval::class);
                })
                ->count();
        }

        // Count project updates for clients (approval results + progress updates)
        if ($user->isClient()) {
            $approvalUpdates = ProjectApproval::whereHas('projectRequest', function ($query) use ($user) {
                $query->where('client_id', $user->id);
            })
                ->whereIn('status', ['approved', 'rejected', 'revision_requested'])
                ->whereNotIn('id', function ($query) use ($user) {
                    $query->select('reference_id')
                        ->from('notification_reads')
                        ->where('user_id', $user->id)
                        ->where('notification_type', 'approval_result')
                        ->where('reference_type', ProjectApproval::class);
                })
                ->where(function ($query) use ($windowStart) {
                    $query->whereNotNull('reviewed_at')
                        ->where('reviewed_at', '>=', $windowStart)
                        ->orWhere(function ($inner) use ($windowStart) {
                            $inner->whereNull('reviewed_at')
                                ->where('updated_at', '>=', $windowStart);
                        });
                })
                ->count();

            $progressUpdates = ProjectProgressLog::whereHas('queue.projectRequest', function ($query) use ($user) {
                $query->where('client_id', $user->id);
            })
                ->where('created_at', '>=', $windowStart)
                ->whereNotIn('id', function ($query) use ($user) {
                    $query->select('reference_id')
                        ->from('notification_reads')
                        ->where('user_id', $user->id)
                        ->where('notification_type', 'progress_update')
                        ->where('reference_type', ProjectProgressLog::class);
                })
                ->count();

            $counts['project_updates'] = $approvalUpdates + $progressUpdates;
        }

        // Count new activities (last 24 hours)
        if ($user->isSuperAdmin()) {
            $counts['new_activities'] = ActivityLog::where('created_at', '>=', now()->subDay())->count();
        }

        $counts['total'] = $counts['unread_messages'] + $counts['pending_approvals'] + $counts['project_updates'];

        return response()->json($counts);
    }

    /**
     * Get detailed notifications
     */
    public function getNotifications()
    {
        $user = Auth::user();
        $windowStart = $this->getNotificationWindowStart();
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
            if ($unreadCount <= 0) {
                continue;
            }

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
                'sort_at' => $conversation->last_message_at?->timestamp ?? now()->timestamp,
                'icon' => 'fas fa-comment',
                'color' => 'primary',
            ];
        }

        // Unread pending approvals
        if ($user->canApproveProjects()) {
            $pendingApprovals = ProjectApproval::with('projectRequest.client')
                ->pending()
                ->whereNotIn('id', function ($query) use ($user) {
                    $query->select('reference_id')
                        ->from('notification_reads')
                        ->where('user_id', $user->id)
                        ->where('notification_type', 'approval_pending')
                        ->where('reference_type', ProjectApproval::class);
                })
                ->latest()
                ->take(5)
                ->get();

            foreach ($pendingApprovals as $approval) {
                $notifications[] = [
                    'type' => 'approval',
                    'id' => $approval->id,
                    'title' => 'Approval needed',
                    'message' => $approval->projectRequest->project_name . ' by ' . $approval->projectRequest->client->name,
                    'count' => 1,
                    'url' => route('approvals.show', $approval),
                    'time' => $approval->created_at->diffForHumans(),
                    'sort_at' => $approval->created_at?->timestamp ?? now()->timestamp,
                    'icon' => 'fas fa-check-circle',
                    'color' => 'warning',
                ];
            }
        }

        // Unread project updates for clients
        if ($user->isClient()) {
            $approvalUpdates = ProjectApproval::with('projectRequest')
                ->whereHas('projectRequest', function ($query) use ($user) {
                    $query->where('client_id', $user->id);
                })
                ->whereIn('status', ['approved', 'rejected', 'revision_requested'])
                ->whereNotIn('id', function ($query) use ($user) {
                    $query->select('reference_id')
                        ->from('notification_reads')
                        ->where('user_id', $user->id)
                        ->where('notification_type', 'approval_result')
                        ->where('reference_type', ProjectApproval::class);
                })
                ->where(function ($query) use ($windowStart) {
                    $query->whereNotNull('reviewed_at')
                        ->where('reviewed_at', '>=', $windowStart)
                        ->orWhere(function ($inner) use ($windowStart) {
                            $inner->whereNull('reviewed_at')
                                ->where('updated_at', '>=', $windowStart);
                        });
                })
                ->latest('reviewed_at')
                ->latest('updated_at')
                ->take(5)
                ->get();

            foreach ($approvalUpdates as $approvalUpdate) {
                $projectName = optional($approvalUpdate->projectRequest)->project_name ?? 'Project';

                $statusConfig = match ($approvalUpdate->status) {
                    'approved' => ['title' => 'Project approved', 'icon' => 'fas fa-check-circle', 'color' => 'success'],
                    'rejected' => ['title' => 'Project rejected', 'icon' => 'fas fa-times-circle', 'color' => 'danger'],
                    default => ['title' => 'Revision requested', 'icon' => 'fas fa-edit', 'color' => 'warning'],
                };

                $notifications[] = [
                    'type' => 'approval_result',
                    'id' => $approvalUpdate->id,
                    'title' => $statusConfig['title'],
                    'message' => $projectName,
                    'count' => 1,
                    'url' => route('project-requests.show', $approvalUpdate->project_request_id),
                    'time' => ($approvalUpdate->reviewed_at ?? $approvalUpdate->updated_at)->diffForHumans(),
                    'sort_at' => ($approvalUpdate->reviewed_at ?? $approvalUpdate->updated_at)?->timestamp ?? now()->timestamp,
                    'icon' => $statusConfig['icon'],
                    'color' => $statusConfig['color'],
                ];
            }

            $progressUpdates = ProjectProgressLog::with(['queue.projectRequest'])
                ->whereHas('queue.projectRequest', function ($query) use ($user) {
                    $query->where('client_id', $user->id);
                })
                ->where('created_at', '>=', $windowStart)
                ->whereNotIn('id', function ($query) use ($user) {
                    $query->select('reference_id')
                        ->from('notification_reads')
                        ->where('user_id', $user->id)
                        ->where('notification_type', 'progress_update')
                        ->where('reference_type', ProjectProgressLog::class);
                })
                ->latest()
                ->take(5)
                ->get();

            foreach ($progressUpdates as $progressUpdate) {
                $projectRequest = optional($progressUpdate->queue)->projectRequest;
                if (! $projectRequest) {
                    continue;
                }

                $notifications[] = [
                    'type' => 'progress_update',
                    'id' => $progressUpdate->id,
                    'title' => 'Progress update: ' . $projectRequest->project_name,
                    'message' => Str::limit($progressUpdate->activity_description, 80),
                    'count' => 1,
                    'url' => route('project-requests.show', $projectRequest),
                    'time' => $progressUpdate->created_at->diffForHumans(),
                    'sort_at' => $progressUpdate->created_at?->timestamp ?? now()->timestamp,
                    'icon' => 'fas fa-tasks',
                    'color' => 'info',
                ];
            }
        }

        usort($notifications, function ($a, $b) {
            return ($b['sort_at'] ?? 0) <=> ($a['sort_at'] ?? 0);
        });

        $notifications = array_slice($notifications, 0, 10);
        $notifications = array_map(function ($notification) {
            unset($notification['sort_at']);

            return $notification;
        }, $notifications);

        return response()->json($notifications);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request)
    {
        $type = $request->input('type');
        $id = (int) $request->input('id');

        if (! $type || $id <= 0) {
            return response()->json(['success' => false, 'message' => 'Invalid notification payload.'], 422);
        }

        if ($type === 'message') {
            $conversation = ChatConversation::find($id);
            if ($conversation) {
                $conversation->markAllAsRead(Auth::id());
            }

            return response()->json(['success' => true]);
        }

        $mapping = [
            'approval' => ['notification_type' => 'approval_pending', 'reference_type' => ProjectApproval::class],
            'approval_result' => ['notification_type' => 'approval_result', 'reference_type' => ProjectApproval::class],
            'progress_update' => ['notification_type' => 'progress_update', 'reference_type' => ProjectProgressLog::class],
            // Backward compatibility if old frontend still sends this type.
            'project_update' => ['notification_type' => 'progress_update', 'reference_type' => ProjectProgressLog::class],
        ];

        if (! isset($mapping[$type])) {
            return response()->json(['success' => true]);
        }

        NotificationRead::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'notification_type' => $mapping[$type]['notification_type'],
                'reference_type' => $mapping[$type]['reference_type'],
                'reference_id' => $id,
            ],
            [
                'read_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    private function getNotificationWindowStart()
    {
        $days = (int) SettingsHelper::get('notification_window_days', 3);
        $days = max(1, min(30, $days));

        return now()->subDays($days);
    }
}
