<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            $table->string('ticket_number')->nullable()->after('id');
            $table->enum('ticket_category', ['incident', 'service_request', 'access', 'bug', 'other'])->default('incident')->after('project_name');
            $table->enum('impact', ['low', 'medium', 'high', 'critical'])->default('medium')->after('developer_id');
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('medium')->after('impact');
            $table->enum('ticket_status', ['open', 'in_progress', 'pending_user', 'resolved', 'closed', 'cancelled'])->default('open')->after('status');
            $table->timestamp('sla_response_due_at')->nullable()->after('submitted_at');
            $table->timestamp('sla_resolution_due_at')->nullable()->after('sla_response_due_at');
            $table->timestamp('first_responded_at')->nullable()->after('sla_resolution_due_at');
            $table->timestamp('resolved_at')->nullable()->after('first_responded_at');
            $table->timestamp('closed_at')->nullable()->after('resolved_at');

            $table->unique('ticket_number');
            $table->index(['ticket_status', 'ticket_category']);
        });

        DB::table('project_requests')
            ->select('id', 'status', 'created_at')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $datePart = $row->created_at ? date('Ym', strtotime($row->created_at)) : now()->format('Ym');
                    $ticketNumber = sprintf('TCK-%s-%06d', $datePart, $row->id);

                    $mappedStatus = match ($row->status) {
                        'under_review', 'approved', 'converted_to_queue' => 'in_progress',
                        'revision_requested' => 'pending_user',
                        'rejected' => 'cancelled',
                        default => 'open',
                    };

                    DB::table('project_requests')
                        ->where('id', $row->id)
                        ->update([
                            'ticket_number' => $ticketNumber,
                            'ticket_status' => $mappedStatus,
                            'ticket_category' => 'incident',
                            'impact' => 'medium',
                            'urgency' => 'medium',
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            $table->dropIndex(['ticket_status', 'ticket_category']);
            $table->dropUnique(['ticket_number']);

            $table->dropColumn([
                'ticket_number',
                'ticket_category',
                'impact',
                'urgency',
                'ticket_status',
                'sla_response_due_at',
                'sla_resolution_due_at',
                'first_responded_at',
                'resolved_at',
                'closed_at',
            ]);
        });
    }
};
