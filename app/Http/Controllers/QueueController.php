<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\ProjectRequest;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Queue::with(['assignedTo', 'projectRequest', 'progressLogs']);

        if ($user->isDeveloper()) {
            $query->where('assigned_to', $user->id);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($builder) use ($search) {
                $builder->where('project_name', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('client_email', 'like', "%{$search}%")
                    ->orWhereHas('projectRequest', function ($requestQuery) use ($search) {
                        $requestQuery->where('ticket_number', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('queue_status')) {
            $query->where('status', $request->queue_status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned')) {
            if ($request->assigned === 'unassigned') {
                $query->whereNull('assigned_to');
            } elseif ($request->assigned === 'assigned') {
                $query->whereNotNull('assigned_to');
            }
        }

        if ($request->filled('ticket_status')) {
            $query->whereHas('projectRequest', function ($requestQuery) use ($request) {
                $requestQuery->where('ticket_status', $request->ticket_status);
            });
        }

        if ($request->filled('sla_filter')) {
            $query->whereHas('projectRequest', function ($requestQuery) use ($request) {
                $requestQuery->whereNotNull('sla_resolution_due_at');

                if ($request->sla_filter === 'overdue') {
                    $requestQuery->where('sla_resolution_due_at', '<', now())
                        ->whereIn('ticket_status', ProjectRequest::slaTrackedTicketStatuses());
                } elseif ($request->sla_filter === 'today') {
                    $requestQuery->whereDate('sla_resolution_due_at', now()->toDateString())
                        ->whereIn('ticket_status', ProjectRequest::slaTrackedTicketStatuses());
                } elseif ($request->sla_filter === 'at_risk_24h') {
                    $requestQuery->whereBetween('sla_resolution_due_at', [now(), now()->copy()->addHours(24)])
                        ->whereIn('ticket_status', ProjectRequest::slaTrackedTicketStatuses());
                }
            });
        }

        if ($request->input('sort') === 'sla_asc') {
            $query->leftJoin('project_requests', 'project_requests.queue_id', '=', 'queues.id')
                ->select('queues.*')
                ->orderByRaw('CASE WHEN project_requests.sla_resolution_due_at IS NULL THEN 1 ELSE 0 END')
                ->orderBy('project_requests.sla_resolution_due_at');
        } else {
            $query->orderByRaw("FIELD(status, 'In Progress', 'Pending', 'On Hold', 'Completed', 'Cancelled')")
                ->latest();
        }

        $queues = $query->paginate(15)->appends($request->query());

        $developers = \App\Models\User::whereIn('role', ['developer', 'admin', 'super_admin'])
            ->where('status', 'active')
            ->withCount(['assignedQueues' => function ($q) {
                $q->whereIn('status', ['Pending', 'In Progress']);
            }])
            ->orderBy('name')
            ->get();

        return view('queues.index', compact('queues', 'developers'));
    }

    public function assignDeveloper(Request $request, Queue $queue)
    {
        if (!auth()->user()->canApproveProjects()) {
            abort(403);
        }

        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $queue->update([
            'assigned_to' => $request->assigned_to,
        ]);

        $developer = $request->assigned_to ? \App\Models\User::find($request->assigned_to) : null;
        $devName = $developer ? $developer->name : 'Belum Ditugaskan';

        \App\Models\ActivityLog::log('assign_developer', "Penugasan antrian '{$queue->project_name}' diubah ke {$devName}", $queue->projectRequest);

        if ($developer) {
            \App\Services\SystemEmailNotifier::sendToUser(
                $developer,
                'Penugasan Tiket Baru: ' . $queue->project_name,
                'Anda mendapat penugasan tiket baru',
                "Anda ditugaskan untuk menangani proyek/tiket '{$queue->project_name}' dari klien {$queue->client_name}.",
                route('queues.index'),
                'Lihat Antrian Saya',
                'Silakan periksa detail tiket di antrian Anda.'
            );
        }

        return redirect()->back()->with('success', "Penugasan developer untuk '{$queue->project_name}' berhasil diperbarui ({$devName}).");
    }
}
