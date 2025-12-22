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
        Schema::create('contractor_tenders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
            $table->foreignId('tender_id')->references('id')->on('tenders')->onDelete('cascade');
            $table->boolean('is_awarded')->nullable()->default(false);
            $table->boolean('status')->nullable()->default(false);
            $table->boolean('is_paid')->nullable()->default(false);
            $table->foreignId('approved_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_tenders');
    }
};
