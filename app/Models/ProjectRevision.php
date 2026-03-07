<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_request_id',
        'revision_number',
        'requested_changes',
        'client_response',
        'status',
        'requested_by',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function projectRequest()
    {
        return $this->belongsTo(ProjectRequest::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSubmitted()
    {
        return $this->status === 'submitted';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function submitResponse($response)
    {
        $this->update([
            'client_response' => $response,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->projectRequest->update([
            'status' => 'under_review',
        ]);
    }

    public function approveRevision()
    {
        $this->update([
            'status' => 'approved',
        ]);
    }
}
