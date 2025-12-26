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
        Schema::create('data_index', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('scraper_name');
            $table->timestamp('scraped_at');
            $table->integer('rows_found');
            $table->integer('rows_merged');
            $table->string('date')->nullable();
            $table->string('week')->nullable();
            $table->string('year')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_index');
    }
};
