<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->morphTo('model');
    }

    // Helper methods
    public static function log($action, $description, $model = null, $properties = [])
    {
        return self::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public static function logLogin()
    {
        return self::log('login', 'User logged in');
    }

    public static function logLogout()
    {
        return self::log('logout', 'User logged out');
    }

    public static function logCreate($model, $description = null)
    {
        return self::log(
            'create',
            $description ?? 'Created ' . class_basename($model),
            $model
        );
    }

    public static function logUpdate($model, $description = null, $changes = [])
    {
        return self::log(
            'update',
            $description ?? 'Updated ' . class_basename($model),
            $model,
            ['changes' => $changes]
        );
    }

    public static function logDelete($model, $description = null)
    {
        return self::log(
            'delete',
            $description ?? 'Deleted ' . class_basename($model),
            $model
        );
    }
}
