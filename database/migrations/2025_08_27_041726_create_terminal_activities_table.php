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
       Schema::create('terminal_activities', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke containers.no_plat
            $table->string('container_nomor_container');
            $table->foreign('container_nomor_container')
                ->references('nomor_container')
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
        Schema::dropIfExists('terminal_activities');
    }
};
