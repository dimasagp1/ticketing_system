<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extend user roles to include operational_manager and general_manager
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('client', 'developer', 'admin', 'super_admin', 'operational_manager', 'general_manager') NOT NULL DEFAULT 'client'");

        // 2. Extend project_requests status to include waiting_manager_approval
        DB::statement("ALTER TABLE project_requests MODIFY COLUMN status ENUM('draft', 'waiting_manager_approval', 'submitted', 'under_review', 'approved', 'rejected', 'revision_requested', 'converted_to_queue') NOT NULL DEFAULT 'draft'");

        // 3. Add manager approval tracking columns to project_requests
        Schema::table('project_requests', function (Blueprint $table) {
            $table->enum('manager_role', ['operational_manager', 'general_manager'])->nullable()->after('client_id');
            $table->foreignId('manager_id')->nullable()->after('manager_role')->constrained('users')->onDelete('set null');
            $table->enum('manager_approval_status', ['pending', 'approved', 'rejected', 'revision_requested'])->nullable()->after('manager_id');
            $table->timestamp('manager_approved_at')->nullable()->after('manager_approval_status');
            $table->text('manager_notes')->nullable()->after('manager_approved_at');

            $table->index(['manager_id', 'manager_approval_status']);
            $table->index('manager_role');
        });
    }

    public function down(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropIndex(['manager_id', 'manager_approval_status']);
            $table->dropIndex(['manager_role']);
            $table->dropColumn([
                'manager_role',
                'manager_id',
                'manager_approval_status',
                'manager_approved_at',
                'manager_notes',
            ]);
        });

        DB::table('project_requests')
            ->where('status', 'waiting_manager_approval')
            ->update(['status' => 'submitted']);

        DB::statement("ALTER TABLE project_requests MODIFY COLUMN status ENUM('draft', 'submitted', 'under_review', 'approved', 'rejected', 'revision_requested', 'converted_to_queue') NOT NULL DEFAULT 'draft'");

        DB::table('users')
            ->whereIn('role', ['operational_manager', 'general_manager'])
            ->update(['role' => 'admin']);

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('client', 'developer', 'admin', 'super_admin') NOT NULL DEFAULT 'client'");
    }
};
