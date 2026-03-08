<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('notification_type', 50);
            $table->string('reference_type', 150);
            $table->unsignedBigInteger('reference_id');
            $table->timestamp('read_at');
            $table->timestamps();

            $table->unique(['user_id', 'notification_type', 'reference_type', 'reference_id'], 'notification_reads_unique_ref');
            $table->index(['user_id', 'notification_type'], 'notification_reads_user_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_reads');
    }
};
