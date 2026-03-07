<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_request_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'version',
        'is_current_version',
        'description',
    ];

    protected $casts = [
        'is_current_version' => 'boolean',
    ];

    // Relationships
    public function projectRequest()
    {
        return $this->belongsTo(ProjectRequest::class);
    }

    // Accessors
    public function getFileUrlAttribute()
    {
        return Storage::url($this->file_path);
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Helper methods
    public function download()
    {
        return Storage::download($this->file_path, $this->file_name);
    }

    public function deleteFile()
    {
        if (Storage::exists($this->file_path)) {
            Storage::delete($this->file_path);
        }
    }

    // Boot method to handle file deletion
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($requirement) {
            $requirement->deleteFile();
        });
    }
}
