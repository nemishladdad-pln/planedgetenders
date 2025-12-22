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
        Schema::create('organization_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('document_type_id')->references('id')->on('document_types')->onDelete('cascade');
            $table->foreignId('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->string('value');
            $table->string('storage');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_documents');
    }
};
