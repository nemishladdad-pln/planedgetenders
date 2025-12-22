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
        Schema::create('contractor_quality_certificates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
            $table->string('name');
            $table->string('storage');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_quality_certificates');
    }
};
