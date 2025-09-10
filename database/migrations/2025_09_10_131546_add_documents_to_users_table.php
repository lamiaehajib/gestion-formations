<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Gha nzido l'column dial 'documents'
        Schema::table('users', function (Blueprint $table) {
            $table->json('documents')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Gha nhaydo l'column dial 'documents' ila kan khasna ndiro rollback
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('documents');
        });
    }
};
