<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            $table->enum('technical_subcategory', [
                'wifi',
                'printer',
                'komputer',
                'software_install',
                'supporting',
            ])->nullable()->after('ticket_category');

            $table->string('location_detail')->nullable()->after('description');
            $table->string('asset_code')->nullable()->after('location_detail');
            $table->unsignedInteger('affected_users_count')->default(1)->after('asset_code');
            $table->unsignedTinyInteger('escalation_level')->default(0)->after('ticket_status');
            $table->timestamp('escalated_at')->nullable()->after('escalation_level');
            $table->unsignedTinyInteger('reopened_count')->default(0)->after('escalated_at');

            $table->index(
                ['ticket_category', 'technical_subcategory', 'ticket_status'],
                'pr_category_subcategory_status_idx'
            );

            $table->index(
                ['sla_resolution_due_at', 'ticket_status'],
                'pr_sla_resolution_status_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            $table->dropIndex('pr_category_subcategory_status_idx');
            $table->dropIndex('pr_sla_resolution_status_idx');

            $table->dropColumn([
                'technical_subcategory',
                'location_detail',
                'asset_code',
                'affected_users_count',
                'escalation_level',
                'escalated_at',
                'reopened_count',
            ]);
        });
    }
};
