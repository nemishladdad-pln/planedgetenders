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
        Schema::create('contractor_works', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
            $table->text('title_description');
            $table->string('scope_work')->nullable();
            $table->string('location')->nullable();
            $table->string('tendered_cost')->nullable();
            $table->string('actual_cost')->nullable();
            $table->string('stage_work')->nullable();
            $table->date('award_date')->nullable();
            $table->date('planned_completion_date')->nullable();
            $table->string('actual_completion_date')->nullable();
            $table->string('client_contact_person_name')->nullable();
            $table->text('client_contact_person_address')->nullable();
            $table->string('architects_name')->nullable();
            $table->string('other_consultants_name')->nullable();
            $table->text('responsible_staff')->nullable();

            $table->boolean('completed')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_works');
    }
};
