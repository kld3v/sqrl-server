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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('company')->nullable();
            $table->string('chain')->nullable();
            $table->unsignedBigInteger('url_id')->nullable();
            $table->foreign('url_id')->references('id')->on('urls');
            $table->string('tel')->nullable();
            $table->text('address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('google_maps')->nullable();
            $table->polygon('area');
            $table->spatialIndex('area'); // existing spatial index on area
            $table->point('midpoint'); 
            $table->spatialIndex('midpoint');
            $table->boolean('complete')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
