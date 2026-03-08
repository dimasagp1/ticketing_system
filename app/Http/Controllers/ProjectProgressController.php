<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\ProjectStage;
use App\Models\ProjectProgressLog;
use App\Models\ActivityLog;
use App\Services\SystemEmailNotifier;
use Illuminate\Http\Request;

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
        ]);

        // Complete current stage if exists
        $currentStage = $queue->getCurrentStage();
        if ($currentStage) {
            $currentStage->completeStage(100);
        }

        // Create new progress log
        $progressLog = ProjectProgressLog::create([
            'queue_id' => $queue->id,
            'project_stage_id' => $request->stage_id,
            'progress_percentage' => $request->progress_percentage,
            'activity_description' => $request->activity_description,
            'updated_by' => auth()->id(),
            'stage_started_at' => now(),
        ]);

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

        if ($queue->projectRequest && $queue->projectRequest->client) {
            $projectRequest = $queue->projectRequest;
            $ticketCode = $projectRequest->ticket_number ?? ('#' . $projectRequest->id);
            $stageName = optional($progressLog->projectStage)->name ?? 'Tahap Progres';

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

        return back()->with('success', 'Project progress updated successfully!');
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
            return back()->with('error', 'No active stage found. Please update project stage first.');
        }

        // Add activity to current stage
        $currentStage->update([
            'activity_description' => $currentStage->activity_description . "\n\n" . now()->format('Y-m-d H:i') . ': ' . $request->activity_description,
        ]);

        ActivityLog::log('log_activity', 'Added activity log to project', $queue);

        return back()->with('success', 'Activity logged successfully!');
    }

    public function timeline(Queue $queue)
    {
        $queue->load(['progressLogs.projectStage', 'progressLogs.updatedBy']);
        
        return view('progress.timeline', compact('queue'));
    }
}
