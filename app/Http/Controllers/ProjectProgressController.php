<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\ProjectStage;
use App\Models\ProjectProgressLog;
use App\Models\ActivityLog;
use App\Services\SystemEmailNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectProgressController extends Controller
{
    public function show(Queue $queue)
    {
        $queue->load(['assignedTo', 'progressLogs.projectStage', 'progressLogs.updatedBy']);
        
        $stages = ProjectStage::active()->ordered()->get();
        $currentStage = $queue->getCurrentStage();
        $completedStages = $queue->getCompletedStages();

        return view('progress.show', compact('queue', 'stages', 'currentStage', 'completedStages'));
    }

    public function updateStage(Request $request, Queue $queue)
    {
        if (!auth()->user()->hasRole(['developer', 'admin', 'super_admin'])) {
            abort(403);
        }

        $request->validate([
            'stage_id' => 'required|exists:project_stages,id',
            'activity_description' => 'required|string',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $attachmentData = [];
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('progress_attachments', 'public');
            $attachmentData = [
                'attachment_path' => $path,
                'attachment_name' => $file->getClientOriginalName(),
                'attachment_type' => $file->getClientMimeType(),
            ];
        }

        // Complete current stage if exists
        $currentStage = $queue->getCurrentStage();
        if ($currentStage) {
            $currentStage->completeStage(100);
        }

        // Create new progress log
        $progressLogData = array_merge([
            'queue_id' => $queue->id,
            'project_stage_id' => $request->stage_id,
            'progress_percentage' => $request->progress_percentage,
            'activity_description' => $request->activity_description,
            'updated_by' => auth()->id(),
            'stage_started_at' => now(),
        ], $attachmentData);

        $progressLog = ProjectProgressLog::create($progressLogData);

        // Update queue progress
        $queue->updateProgress($request->progress_percentage);

        // Update queue status based on progress
        if ($request->progress_percentage == 100) {
            $queue->update(['status' => 'Completed']);
            $progressLog->completeStage(100);

            if ($queue->projectRequest) {
                $queue->projectRequest->update([
                    'ticket_status' => 'resolved',
                    'resolved_at' => now(),
                ]);
            }
        } elseif ($request->progress_percentage > 0) {
            $queue->update(['status' => 'In Progress']);

            if ($queue->projectRequest) {
                $queue->projectRequest->update([
                    'ticket_status' => 'in_progress',
                    'first_responded_at' => $queue->projectRequest->first_responded_at ?? now(),
                ]);
            }
        }

        ActivityLog::log('update_progress', 'Updated project progress to ' . $request->progress_percentage . '%', $queue);

        if ($queue->projectRequest) {
            $projectRequest = $queue->projectRequest;
            $ticketCode = $projectRequest->ticket_number ?? ('#' . $projectRequest->id);
            $stageName = optional($progressLog->projectStage)->name ?? 'Tahap Progres';
            $developerName = auth()->user()->name;

            // 1. Kirim Email Notifikasi ke Klien
            if ($projectRequest->client) {
                SystemEmailNotifier::sendToUser(
                    $projectRequest->client,
                    'Update Progres Tiket: ' . $ticketCode,
                    'Ada pembaruan progres pada tiket Anda',
                    "Tiket {$ticketCode} ({$projectRequest->project_name}) diperbarui ke {$request->progress_percentage}% pada tahap {$stageName}.\nCatatan: {$request->activity_description}",
                    route('project-requests.show', $projectRequest),
                    'Lihat Progres Tiket',
                    'Anda menerima email ini karena notifikasi progres aktif.'
                );
            }

            // 2. Kirim Email Notifikasi ke Admin & Super Admin jika di-update oleh Developer
            if (auth()->user()->isDeveloper()) {
                $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->where('status', 'active')->get();
                foreach ($admins as $admin) {
                    SystemEmailNotifier::sendToUser(
                        $admin,
                        'Update Progres Developer: ' . $ticketCode,
                        "Developer {$developerName} memperbarui progres tiket",
                        "Developer {$developerName} memperbarui progres tiket {$ticketCode} ({$projectRequest->project_name}) menjadi {$request->progress_percentage}% pada tahap {$stageName}.\nCatatan: {$request->activity_description}",
                        route('progress.show', $queue),
                        'Buka Progres Tiket',
                        'Notifikasi otomatis untuk Admin & Super Admin.'
                    );
                }
            }
        }

        return back()->with('success', 'Progres proyek berhasil diperbarui.');
    }

    public function logActivity(Request $request, Queue $queue)
    {
        if (!auth()->user()->hasRole(['developer', 'admin', 'super_admin'])) {
            abort(403);
        }

        $request->validate([
            'activity_description' => 'required|string',
        ]);

        $currentStage = $queue->getCurrentStage();
        
        if (!$currentStage) {
            return back()->with('error', 'Tahap aktif tidak ditemukan. Silakan perbarui tahap proyek terlebih dahulu.');
        }

        // Add activity to current stage
        $currentStage->update([
            'activity_description' => $currentStage->activity_description . "\n\n" . now()->format('Y-m-d H:i') . ': ' . $request->activity_description,
        ]);

        ActivityLog::log('log_activity', 'Added activity log to project', $queue);

        return back()->with('success', 'Aktivitas berhasil dicatat.');
    }

    public function timeline(Queue $queue)
    {
        $queue->load(['progressLogs.projectStage', 'progressLogs.updatedBy']);
        
        return view('progress.timeline', compact('queue'));
    }

    public function downloadAttachment(ProjectProgressLog $progressLog)
    {
        abort_unless($progressLog->attachment_path && Storage::disk('public')->exists($progressLog->attachment_path), 404);

        return Storage::disk('public')->download($progressLog->attachment_path, $progressLog->attachment_name);
    }

    public function viewAttachment(ProjectProgressLog $progressLog)
    {
        abort_unless($progressLog->attachment_path && Storage::disk('public')->exists($progressLog->attachment_path), 404);

        $mimeType = Storage::disk('public')->mimeType($progressLog->attachment_path) ?? 'application/octet-stream';

        return Storage::disk('public')->response(
            $progressLog->attachment_path,
            $progressLog->attachment_name,
            [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . addslashes($progressLog->attachment_name) . '"',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }
}
