<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_progress_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_id')->constrained('queues')->onDelete('cascade');
            $table->foreignId('project_stage_id')->constrained('project_stages')->onDelete('cascade');
            $table->integer('progress_percentage')->default(0);
            $table->longText('activity_description');
            $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('stage_started_at')->nullable();
            $table->timestamp('stage_completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_progress_logs');
    }
};
