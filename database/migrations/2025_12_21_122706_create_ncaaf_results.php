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
        Schema::create('ncaaf_results', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('game_date');
            $table->time('game_time');
            $table->string('team_left');
            $table->string('team_right');
            $table->float('spread_left')->nullable();
            $table->float('spread_right')->nullable();
            $table->integer('perc_bets_left')->nullable();
            $table->integer('perc_bets_right')->nullable();
            $table->integer('perc_money_left')->nullable();
            $table->integer('perc_money_right')->nullable();
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ncaaf_results');
    }
};
