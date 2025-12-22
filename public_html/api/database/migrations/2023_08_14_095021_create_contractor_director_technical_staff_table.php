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
        Schema::create('contractor_director_technical_staff', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
            $table->string('type')->nullable();
            $table->string('name');
            $table->string('qualification')->nullable();
            $table->string('experience')->nullable();
            $table->string('working_with_company_since')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_director_technical_staff');
    }
};
