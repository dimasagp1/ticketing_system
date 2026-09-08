<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('project_stages')) {
            return;
        }

        // Find duplicate stage names
        $duplicates = DB::table('project_stages')
            ->select('name', DB::raw('MIN(id) as canonical_id'), DB::raw('COUNT(*) as count'))
            ->groupBy('name')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            // Get all duplicate IDs except the canonical one
            $duplicateIds = DB::table('project_stages')
                ->where('name', $dup->name)
                ->where('id', '!=', $dup->canonical_id)
                ->pluck('id');

            if ($duplicateIds->isNotEmpty()) {
                // Remap any progress logs referencing duplicate stage IDs to the canonical ID
                if (Schema::hasTable('project_progress_logs')) {
                    DB::table('project_progress_logs')
                        ->whereIn('project_stage_id', $duplicateIds)
                        ->update(['project_stage_id' => $dup->canonical_id]);
                }

                // Delete the duplicate stage records
                DB::table('project_stages')
                    ->whereIn('id', $duplicateIds)
                    ->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed as deduplication cleans up redundant data.
    }
};
