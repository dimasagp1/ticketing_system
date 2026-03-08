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

    public function getIsPreviewableAttribute(): bool
    {
        $mime = strtolower((string) ($this->file_type ?? ''));
        $extension = strtolower((string) pathinfo((string) $this->file_name, PATHINFO_EXTENSION));

        if (str_starts_with($mime, 'image/') || $mime === 'application/pdf' || str_starts_with($mime, 'text/')) {
            return true;
        }

        return in_array($extension, ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp', 'svg', 'txt', 'csv', 'json'], true);
    }

    public function getFileIconClassAttribute(): string
    {
        $mime = strtolower((string) ($this->file_type ?? ''));
        $extension = strtolower((string) pathinfo((string) $this->file_name, PATHINFO_EXTENSION));

        if (str_starts_with($mime, 'image/') || in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp', 'svg'], true)) {
            return 'fa-file-image text-info';
        }

        if ($mime === 'application/pdf' || $extension === 'pdf') {
            return 'fa-file-pdf text-danger';
        }

        if (str_contains($mime, 'spreadsheet') || in_array($extension, ['xls', 'xlsx', 'csv'], true)) {
            return 'fa-file-excel text-success';
        }

        if (str_contains($mime, 'word') || in_array($extension, ['doc', 'docx'], true)) {
            return 'fa-file-word text-primary';
        }

        if (str_contains($mime, 'zip') || in_array($extension, ['zip', 'rar', '7z', 'tar', 'gz'], true)) {
            return 'fa-file-archive text-warning';
        }

        return 'fa-file text-muted';
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
