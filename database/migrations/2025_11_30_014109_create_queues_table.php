<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->longText('description');
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone')->nullable();
            $table->string('client_company')->nullable();
            $table->enum('priority', ['Low', 'Medium', 'High'])->default('Medium');
            $table->enum('status', ['Pending', 'In Progress', 'On Hold', 'Completed', 'Cancelled'])->default('Pending');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->date('start_date');
            $table->date('deadline');
            $table->integer('progress')->default(0);
            $table->longText('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};