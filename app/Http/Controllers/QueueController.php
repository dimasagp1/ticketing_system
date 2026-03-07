<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function index(Request $request)
    {
        $query = Queue::with(['assignedTo', 'projectRequest', 'progressLogs']);

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
                        ->whereIn('ticket_status', ['open', 'in_progress', 'pending_user']);
                } elseif ($request->sla_filter === 'today') {
                    $requestQuery->whereDate('sla_resolution_due_at', now()->toDateString())
                        ->whereIn('ticket_status', ['open', 'in_progress', 'pending_user']);
                } elseif ($request->sla_filter === 'at_risk_24h') {
                    $requestQuery->whereBetween('sla_resolution_due_at', [now(), now()->copy()->addHours(24)])
                        ->whereIn('ticket_status', ['open', 'in_progress', 'pending_user']);
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
            
        return view('queues.index', compact('queues'));
    }
}
