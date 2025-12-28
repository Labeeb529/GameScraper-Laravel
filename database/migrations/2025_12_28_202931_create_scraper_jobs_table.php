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
        Schema::create('scraper_jobs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('name');
            $table->string('scraper_type');
            $table->string('url');
            $table->string('cron_expression');

            $table->boolean('active')->default(true);
            $table->boolean('run_once')->default(false);

            $table->timestamp('last_run_at')->nullable();
            $table->string('last_status')->nullable();
            $table->integer('last_rows_updated')->nullable();
            $table->text('last_error')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scraper_jobs');
    }
};
