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
        Schema::create('contractor_tender_work_revisions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('contractor_tender_id')->references('id')->on('contractor_tenders')->onDelete('cascade');
            $table->foreignId('material_work_type_id')->references('id')->on('material_work_types')->onDelete('cascade');
            $table->double('total_amount')->nullable();
            $table->integer('revision')->nullable();
            $table->double('percentage_difference')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_tender_work_revisions');
    }
};
