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
       Schema::create('tps_activities', function (Blueprint $table) {
         $table->id();
    
        // ganti foreignId jadi string manual
        $table->string('container_no_plat'); 
        $table->foreign('container_no_plat')
            ->references('no_plat')
            ->on('containers')
            ->onDelete('cascade');

        $table->dateTime('masuk')->nullable();
        $table->dateTime('keluar')->nullable();
        $table->string('foto_masuk_depan')->nullable();
        $table->string('foto_masuk_belakang')->nullable();
        $table->string('foto_masuk_kanan')->nullable();
        $table->string('foto_masuk_kiri')->nullable();
        $table->string('foto_keluar_depan')->nullable();
        $table->string('foto_keluar_belakang')->nullable();
        $table->string('foto_keluar_kanan')->nullable();
        $table->string('foto_keluar_kiri')->nullable();
        $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tps_activities');
    }
};
