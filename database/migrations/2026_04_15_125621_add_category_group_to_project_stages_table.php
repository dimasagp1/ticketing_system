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
        Schema::table('project_stages', function (Blueprint $table) {
            $table->string('category_group')->default('development')->after('is_active');
            $table->index('category_group');
        });
    }

    public function down(): void
    {
        Schema::table('project_stages', function (Blueprint $table) {
            $table->dropIndex(['category_group']);
            $table->dropColumn('category_group');
        });
    }
};
