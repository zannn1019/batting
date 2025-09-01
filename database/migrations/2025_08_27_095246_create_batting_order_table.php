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
        Schema::create('batting_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batting_id')->constrained('batting')->onDelete('cascade');
            $table->foreignId('id_player')->constrained('players')->onDelete('cascade');
            $table->integer('batting_position');
            $table->boolean('is_extra_player')->default(false);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batting_order');
    }
};
