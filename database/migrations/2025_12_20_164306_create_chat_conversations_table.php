<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_request_id')->nullable()->constrained('project_requests')->onDelete('cascade');
            $table->foreignId('queue_id')->nullable()->constrained('queues')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('developer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('subject')->nullable();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
