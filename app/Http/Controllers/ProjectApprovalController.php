<?php

namespace App\Http\Controllers;

use App\Models\ProjectRequest;
use App\Models\ProjectApproval;
use App\Models\ProjectRevision;
use App\Models\Queue;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\SystemEmailNotifier;
use Illuminate\Http\Request;

class ProjectApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->canApproveProjects()) {
            $query = ProjectApproval::with(['projectRequest.client', 'projectRequest.requirements', 'projectRequest.manager'])
                ->pending()
                ->latest();

            if ($user->isManager()) {
                $query->where(function ($q) use ($user) {
                    $q->where('approver_id', $user->id)
                      ->orWhereHas('projectRequest', function ($pq) use ($user) {
                          $pq->where('manager_id', $user->id);
                      });
                });
            }

            $pendingApprovals = $query->paginate(10);

            return view('approvals.index', compact('pendingApprovals'));
        }

        abort(403);
    }

    public function show(ProjectApproval $approval)
    {
        $user = auth()->user();

        if (!$user->canApproveProjects()) {
            abort(403);
        }

        // Managers can only view their own approvals unless Super Admin
        if ($user->isManager() && !$user->isSuperAdmin()) {
            if ($approval->approver_id !== $user->id && $approval->projectRequest->manager_id !== $user->id) {
                abort(403, 'Anda tidak berwenang meninjau persetujuan ini.');
            }
        }

        $approval->load([
            'projectRequest.client',
            'projectRequest.requirements',
            'projectRequest.revisions',
            'projectRequest.manager',
            'approver'
        ]);

        $developers = \App\Models\User::whereIn('role', ['developer', 'admin', 'super_admin'])
            ->where('status', 'active')
            ->withCount(['assignedQueues' => function ($q) {
                $q->whereIn('status', ['Pending', 'In Progress']);
            }])
            ->orderBy('name')
            ->get();

        $isManagerTier = ($approval->projectRequest->status === 'waiting_manager_approval') || 
                         ($user->isManager() && $approval->projectRequest->manager_approval_status === 'pending');

        return view('approvals.show', compact('approval', 'developers', 'isManagerTier'));
    }

    public function approve(Request $request, ProjectApproval $approval)
    {
        $user = auth()->user();

        if (!$user->canApproveProjects()) {
            abort(403);
        }

        $projectRequest = $approval->projectRequest;
        $ticketCode = $projectRequest->ticket_number ?? ('#' . $projectRequest->id);

        $isManagerTier = ($projectRequest->status === 'waiting_manager_approval') || 
                         ($user->isManager() && $projectRequest->manager_approval_status === 'pending');

        // TIER 1: Approval oleh Manager (Operational Manager / General Manager)
        if ($isManagerTier) {
            $request->validate([
                'comments' => 'nullable|string',
            ]);

            $approval->update([
                'status' => 'approved',
                'comments' => $request->comments,
                'reviewed_at' => now(),
            ]);

            $projectRequest->update([
                'manager_approval_status' => 'approved',
                'manager_approved_at' => now(),
                'manager_notes' => $request->comments,
                'status' => 'submitted', // Diteruskan ke Admin IT
                'ticket_status' => 'open',
            ]);

            // Buat approval record untuk tahap 2 (Admin IT)
            $adminApprover = User::whereIn('role', ['admin', 'super_admin'])
                ->where('status', 'active')
                ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
                ->first();

            if ($adminApprover) {
                $itApproval = $projectRequest->approvals()->create([
                    'approver_id' => $adminApprover->id,
                    'status' => 'pending',
                ]);

                // Notifikasi ke Admin IT
                SystemEmailNotifier::sendToUser(
                    $adminApprover,
                    'Tiket Disetujui Atasan (Siap Triage IT): ' . $ticketCode,
                    'Tiket baru telah disetujui atasan dan siap ditriage',
                    "Tiket {$ticketCode} ({$projectRequest->project_name}) telah disetujui oleh {$user->role_display_name} ({$user->name}).\nSilakan tentukan teknisi dan jadwal pengerjaan.",
                    route('approvals.show', $itApproval),
                    'Triage Tiket IT',
                    'Tiket sudah melewati persetujuan manajerial.'
                );
            }

            // Notifikasi ke Client
            SystemEmailNotifier::sendToUser(
                $projectRequest->client,
                'Tiket Disetujui Atasan: ' . $ticketCode,
                'Pengajuan tiket Anda telah disetujui oleh Atasan',
                "Tiket {$ticketCode} ({$projectRequest->project_name}) telah disetujui oleh {$user->name} ({$user->role_display_name}) dan sedang diteruskan ke Tim IT.",
                route('project-requests.show', $projectRequest),
                'Lihat Detail Tiket',
                'Menunggu penugasan teknisi oleh Tim IT.'
            );

            ActivityLog::log('manager_approve_project', "Atasan ({$user->role_display_name}) menyetujui tiket: {$projectRequest->project_name}", $projectRequest);

            return redirect()->route('approvals.index')
                ->with('success', "Tiket berhasil disetujui oleh {$user->role_display_name} dan diteruskan ke Tim IT.");
        }

        // TIER 2: Approval oleh Admin IT / Super Admin (Convert ke Queue)
        $request->validate([
            'comments' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $approval->approve($request->comments);

        // Convert to queue
        $queue = Queue::create([
            'project_name' => $projectRequest->project_name,
            'description' => $projectRequest->description,
            'client_name' => $projectRequest->client->name,
            'client_email' => $projectRequest->client->email,
            'client_phone' => $projectRequest->client->phone,
            'client_company' => $projectRequest->client->company,
            'priority' => $this->mapTicketPriority($projectRequest->impact, $projectRequest->urgency),
            'status' => 'Pending',
            'assigned_to' => $request->assigned_to,
            'start_date' => now(),
            'deadline' => now()->addDays($projectRequest->estimated_duration ?? 30),
            'progress' => 0,
            'notes' => 'Converted from ticket ' . ($projectRequest->ticket_number ?? ('#' . $projectRequest->id)),
        ]);

        $projectRequest->update([
            'queue_id' => $queue->id,
            'developer_id' => $request->assigned_to ?? $projectRequest->developer_id,
            'status' => 'converted_to_queue',
            'ticket_status' => 'in_progress',
            'first_responded_at' => $projectRequest->first_responded_at ?? now(),
        ]);

        ActivityLog::log('approve_project', 'Approved project request: ' . $projectRequest->project_name, $projectRequest);

        if ($request->assigned_to) {
            $developer = User::find($request->assigned_to);
            if ($developer) {
                SystemEmailNotifier::sendToUser(
                    $developer,
                    'Penugasan Tiket Baru: ' . $ticketCode,
                    'Anda ditugaskan menangani tiket baru',
                    "Tiket {$ticketCode} ({$projectRequest->project_name}) telah disetujui dan ditugaskan kepada Anda.\nSilakan periksa detail dan perbarui progres pengerjaan.",
                    route('progress.show', $queue),
                    'Buka Antrean Pengerjaan',
                    'Tiket siap dikerjakan.'
                );
            }
        }

        SystemEmailNotifier::sendToUser(
            $projectRequest->client,
            'Tiket Disetujui IT: ' . $ticketCode,
            'Pengajuan tiket Anda telah disetujui oleh Tim IT',
            "Tiket {$ticketCode} ({$projectRequest->project_name}) sudah disetujui dan dipindahkan ke antrian pengerjaan.",
            route('project-requests.show', $projectRequest),
            'Lihat Detail Tiket',
            'Pantau progres tiket melalui dashboard Anda.'
        );

        return redirect()->route('approvals.index')
            ->with('success', 'Permintaan proyek disetujui dan ditambahkan ke antrean teknisi.');
    }

    public function reject(Request $request, ProjectApproval $approval)
    {
        $user = auth()->user();

        if (!$user->canApproveProjects()) {
            abort(403);
        }

        $request->validate([
            'comments' => 'required|string',
        ]);

        $projectRequest = $approval->projectRequest;
        $ticketCode = $projectRequest->ticket_number ?? ('#' . $projectRequest->id);

        $approval->reject($request->comments);

        $updateData = [
            'ticket_status' => 'cancelled',
            'status' => 'rejected',
            'rejection_reason' => $request->comments,
            'closed_at' => now(),
        ];

        if ($projectRequest->status === 'waiting_manager_approval' || $user->isManager()) {
            $updateData['manager_approval_status'] = 'rejected';
            $updateData['manager_notes'] = $request->comments;
        }

        $projectRequest->update($updateData);

        ActivityLog::log('reject_project', "Rejected project request ({$user->role_display_name}): " . $projectRequest->project_name, $projectRequest);

        SystemEmailNotifier::sendToUser(
            $projectRequest->client,
            'Tiket Ditolak: ' . $ticketCode,
            'Pengajuan tiket Anda ditolak',
            "Tiket {$ticketCode} ({$projectRequest->project_name}) ditolak oleh {$user->name} ({$user->role_display_name}).\nAlasan: {$request->comments}",
            route('project-requests.show', $projectRequest),
            'Lihat Detail Tiket',
            'Silakan perbarui data dan ajukan ulang jika diperlukan.'
        );

        return redirect()->route('approvals.index')
            ->with('success', 'Permintaan proyek ditolak.');
    }

    public function requestRevision(Request $request, ProjectApproval $approval)
    {
        $user = auth()->user();

        if (!$user->canApproveProjects()) {
            abort(403);
        }

        $request->validate([
            'revision_notes' => 'required|string',
        ]);

        $projectRequest = $approval->projectRequest;
        $ticketCode = $projectRequest->ticket_number ?? ('#' . $projectRequest->id);

        $approval->requestRevision($request->revision_notes);

        $updateData = [
            'ticket_status' => 'pending_user',
            'status' => 'revision_requested',
        ];

        if ($projectRequest->status === 'waiting_manager_approval' || $user->isManager()) {
            $updateData['manager_approval_status'] = 'revision_requested';
            $updateData['manager_notes'] = $request->revision_notes;
        }

        $projectRequest->update($updateData);

        // Create revision record
        $revisionNumber = $projectRequest->revisions()->count() + 1;
        
        ProjectRevision::create([
            'project_request_id' => $projectRequest->id,
            'revision_number' => $revisionNumber,
            'requested_changes' => $request->revision_notes,
            'status' => 'pending',
            'requested_by' => auth()->id(),
        ]);

        ActivityLog::log('request_revision', "Requested revision for project ({$user->role_display_name}): " . $projectRequest->project_name, $projectRequest);

        SystemEmailNotifier::sendToUser(
            $projectRequest->client,
            'Revisi Diperlukan: ' . $ticketCode,
            'Tiket Anda membutuhkan revisi',
            "Tim / Atasan ({$user->name}) meminta revisi untuk tiket {$ticketCode} ({$projectRequest->project_name}).\nCatatan revisi: {$request->revision_notes}",
            route('project-requests.edit', $projectRequest),
            'Perbarui Tiket',
            'Setelah revisi disimpan, silakan submit kembali tiket.'
        );

        return redirect()->route('approvals.index')
            ->with('success', 'Permintaan revisi berhasil dikirim. Pemohon akan menerima notifikasi.');
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
