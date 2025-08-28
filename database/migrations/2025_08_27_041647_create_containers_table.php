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
      Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_container')->unique();
            $table->string('foto_nomor_container')->nullable();
            $table->string('size');
            $table->string('asal');
            $table->string('no_plat');
            $table->string('no_seal');
            $table->string('foto_no_plat')->nullable();
            $table->string('foto_no_seal')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
