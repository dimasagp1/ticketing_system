<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_request_id',
        'approver_id',
        'status',
        'comments',
        'revision_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function projectRequest()
    {
        return $this->belongsTo(ProjectRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRevisionRequested($query)
    {
        return $query->where('status', 'revision_requested');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isRevisionRequested()
    {
        return $this->status === 'revision_requested';
    }

    public function approve($comments = null)
    {
        $this->update([
            'status' => 'approved',
            'comments' => $comments,
            'reviewed_at' => now(),
        ]);

        $this->projectRequest->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $this->approver_id,
        ]);
    }

    public function reject($comments)
    {
        $this->update([
            'status' => 'rejected',
            'comments' => $comments,
            'reviewed_at' => now(),
        ]);

        $this->projectRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $comments,
        ]);
    }

    public function requestRevision($revisionNotes)
    {
        $this->update([
            'status' => 'revision_requested',
            'revision_notes' => $revisionNotes,
            'reviewed_at' => now(),
        ]);

        $this->projectRequest->update([
            'status' => 'revision_requested',
        ]);
    }
}
