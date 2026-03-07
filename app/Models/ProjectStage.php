<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'order',
        'icon',
        'color',
        'estimated_duration',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function progressLogs()
    {
        return $this->hasMany(ProjectProgressLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Helper methods
    public function getNextStage()
    {
        return self::where('order', '>', $this->order)
            ->where('is_active', true)
            ->orderBy('order')
            ->first();
    }

    public function getPreviousStage()
    {
        return self::where('order', '<', $this->order)
            ->where('is_active', true)
            ->orderBy('order', 'desc')
            ->first();
    }

    public function isFirstStage()
    {
        return $this->order === self::active()->min('order');
    }

    public function isLastStage()
    {
        return $this->order === self::active()->max('order');
    }
}
