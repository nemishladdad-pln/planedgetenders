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
        Schema::create('organization_bank_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->string('favouring_name');
            $table->string('account_no');
            $table->string('bank_name');
            $table->string('branch_name');
            $table->string('ifsc_code');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_bank_details');
    }
};
