<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProgressLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_id',
        'project_stage_id',
        'progress_percentage',
        'activity_description',
        'updated_by',
        'stage_started_at',
        'stage_completed_at',
    ];

    protected $casts = [
        'stage_started_at' => 'datetime',
        'stage_completed_at' => 'datetime',
    ];

    // Relationships
    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

    public function projectStage()
    {
        return $this->belongsTo(ProjectStage::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Helper methods
    public function isCompleted()
    {
        return $this->stage_completed_at !== null;
    }

    public function isInProgress()
    {
        return $this->stage_started_at !== null && $this->stage_completed_at === null;
    }

    public function getDurationAttribute()
    {
        if ($this->stage_started_at && $this->stage_completed_at) {
            return $this->stage_started_at->diffInDays($this->stage_completed_at);
        }
        
        if ($this->stage_started_at) {
            return $this->stage_started_at->diffInDays(now());
        }
        
        return 0;
    }

    public function startStage()
    {
        $this->update([
            'stage_started_at' => now(),
        ]);
    }

    public function completeStage($progressPercentage = 100)
    {
        $this->update([
            'stage_completed_at' => now(),
            'progress_percentage' => $progressPercentage,
        ]);
    }

    public static function getCurrentStageForQueue($queueId)
    {
        return self::where('queue_id', $queueId)
            ->whereNull('stage_completed_at')
            ->latest()
            ->first();
    }

    public static function getCompletedStagesForQueue($queueId)
    {
        return self::where('queue_id', $queueId)
            ->whereNotNull('stage_completed_at')
            ->orderBy('created_at')
            ->get();
    }
}
