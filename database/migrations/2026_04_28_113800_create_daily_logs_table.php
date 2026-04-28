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
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('log_date');
            $table->string('reporter_name');
            $table->string('department')->nullable();
            $table->string('contact_info')->nullable()->comment('No. HP / email pelapor');
            $table->enum('source', ['WhatsApp', 'Telepon', 'Tatap Muka', 'Email', 'Teams/Chat', 'Lainnya'])->default('Tatap Muka');
            $table->text('issue_description');
            $table->text('action_taken');
            $table->enum('status', ['Selesai', 'Pending', 'Eskalasi ke Tiket'])->default('Selesai');
            $table->unsignedInteger('duration_minutes')->nullable()->comment('Estimasi waktu penanganan dalam menit');
            $table->text('notes')->nullable()->comment('Catatan tambahan opsional');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};
