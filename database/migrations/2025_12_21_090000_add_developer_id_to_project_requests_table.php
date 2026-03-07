<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            $table->foreignId('developer_id')->nullable()->after('client_id')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('project_requests', function (Blueprint $table) {
            $table->dropForeign(['developer_id']);
            $table->dropColumn('developer_id');
        });
    }
};
