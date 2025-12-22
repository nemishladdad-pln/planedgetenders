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
        Schema::create('tender_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->references('id')->on('tenders')->onDelete('cascade');
            $table->foreignId('document_format_id')->references('id')->on('document_formats')->onDelete('cascade');
            $table->foreignId('tender_document_type_id')->references('id')->on('tender_document_types')->onDelete('cascade');
            $table->string('storage')->nullable();
            $table->string('description')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tender_documents');
    }
};
