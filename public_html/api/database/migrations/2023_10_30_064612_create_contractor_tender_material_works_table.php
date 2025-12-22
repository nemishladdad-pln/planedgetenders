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
        Schema::create('contractor_tender_material_works', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('contractor_tender_id')->references('id')->on('contractor_tenders')->onDelete('cascade');
            $table->foreignId('tender_material_work_id')->references('id')->on('tender_material_works')->onDelete('cascade');
            $table->double('rate', 250)->nullable();
            $table->string('quantity')->nullable(true);
            $table->string('total')->nullable(true);
            $table->integer('revision')->nullable();
            $table->foreignId('approved_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_tender_material_works');
    }
};
