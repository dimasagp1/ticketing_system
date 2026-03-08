<?php

namespace App\Http\Controllers;

use App\Models\ProjectRequest;
use App\Models\ProjectApproval;
use App\Models\ProjectRevision;
use App\Models\Queue;
use App\Models\ActivityLog;
use App\Services\SystemEmailNotifier;
use Illuminate\Http\Request;

class ProjectApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->canApproveProjects()) {
            $pendingApprovals = ProjectApproval::with(['projectRequest.client', 'projectRequest.requirements'])
                ->pending()
                ->latest()
                ->paginate(10);

            return view('approvals.index', compact('pendingApprovals'));
        }

        abort(403);
    }

    public function show(ProjectApproval $approval)
    {
        if (!auth()->user()->canApproveProjects()) {
            abort(403);
        }

        $approval->load([
            'projectRequest.client',
            'projectRequest.requirements',
            'projectRequest.revisions',
            'approver'
        ]);

        return view('approvals.show', compact('approval'));
    }

    public function approve(Request $request, ProjectApproval $approval)
    {
        if (!auth()->user()->canApproveProjects()) {
            abort(403);
        }

        $request->validate([
            'comments' => 'nullable|string',
        ]);

        $approval->approve($request->comments);

        // Convert to queue
        $projectRequest = $approval->projectRequest;
        $queue = Queue::create([
            'project_name' => $projectRequest->project_name,
            'description' => $projectRequest->description,
            'client_name' => $projectRequest->client->name,
            'client_email' => $projectRequest->client->email,
            'client_phone' => $projectRequest->client->phone,
            'client_company' => $projectRequest->client->company,
            'priority' => $this->mapTicketPriority($projectRequest->impact, $projectRequest->urgency),
            'status' => 'Pending',
            'assigned_to' => null,
            'start_date' => now(),
            'deadline' => now()->addDays($projectRequest->estimated_duration ?? 30),
            'progress' => 0,
            'notes' => 'Converted from ticket ' . ($projectRequest->ticket_number ?? ('#' . $projectRequest->id)),
        ]);

        $projectRequest->update([
            'queue_id' => $queue->id,
            'status' => 'converted_to_queue',
            'ticket_status' => 'in_progress',
            'first_responded_at' => $projectRequest->first_responded_at ?? now(),
        ]);

        ActivityLog::log('approve_project', 'Approved project request: ' . $projectRequest->project_name, $projectRequest);

        $ticketCode = $projectRequest->ticket_number ?? ('#' . $projectRequest->id);
        SystemEmailNotifier::sendToUser(
            $projectRequest->client,
            'Tiket Disetujui: ' . $ticketCode,
            'Pengajuan tiket Anda telah disetujui',
            "Tiket {$ticketCode} ({$projectRequest->project_name}) sudah disetujui dan dipindahkan ke antrian pengerjaan.",
            route('project-requests.show', $projectRequest),
            'Lihat Detail Tiket',
            'Pantau progres tiket melalui dashboard Anda.'
        );

        return redirect()->route('approvals.index')
            ->with('success', 'Project request approved and added to queue!');
    }

    public function reject(Request $request, ProjectApproval $approval)
    {
        if (!auth()->user()->canApproveProjects()) {
            abort(403);
        }

        $request->validate([
            'comments' => 'required|string',
        ]);

        $approval->reject($request->comments);

        $approval->projectRequest->update([
            'ticket_status' => 'cancelled',
            'closed_at' => now(),
        ]);

        ActivityLog::log('reject_project', 'Rejected project request: ' . $approval->projectRequest->project_name, $approval->projectRequest);

        $projectRequest = $approval->projectRequest;
        $ticketCode = $projectRequest->ticket_number ?? ('#' . $projectRequest->id);
        SystemEmailNotifier::sendToUser(
            $projectRequest->client,
            'Tiket Ditolak: ' . $ticketCode,
            'Pengajuan tiket Anda ditolak',
            "Tiket {$ticketCode} ({$projectRequest->project_name}) ditolak.\nAlasan: {$request->comments}",
            route('project-requests.show', $projectRequest),
            'Lihat Detail Tiket',
            'Silakan perbarui data dan ajukan ulang jika diperlukan.'
        );

        return redirect()->route('approvals.index')
            ->with('success', 'Project request rejected.');
    }

    public function requestRevision(Request $request, ProjectApproval $approval)
    {
        if (!auth()->user()->canApproveProjects()) {
            abort(403);
        }

        $request->validate([
            'revision_notes' => 'required|string',
        ]);

        $approval->requestRevision($request->revision_notes);

        $approval->projectRequest->update([
            'ticket_status' => 'pending_user',
        ]);

        // Create revision record
        $revisionNumber = $approval->projectRequest->revisions()->count() + 1;
        
        ProjectRevision::create([
            'project_request_id' => $approval->projectRequest->id,
            'revision_number' => $revisionNumber,
            'requested_changes' => $request->revision_notes,
            'status' => 'pending',
            'requested_by' => auth()->id(),
        ]);

        ActivityLog::log('request_revision', 'Requested revision for project: ' . $approval->projectRequest->project_name, $approval->projectRequest);

        $projectRequest = $approval->projectRequest;
        $ticketCode = $projectRequest->ticket_number ?? ('#' . $projectRequest->id);
        SystemEmailNotifier::sendToUser(
            $projectRequest->client,
            'Revisi Diperlukan: ' . $ticketCode,
            'Tiket Anda membutuhkan revisi',
            "Tim meminta revisi untuk tiket {$ticketCode} ({$projectRequest->project_name}).\nCatatan revisi: {$request->revision_notes}",
            route('project-requests.edit', $projectRequest),
            'Perbarui Tiket',
            'Setelah revisi disimpan, silakan submit kembali tiket.'
        );

        return redirect()->route('approvals.index')
            ->with('success', 'Revision requested. Client will be notified.');
    }

    private function mapTicketPriority(?string $impact, ?string $urgency): string
    {
        $weights = [
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            'critical' => 4,
        ];

        $score = ($weights[$impact ?? 'medium'] ?? 2) + ($weights[$urgency ?? 'medium'] ?? 2);

        return match (true) {
            $score >= 7 => 'High',
            $score >= 4 => 'Medium',
            default => 'Low',
        };
    }
}
