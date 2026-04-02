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
        Schema::table('users', function (Blueprint $table) {
            // Bach n-zido l-verif hda l-count dyal l-login
            $table->timestamp('info_verified_at')->nullable()->after('login_count');
            
            // Bach n-farqo l-ism (ghaliban t-zido hda l-name l-qdim)
            $table->string('nom')->nullable()->after('name');
            $table->string('prenom')->nullable()->after('nom');
            
            // L-m3loumat l-izafiya hda l-birth_date
            $table->string('lieu_naissance')->nullable()->after('birth_date');
            $table->string('nationalite')->nullable()->after('lieu_naissance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'info_verified_at', 
                'nom', 
                'prenom', 
                'lieu_naissance', 
                'nationalite'
            ]);
        });
    }
};