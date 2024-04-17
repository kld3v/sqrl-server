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
            $table->unsignedBigInteger('venue_domain_id');
            $table->foreign('venue_domain_id')->references('id')->on('venue_domains')->onDelete('cascade');
            $table->string('similar_domain');
            $table->unsignedTinyInteger('trust_score');
            $table->timestamps();
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
