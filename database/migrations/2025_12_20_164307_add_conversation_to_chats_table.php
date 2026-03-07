<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            // Add conversation_id foreign key
            $table->foreignId('conversation_id')->nullable()->after('id')->constrained('chat_conversations')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn('conversation_id');
        });
    }
};
