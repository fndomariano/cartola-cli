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
        Schema::create('season', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->year('year');
            $table->decimal('value_round', 6, 2);
            $table->decimal('value_subscription', 6, 2);
            $table->integer('number_exempt_players_round');            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('season');
    }
};
