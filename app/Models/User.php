<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'company',
        'bio',
        'avatar',
        'signature_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function projectRequests()
    {
        return $this->hasMany(ProjectRequest::class, 'client_id');
    }

    public function approvedProjects()
    {
        return $this->hasMany(ProjectRequest::class, 'approved_by');
    }

    public function approvals()
    {
        return $this->hasMany(ProjectApproval::class, 'approver_id');
    }

    public function managedProjectRequests()
    {
        return $this->hasMany(ProjectRequest::class, 'manager_id');
    }

    public function assignedQueues()
    {
        return $this->hasMany(Queue::class, 'assigned_to');
    }

    public function clientConversations()
    {
        return $this->hasMany(ChatConversation::class, 'client_id');
    }

    public function developerConversations()
    {
        return $this->hasMany(ChatConversation::class, 'developer_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isOperationalManager()
    {
        return $this->role === 'operational_manager';
    }

    public function isGeneralManager()
    {
        return $this->role === 'general_manager';
    }

    public function isManager()
    {
        return in_array($this->role, ['operational_manager', 'general_manager']);
    }

    public function canApproveAsManager()
    {
        return in_array($this->role, ['operational_manager', 'general_manager', 'super_admin']);
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

    public function isDeveloper()
    {
        return $this->role === 'developer';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function hasRole($role)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    public function canApproveProjects()
    {
        return in_array($this->role, ['admin', 'super_admin', 'operational_manager', 'general_manager']);
    }

    public function getRoleDisplayNameAttribute(): string
    {
        return match ($this->role) {
            'operational_manager' => 'Operational Manager',
            'general_manager' => 'General Manager',
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'developer' => 'Developer',
            'client' => 'Client',
            default => ucfirst(str_replace('_', ' ', $this->role ?? '')),
        };
    }

    public function canManageUsers()
    {
        return $this->role === 'super_admin';
    }

    public function canViewUsers()
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function canActivateUsers()
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function canAccessSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    // Status helper methods
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    public function activate()
    {
        $this->update(['status' => 'active']);
    }

    public function deactivate()
    {
        $this->update(['status' => 'inactive']);
    }

    public function suspend()
    {
        $this->update(['status' => 'suspended']);
    }

    // Avatar helper
    public function getAvatarUrlAttribute()
    {
        if (! $this->avatar) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
        }

        if (Str::startsWith($this->avatar, ['http://', 'https://'])) {
            return $this->avatar;
        }

        $path = str_replace('\\', '/', $this->avatar);
        $path = ltrim($path, '/');

        // Ignore invalid absolute paths (e.g. C:/Windows/Temp/...)
        if (preg_match('/^[A-Za-z]:\//', $path)) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
        }

        if (Str::startsWith($path, 'storage/')) {
            return asset($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }
}
