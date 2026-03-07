<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_request_id',
        'queue_id',
        'client_id',
        'developer_id',
        'subject',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // Relationships
    public function projectRequest()
    {
        return $this->belongsTo(ProjectRequest::class);
    }

    public function queue()
    {
        return $this->belongsTo(Queue::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function developer()
    {
        return $this->belongsTo(User::class, 'developer_id');
    }

    public function messages()
    {
        return $this->hasMany(Chat::class, 'conversation_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    // Helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function close()
    {
        $this->update(['status' => 'closed']);
    }

    public function reopen()
    {
        $this->update(['status' => 'active']);
    }

    public function getUnreadMessagesCount($userId)
    {
        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function markAllAsRead($userId)
    {
        $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    public function getLatestMessage()
    {
        return $this->messages()->latest()->first();
    }

    public function updateLastMessageTime()
    {
        $this->update(['last_message_at' => now()]);
    }
}
