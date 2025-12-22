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
        Schema::create('organization_works', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->string('name');
            $table->string('number_buildings');
            $table->string('number_floors');
            $table->string('total_area');
            $table->string('location');
            $table->date('planned_completion_date');
            $table->date('actual_completion_date');
            $table->text('type_construction');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_works');
    }
};
