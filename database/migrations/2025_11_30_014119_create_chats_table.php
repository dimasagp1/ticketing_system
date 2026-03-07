<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('support_staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('queue_id')->nullable()->constrained('queues')->onDelete('cascade');
            $table->longText('message');
            $table->string('file_path')->nullable();
            $table->enum('message_type', ['text', 'file'])->default('text');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};