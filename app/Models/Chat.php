<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'support_staff_id',
        'queue_id',
        'message',
        'file_path',
        'message_type',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relationships
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function supportStaff()
    {
        return $this->belongsTo(User::class, 'support_staff_id');
    }

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeTextMessages($query)
    {
        return $query->where('message_type', 'text');
    }

    public function scopeFileMessages($query)
    {
        return $query->where('message_type', 'file');
    }

    // Helper methods
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function isFromUser($userId)
    {
        return $this->user_id === $userId;
    }

    public function hasFile()
    {
        return $this->message_type === 'file' && $this->file_path !== null;
    }

    public function getFileUrl()
    {
        if ($this->hasFile()) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::created(function ($chat) {
            // Update conversation's last_message_at
            if ($chat->conversation) {
                $chat->conversation->updateLastMessageTime();
            }
        });
    }
}
