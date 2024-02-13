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
        Schema::disableForeignKeyConstraints();

        Schema::create('url_checks_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('url_id');
            $table->foreign('url_id')->references('id')->on('URLs');
            $table->smallInteger('trust_score');
            $table->json('test_result');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_checks_log');
    }
};
