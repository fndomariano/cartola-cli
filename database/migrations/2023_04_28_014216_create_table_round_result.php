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
        Schema::create('round_result', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('round');
            $table->float('score');
            $table->integer('ranking');
            $table->uuid('team_id');
        });

        Schema::table('round_result', function(Blueprint $table) {
            $table->foreign('team_id')->references('id')->on('team');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('round_result');
    }
};
