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
        Schema::create('contractor_evaluations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('site_manager_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
            $table->foreignId('material_work_type_id')->references('id')->on('material_work_types')->onDelete('cascade');
            $table->foreignId('contractor_tender_id')->references('id')->on('contractor_tenders')->onDelete('cascade');
            $table->longText('evaluation_data')->nullable();
            $table->double('rating_calculated')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_evaluations');
    }
};
