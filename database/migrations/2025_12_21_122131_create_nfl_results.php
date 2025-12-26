<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nfl_results', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('game_date');
            $table->time('game_time');
            $table->string('team_left');
            $table->string('team_right');
            $table->string('score_left')->nullable();
            $table->string('score_right')->nullable();
            $table->string('winning_spread')->nullable();
            $table->foreignId('data_index_id')
                ->nullable()
                ->constrained('data_index')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nfl_results');
    }
};
