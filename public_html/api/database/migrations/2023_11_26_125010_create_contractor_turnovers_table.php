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
        Schema::create('contractor_turnovers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
            $table->string('year')->nullable();
            $table->integer('turnover')->nullable();
            $table->string('certificate_storage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_turnovers');
    }
};
