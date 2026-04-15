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
        Schema::table('project_requests', function (Blueprint $table) {
            $table->enum('ticket_category', ['incident', 'service_request', 'access', 'bug', 'technical_support', 'other'])
                  ->default('incident')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            $table->enum('ticket_category', ['incident', 'service_request', 'access', 'bug', 'other'])
                  ->default('incident')
                  ->change();
        });
    }
};
