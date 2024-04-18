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
        Schema::create('domain_similarity_flags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('monitored_domain_id');
            $table->unsignedBigInteger('similar_url_id');
            $table->unsignedTinyInteger('trust_score');
            $table->timestamps();

            $table->foreign('monitored_domain_id')->references('id')->on('monitored_domains')->onDelete('cascade');
            $table->foreign('similar_url_id')->references('id')->on('urls')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_similarity_flags');
    }
};
