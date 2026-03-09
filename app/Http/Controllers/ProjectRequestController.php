<?php

namespace App\Http\Controllers;

use App\Models\ProjectRequest;
use App\Models\ProjectRequirement;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\SystemEmailNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isClient()) {
            $query = ProjectRequest::where('client_id', $user->id)
                ->with(['requirements', 'approvals']);
        } else {
            $query = ProjectRequest::with(['client', 'requirements', 'approvals']);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($builder) use ($search) {
                $builder->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('project_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('ticket_status')) {
            $query->where('ticket_status', $request->ticket_status);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('ticket_category')) {
            $query->where('ticket_category', $request->ticket_category);
        }

        if ($request->filled('impact')) {
            $query->where('impact', $request->impact);
        }

        if ($request->filled('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->filled('sla_filter')) {
            $query->whereNotNull('sla_resolution_due_at');

            if ($request->sla_filter === 'overdue') {
                $query->where('sla_resolution_due_at', '<', now())
                    ->whereIn('ticket_status', ['open', 'in_progress', 'pending_user']);
            } elseif ($request->sla_filter === 'today') {
                $query->whereDate('sla_resolution_due_at', now()->toDateString())
                    ->whereIn('ticket_status', ['open', 'in_progress', 'pending_user']);
            } elseif ($request->sla_filter === 'at_risk_24h') {
                $query->whereBetween('sla_resolution_due_at', [now(), now()->copy()->addHours(24)])
                    ->whereIn('ticket_status', ['open', 'in_progress', 'pending_user']);
            }
        }

        if ($request->input('sort') === 'sla_asc') {
            $query->orderByRaw('CASE WHEN sla_resolution_due_at IS NULL THEN 1 ELSE 0 END')
                ->orderBy('sla_resolution_due_at');
        } else {
            $query->latest();
        }

        $requests = $query->paginate(10)->appends($request->query());

        return view('project-requests.index', compact('requests'));
    }

    public function create()
    {
        return view('project-requests.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'ticket_category' => 'required|in:incident,service_request,access,bug,other',
            'description' => 'required|string',
            'estimated_duration' => 'nullable|integer|min:1',
            'impact' => 'required|in:low,medium,high,critical',
            'urgency' => 'required|in:low,medium,high,critical',
            'requirements.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        $slaTargets = $this->computeSlaTargets($validated['impact'], $validated['urgency']);

        $projectRequest = ProjectRequest::create([
            'ticket_number' => $this->generateTicketNumber(),
            'project_name' => $validated['project_name'],
            'ticket_category' => $validated['ticket_category'],
            'description' => $validated['description'],
            'estimated_duration' => $validated['estimated_duration'] ?? null,
            'client_id' => auth()->id(),
            'impact' => $validated['impact'],
            'urgency' => $validated['urgency'],
            'status' => 'draft',
            'ticket_status' => 'open',
            'sla_response_due_at' => $slaTargets['response_due_at'],
            'sla_resolution_due_at' => $slaTargets['resolution_due_at'],
        ]);

        // Handle file uploads
        if ($request->hasFile('requirements')) {
            foreach ($request->file('requirements') as $file) {
                $path = $file->store('requirements', 'public');
                
                ProjectRequirement::create([
                    'project_request_id' => $projectRequest->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'version' => 1,
                    'is_current_version' => true,
                ]);
            }
        }

        ActivityLog::logCreate($projectRequest, 'Created new project request: ' . $projectRequest->project_name);

        return redirect()->route('project-requests.show', $projectRequest)
            ->with('success', 'Project request created successfully!');
    }

    public function show(ProjectRequest $projectRequest)
    {
        // Authorization check
        if (auth()->user()->isClient() && $projectRequest->client_id !== auth()->id()) {
            abort(403);
        }

        $projectRequest->load(['client', 'requirements', 'approvals.approver', 'revisions']);

        return view('project-requests.show', compact('projectRequest'));
    }

    public function edit(ProjectRequest $projectRequest)
    {
        // Only allow editing if draft or revision requested
        if (!in_array($projectRequest->status, ['draft', 'revision_requested'])) {
            return redirect()->route('project-requests.show', $projectRequest)
                ->with('error', 'This project request cannot be edited.');
        }

        // Authorization check
        if (auth()->user()->isClient() && $projectRequest->client_id !== auth()->id()) {
            abort(403);
        }

        return view('project-requests.edit', compact('projectRequest'));
    }

    public function update(Request $request, ProjectRequest $projectRequest)
    {
        $validated = $request->validate([
            'project_name' => 'required|string|max:255',
            'ticket_category' => 'required|in:incident,service_request,access,bug,other',
            'description' => 'required|string',
            'estimated_duration' => 'nullable|integer|min:1',
            'impact' => 'required|in:low,medium,high,critical',
            'urgency' => 'required|in:low,medium,high,critical',
            'requirements.*' => 'nullable|file|max:10240',
        ]);

        $slaTargets = $this->computeSlaTargets($validated['impact'], $validated['urgency']);

        $updateData = [
            'project_name' => $validated['project_name'],
            'ticket_category' => $validated['ticket_category'],
            'description' => $validated['description'],
            'estimated_duration' => $validated['estimated_duration'] ?? null,
            'impact' => $validated['impact'],
            'urgency' => $validated['urgency'],
            'sla_response_due_at' => $projectRequest->first_responded_at ? $projectRequest->sla_response_due_at : $slaTargets['response_due_at'],
            'sla_resolution_due_at' => $projectRequest->resolved_at ? $projectRequest->sla_resolution_due_at : $slaTargets['resolution_due_at'],
        ];

        // If it was a revision request, we update status to submitted so it appears in approvals again
        if ($projectRequest->status === 'revision_requested') {
            $updateData['status'] = 'submitted';
            $updateData['submitted_at'] = now();
            $updateData['ticket_status'] = 'in_progress';
            // Also reset approval status if needed, but the approvals table handle checks
        }

        $projectRequest->update($updateData);

        // Handle new file uploads
        if ($request->hasFile('requirements')) {
            // Mark old files as not current
            $projectRequest->requirements()->update(['is_current_version' => false]);
            
            $version = $projectRequest->requirements()->max('version') + 1;

            foreach ($request->file('requirements') as $file) {
                $path = $file->store('requirements', 'public');
                
                ProjectRequirement::create([
                    'project_request_id' => $projectRequest->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'version' => $version,
                    'is_current_version' => true,
                ]);
            }
        }

        ActivityLog::logUpdate($projectRequest, 'Updated project request: ' . $projectRequest->project_name);

        return redirect()->route('project-requests.show', $projectRequest)
            ->with('success', 'Project request updated successfully!');
    }

    public function destroy(ProjectRequest $projectRequest)
    {
        // Only allow deletion if draft
        if ($projectRequest->status !== 'draft') {
            return redirect()->route('project-requests.index')
                ->with('error', 'Only draft requests can be deleted.');
        }

        // Authorization check
        if (auth()->user()->isClient() && $projectRequest->client_id !== auth()->id()) {
            abort(403);
        }

        ActivityLog::logDelete($projectRequest, 'Deleted project request: ' . $projectRequest->project_name);

        $projectRequest->delete();

        return redirect()->route('project-requests.index')
            ->with('success', 'Project request deleted successfully!');
    }

    public function submitForApproval(ProjectRequest $projectRequest)
    {
        if ($projectRequest->status !== 'draft' && $projectRequest->status !== 'revision_requested') {
            return redirect()->route('project-requests.show', $projectRequest)
                ->with('error', 'This request cannot be submitted.');
        }

        // Check if has requirements
        if ($projectRequest->requirements()->count() === 0) {
            return redirect()->route('project-requests.show', $projectRequest)
                ->with('error', 'Please upload at least one requirement file before submitting.');
        }

        $projectRequest->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'ticket_status' => 'in_progress',
            'first_responded_at' => $projectRequest->first_responded_at ?? now(),
        ]);

        $approver = User::whereIn('role', ['admin', 'super_admin'])
            ->where('status', 'active')
            ->orderByRaw("CASE WHEN role = 'admin' THEN 0 ELSE 1 END")
            ->first();

        if (!$approver) {
            $projectRequest->update([
                'status' => 'draft',
                'submitted_at' => null,
            ]);

            return redirect()->route('project-requests.show', $projectRequest)
                ->with('error', 'Tidak ada admin aktif yang tersedia untuk proses approval.');
        }

        // Create approval record
        $approval = $projectRequest->approvals()->create([
            'approver_id' => $approver->id,
            'status' => 'pending',
        ]);

        ActivityLog::log('submit_project', 'Submitted project request for approval: ' . $projectRequest->project_name, $projectRequest);

        $ticketCode = $projectRequest->ticket_number ?? ('#' . $projectRequest->id);

        SystemEmailNotifier::sendToUser(
            $approver,
            'Tiket Baru Menunggu Approval: ' . $ticketCode,
            'Tiket baru membutuhkan persetujuan Anda',
            "Tiket {$ticketCode} ({$projectRequest->project_name}) telah diajukan oleh {$projectRequest->client?->name}.\nSilakan review dan berikan keputusan approval.",
            route('approvals.show', $approval),
            'Buka Approval',
            'Email ini dikirim otomatis oleh sistem ticketing.'
        );

        SystemEmailNotifier::sendToUser(
            $projectRequest->client,
            'Pengajuan Tiket Berhasil: ' . $ticketCode,
            'Tiket Anda berhasil dikirim untuk proses approval',
            "Tiket {$ticketCode} ({$projectRequest->project_name}) sudah masuk ke antrian approval.\nKami akan mengirim pembaruan saat ada perubahan status.",
            route('project-requests.show', $projectRequest),
            'Lihat Tiket',
            'Anda menerima email ini karena notifikasi email aktif.'
        );

        return redirect()->route('project-requests.show', $projectRequest)
            ->with('success', 'Project request submitted for approval!');
    }

    public function resolveTicket(ProjectRequest $projectRequest)
    {
        $user = auth()->user();

        if (!$user->hasRole(['admin', 'super_admin'])) {
            abort(403);
        }

        if (!in_array($projectRequest->ticket_status, ['open', 'in_progress', 'pending_user'])) {
            return back()->with('error', 'Ticket hanya bisa di-resolve dari status aktif.');
        }

        if ($projectRequest->status === 'draft') {
            return back()->with('error', 'Ticket draft tidak bisa diselesaikan.');
        }

        $projectRequest->update([
            'ticket_status' => 'resolved',
            'resolved_at' => now(),
        ]);

        ActivityLog::log('resolve_ticket', 'Resolved ticket: ' . ($projectRequest->ticket_number ?? $projectRequest->project_name), $projectRequest);

        $ticketCode = $projectRequest->ticket_number ?? ('#' . $projectRequest->id);
        SystemEmailNotifier::sendToUser(
            $projectRequest->client,
            'Tiket Terselesaikan: ' . $ticketCode,
            'Tiket Anda telah diselesaikan',
            "Tiket {$ticketCode} ({$projectRequest->project_name}) sudah diselesaikan oleh tim.\nSilakan review hasilnya sebelum tiket ditutup.",
            route('project-requests.show', $projectRequest),
            'Tinjau Tiket',
            'Silakan berikan konfirmasi jika hasil sudah sesuai.'
        );

        return back()->with('success', 'Ticket berhasil di-resolve.');
    }

    public function closeTicket(ProjectRequest $projectRequest)
    {
        $user = auth()->user();

        $canClose = $user->hasRole(['admin', 'super_admin']) || ($user->isClient() && $projectRequest->client_id === $user->id);
        if (!$canClose) {
            abort(403);
        }

        if (!in_array($projectRequest->ticket_status, ['resolved', 'cancelled'])) {
            return back()->with('error', 'Ticket hanya bisa ditutup jika sudah resolved atau cancelled.');
        }

        $projectRequest->update([
            'ticket_status' => 'closed',
            'closed_at' => now(),
        ]);

        ActivityLog::log('close_ticket', 'Closed ticket: ' . ($projectRequest->ticket_number ?? $projectRequest->project_name), $projectRequest);

        $ticketCode = $projectRequest->ticket_number ?? ('#' . $projectRequest->id);
        SystemEmailNotifier::sendToUser(
            $projectRequest->client,
            'Tiket Ditutup: ' . $ticketCode,
            'Tiket telah ditutup',
            "Tiket {$ticketCode} ({$projectRequest->project_name}) telah ditutup.\nTerima kasih telah menggunakan layanan kami.",
            route('project-requests.show', $projectRequest),
            'Lihat Riwayat Tiket',
            'Email ini sebagai konfirmasi penutupan tiket.'
        );

        return back()->with('success', 'Ticket berhasil ditutup.');
    }

    public function uploadRequirements(Request $request, ProjectRequest $projectRequest)
    {
        $request->validate([
            'requirements.*' => 'required|file|max:10240',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('requirements')) {
            $version = $projectRequest->requirements()->max('version') + 1;

            foreach ($request->file('requirements') as $file) {
                $path = $file->store('requirements', 'public');
                
                ProjectRequirement::create([
                    'project_request_id' => $projectRequest->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'version' => $version,
                    'is_current_version' => true,
                    'description' => $request->description,
                ]);
            }
        }

        return back()->with('success', 'Requirements uploaded successfully!');
    }

    public function downloadRequirement(ProjectRequirement $requirement)
    {
        return Storage::disk('public')->download($requirement->file_path, $requirement->file_name);
    }

    public function viewRequirement(ProjectRequirement $requirement)
    {
        abort_unless(Storage::disk('public')->exists($requirement->file_path), 404);

        $mimeType = Storage::disk('public')->mimeType($requirement->file_path) ?? 'application/octet-stream';

        return Storage::disk('public')->response(
            $requirement->file_path,
            $requirement->file_name,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . addslashes($requirement->file_name) . '"',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    public function deleteRequirement(ProjectRequirement $requirement)
    {
        // Only allow if project is draft
        if ($requirement->projectRequest->status !== 'draft') {
            return back()->with('error', 'Cannot delete requirements from submitted projects.');
        }

        $requirement->delete();

        return back()->with('success', 'Requirement file deleted successfully!');
    }

    private function generateTicketNumber(): string
    {
        $prefix = 'TCK-' . now()->format('Ym') . '-';
        $latestTicket = ProjectRequest::where('ticket_number', 'like', $prefix . '%')
            ->latest('id')
            ->value('ticket_number');

        $nextNumber = 1;

        if ($latestTicket) {
            $parts = explode('-', $latestTicket);
            $lastSequence = (int) end($parts);
            $nextNumber = $lastSequence + 1;
        }

        return $prefix . str_pad((string) $nextNumber, 6, '0', STR_PAD_LEFT);
    }

    private function computeSlaTargets(string $impact, string $urgency): array
    {
        $weights = [
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            'critical' => 4,
        ];

        $score = ($weights[$impact] ?? 2) + ($weights[$urgency] ?? 2);

        $responseHours = match (true) {
            $score >= 8 => 1,
            $score >= 6 => 2,
            $score >= 4 => 4,
            default => 8,
        };

        $resolutionHours = match (true) {
            $score >= 8 => 8,
            $score >= 6 => 24,
            $score >= 4 => 72,
            default => 120,
        };

        return [
            'response_due_at' => now()->addHours($responseHours),
            'resolution_due_at' => now()->addHours($resolutionHours),
        ];
    }
}
