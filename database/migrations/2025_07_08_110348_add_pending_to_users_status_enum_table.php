<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // استيراد DB

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        DB::statement("ALTER TABLE users CHANGE status status ENUM('pending', 'active', 'inactive', 'suspended') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        DB::statement("UPDATE users SET status = 'inactive' WHERE status = 'pending'");
        DB::statement("ALTER TABLE users CHANGE status status ENUM('active', 'inactive', 'suspended') DEFAULT 'active'");
    }
};