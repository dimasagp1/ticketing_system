<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_request_id')->constrained('project_requests')->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected', 'revision_requested'])->default('pending');
            $table->longText('comments')->nullable();
            $table->longText('revision_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_approvals');
    }
};
