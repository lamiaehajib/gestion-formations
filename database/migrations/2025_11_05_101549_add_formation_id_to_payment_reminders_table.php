<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Zid l'colonne nullable
        Schema::table('payment_reminders', function (Blueprint $table) {
            $table->foreignId('formation_id')
                ->after('user_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
        });

        // 2. Update les enregistrements existants
        $defaultFormationId = DB::table('formations')->first()?->id;
        
        if ($defaultFormationId) {
            DB::table('payment_reminders')
                ->whereNull('formation_id')
                ->update(['formation_id' => $defaultFormationId]);
        } else {
            DB::table('payment_reminders')->delete();
        }

        // 3. Dir l'colonne NOT NULL
        DB::statement('ALTER TABLE payment_reminders MODIFY formation_id BIGINT UNSIGNED NOT NULL');

        // 4. Zid l'index jdid (ma 7tajnash n7ydo l'qdim 7it ma kansh)
        Schema::table('payment_reminders', function (Blueprint $table) {
            $table->index(['user_id', 'formation_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('payment_reminders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'formation_id', 'is_active']);
            $table->dropForeign(['formation_id']);
            $table->dropColumn('formation_id');
        });
    }
};