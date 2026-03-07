<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_requests', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->longText('description');
            $table->decimal('budget', 15, 2)->nullable();
            $table->integer('estimated_duration')->nullable()->comment('Duration in days');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'approved',
                'rejected',
                'revision_requested',
                'converted_to_queue'
            ])->default('draft');
            $table->foreignId('queue_id')->nullable()->constrained('queues')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->longText('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_requests');
    }
};
