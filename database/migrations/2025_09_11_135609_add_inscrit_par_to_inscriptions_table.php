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
        Schema::table('inscriptions', function (Blueprint $table) {
            
            $table->enum('inscrit_par', ['Sara BELKASSEH', 'Ghizlane LAFKIR', 'Lamiae HAJIB', 'Abdellatif LEZHARI', 'Khalid Katkout'])->nullable()->after('notes');

            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropColumn('inscrit_par');
        });
    }
};