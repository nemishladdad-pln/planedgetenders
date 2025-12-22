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
        Schema::create('contractor_material_work_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contractor_id')->references('id')->on('contractors')->constrained()->cascadeOnDelete();
            $table->foreignId('material_work_type_id')->references('id')->on('material_work_types')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_material_work_type');
    }
};
