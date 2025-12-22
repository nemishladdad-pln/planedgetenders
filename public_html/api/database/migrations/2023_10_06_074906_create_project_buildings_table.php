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
        Schema::create('project_buildings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->string('name');
            $table->string('floors');
            $table->boolean('is_completed')->default(false);
            $table->string('percentage_completed')->default(0);
            $table->string('status')->nullable(true);
            $table->boolean('active')->default(true);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_buildings');
    }
};
