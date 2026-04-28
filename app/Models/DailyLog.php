<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'log_date',
        'reporter_name',
        'department',
        'contact_info',
        'source',
        'issue_description',
        'action_taken',
        'status',
        'duration_minutes',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    /**
     * The user (IT staff) who recorded this log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get badge class for status.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Selesai'          => 'success',
            'Pending'          => 'warning',
            'Eskalasi ke Tiket'=> 'danger',
            default            => 'secondary',
        };
    }

    /**
     * Get icon for source.
     */
    public function getSourceIconAttribute(): string
    {
        return match ($this->source) {
            'WhatsApp'    => 'fab fa-whatsapp',
            'Telepon'     => 'fas fa-phone',
            'Email'       => 'fas fa-envelope',
            'Teams/Chat'  => 'fas fa-comment-dots',
            'Tatap Muka'  => 'fas fa-user',
            default       => 'fas fa-question-circle',
        };
    }

    /**
     * Get all available sources.
     */
    public static function sources(): array
    {
        return ['WhatsApp', 'Telepon', 'Tatap Muka', 'Email', 'Teams/Chat', 'Lainnya'];
    }

    /**
     * Get all available statuses.
     */
    public static function statuses(): array
    {
        return ['Selesai', 'Pending', 'Eskalasi ke Tiket'];
    }
}
