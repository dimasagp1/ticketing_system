<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_request_id')->constrained('project_requests')->onDelete('cascade');
            $table->integer('revision_number');
            $table->longText('requested_changes');
            $table->longText('client_response')->nullable();
            $table->enum('status', ['pending', 'submitted', 'approved'])->default('pending');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_revisions');
    }
};
