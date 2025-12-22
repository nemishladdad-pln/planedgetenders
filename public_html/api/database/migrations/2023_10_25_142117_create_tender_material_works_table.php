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
        Schema::create('tender_material_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->references('id')->on('tenders')->constrained()->cascadeOnDelete();
            $table->foreignId('material_work_type_id')->references('id')->on('material_work_types')->constrained()->cascadeOnDelete();
            $table->string('work')->nullable(true);
            $table->double('rate')->nullable(true);
            $table->foreignId('unit_id')->references('id')->on('units')->cascadeOnDelete();
            $table->string('quantity')->nullable(true);
            $table->string('total')->nullable(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_material_works');
    }
};
