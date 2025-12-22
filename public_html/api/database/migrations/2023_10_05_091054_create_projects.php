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
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pUID');
            $table->string('name');
            $table->string('billing_name')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreignId('project_type_id')->references('id')->on('project_types')->onDelete('cascade');
            $table->string('number_of_floors')->default(1);
            $table->string('total_project_area')->default(1);
            $table->foreignId('site_project_manager_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('general_manager_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_completed')->default(false);
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
