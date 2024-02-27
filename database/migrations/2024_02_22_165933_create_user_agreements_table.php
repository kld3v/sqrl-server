<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_agreements', function (Blueprint $table) {
            $table->id();
            $table->string('device_uuid');
            $table->unsignedBigInteger('document_version_id');
            $table->foreign('document_version_id')->references('id')->on('document_versions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_agreements');
    }
};
