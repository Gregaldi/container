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
            $table->string('size');
            $table->string('asal');
            $table->string('no_plat');
            $table->string('no_seal');
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
