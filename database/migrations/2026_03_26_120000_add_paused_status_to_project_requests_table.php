<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE project_requests MODIFY COLUMN ticket_status ENUM('open', 'in_progress', 'pending_user', 'paused', 'resolved', 'closed', 'cancelled') NOT NULL DEFAULT 'open'");
    }

    public function down(): void
    {
        DB::table('project_requests')
            ->where('ticket_status', 'paused')
            ->update(['ticket_status' => 'in_progress']);

        DB::statement("ALTER TABLE project_requests MODIFY COLUMN ticket_status ENUM('open', 'in_progress', 'pending_user', 'resolved', 'closed', 'cancelled') NOT NULL DEFAULT 'open'");
    }
};