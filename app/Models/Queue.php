<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Queue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_name',
        'description',
        'client_name',
        'client_email',
        'client_phone',
        'client_company',
        'priority',
        'status',
        'assigned_to',
        'start_date',
        'deadline',
        'progress',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
    ];

    // Relationships
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function projectRequest()
    {
        return $this->hasOne(ProjectRequest::class);
    }

    public function progressLogs()
    {
        return $this->hasMany(ProjectProgressLog::class);
    }

    public function conversations()
    {
        return $this->hasMany(ChatConversation::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'In Progress');
    }

    public function scopeOnHold($query)
    {
        return $query->where('status', 'On Hold');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'Cancelled');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'High');
    }

    public function scopeMediumPriority($query)
    {
        return $query->where('priority', 'Medium');
    }

    public function scopeLowPriority($query)
    {
        return $query->where('priority', 'Low');
    }

    // Helper methods
    public function getCurrentStage()
    {
        return ProjectProgressLog::getCurrentStageForQueue($this->id);
    }

    public function getCompletedStages()
    {
        return ProjectProgressLog::getCompletedStagesForQueue($this->id);
    }

    public function updateProgress($percentage)
    {
        $this->update(['progress' => $percentage]);
    }

    public function isOverdue()
    {
        return $this->deadline < now() && !in_array($this->status, ['Completed', 'Cancelled']);
    }

    public function getQueuePositionAttribute()
    {
        // Only relevant for active projects (Pending or In Progress)
        if (!in_array($this->status, ['Pending', 'In Progress'])) {
            return null;
        }

        // Return 0 or special value if not assigned
        if (!$this->assigned_to) {
             return null;
        }

        return self::where('assigned_to', $this->assigned_to)
            ->whereIn('status', ['Pending', 'In Progress'])
            ->where(function($q) {
                // Priority logic: High (1) < Medium (2) < Low (3) usually, 
                // but let's assume standard FIFO if priority not numeric or complex.
                // Or simply order by created_at.
                // Let's rely on standard ID/Created_at order for simplicity unless priority is strict.
                // If this project is newer than others, it's behind them.
                $q->where('created_at', '<', $this->created_at);
            })
            ->count() + 1;
    }

    public function getDaysRemaining()
    {
        if ($this->deadline) {
            return now()->diffInDays($this->deadline, false);
        }
        return null;
    }
}
