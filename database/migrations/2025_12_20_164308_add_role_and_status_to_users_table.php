<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['client', 'developer', 'admin', 'super_admin'])->default('client')->after('email');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('role');
            $table->string('phone')->nullable()->after('status');
            $table->string('company')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('company');
            $table->string('avatar')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'status', 'phone', 'company', 'bio', 'avatar']);
        });
    }
};
