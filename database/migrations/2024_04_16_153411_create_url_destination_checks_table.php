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
        Schema::create('url_destination_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monitored_redirect_path_id');
            $table->unsignedBigInteger('final_url_id')->nullable();
            $table->boolean('is_safe');
            $table->timestamps();

            $table->foreign('monitored_redirect_path_id')->references('id')->on('monitored_redirect_paths');
            $table->foreign('final_url_id')->references('id')->on('urls')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_destination_checks');
    }
};
