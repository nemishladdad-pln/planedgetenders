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
        Schema::create('contractor_category_ratings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
            $table->foreignId('material_work_type_id')->references('id')->on('material_work_types')->onDelete('cascade');
            $table->foreignId('site_manager_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('category_rating_id')->references('id')->on('category_ratings')->onDelete('cascade');
            $table->foreignId('category_rating_parent_id')->references('parent_id')->on('category_ratings')->onDelete('cascade');
            $table->foreignId('quarter_id')->references('id')->on('quarters')->onDelete('cascade');
            $table->year('year')->nullable();
            $table->integer('rating');
            $table->foreignId('contractor_tender_id')->references('id')->on('contractor_tenders')->onDelete('cascade');
            $table->foreignId('project_id')->references('id')->on('projects')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_category_ratings');
    }
};
