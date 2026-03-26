<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'project_name',
        'ticket_category',
        'description',
        'estimated_duration',
        'client_id',
        'impact',
        'urgency',
        'status',
        'ticket_status',
        'queue_id',
        'submitted_at',
        'sla_response_due_at',
        'sla_resolution_due_at',
        'first_responded_at',
        'resolved_at',
        'closed_at',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'developer_id',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'sla_response_due_at' => 'datetime',
        'sla_resolution_due_at' => 'datetime',
        'first_responded_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function developer()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

    public function requirements()
    {
        return $this->hasMany(ProjectRequirement::class);
    }

    public function approvals()
    {
        return $this->hasMany(ProjectApproval::class);
    }

    public function revisions()
    {
        return $this->hasMany(ProjectRevision::class);
    }

    public function conversations()
    {
        return $this->hasMany(ChatConversation::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
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
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isSubmitted()
    {
        return $this->status === 'submitted';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function needsRevision()
    {
        return $this->status === 'revision_requested';
    }

    public function isOpenTicket()
    {
        return $this->ticket_status === 'open';
    }

    public function isInProgressTicket()
    {
        return $this->ticket_status === 'in_progress';
    }

    public function isResolvedTicket()
    {
        return $this->ticket_status === 'resolved';
    }

    public function isClosedTicket()
    {
        return $this->ticket_status === 'closed';
    }

    public function getCurrentRequirements()
    {
        return $this->requirements()->where('is_current_version', true)->get();
    }

    public function getLatestApproval()
    {
        return $this->approvals()->latest()->first();
    }

    public function getLatestRevision()
    {
        return $this->revisions()->latest()->first();
    }

    public static function ticketStatusLabels(): array
    {
        return [
            'open' => 'Terbuka',
            'in_progress' => 'Diproses',
            'pending_user' => 'Menunggu User',
            'paused' => 'Dijeda',
            'resolved' => 'Terselesaikan',
            'closed' => 'Ditutup',
            'cancelled' => 'Dibatalkan',
        ];
    }

    public static function activeTicketStatuses(): array
    {
        return [
            'open',
            'in_progress',
            'pending_user',
            'paused',
        ];
    }

    public static function slaTrackedTicketStatuses(): array
    {
        return [
            'open',
            'in_progress',
            'pending_user',
        ];
    }

    public static function pausableTicketStatuses(): array
    {
        return [
            'open',
            'in_progress',
            'pending_user',
        ];
    }

    public static function playableTicketStatuses(): array
    {
        return ['paused'];
    }

    public static function resolvableTicketStatuses(): array
    {
        return [
            'open',
            'in_progress',
            'pending_user',
        ];
    }

    public function getTicketStatusLabelAttribute(): string
    {
        $status = $this->ticket_status ?? 'open';

        return static::ticketStatusLabels()[$status] ?? ucfirst(str_replace('_', ' ', $status));
    }

    public function getTicketStatusBadgeClassAttribute(): string
    {
        return match ($this->ticket_status ?? 'open') {
            'open' => 'primary',
            'in_progress' => 'info',
            'pending_user' => 'warning',
            'paused' => 'dark',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'danger',
        };
    }

    public static function requestStatusLabels(): array
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'under_review' => 'Ditinjau',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'revision_requested' => 'Perlu Revisi',
        ];
    }

    public function getRequestStatusLabelAttribute(): string
    {
        return static::requestStatusLabels()[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getRequestStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'submitted' => 'warning',
            'under_review' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            'revision_requested' => 'primary',
            default => 'dark',
        };
    }
}
