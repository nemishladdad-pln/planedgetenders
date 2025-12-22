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
        Schema::create('contractor_tender_work_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('tender_id')->references('id')->on('tenders')->onDelete('cascade');
            $table->foreignId('contractor_tender_id')->references('id')->on('contractor_tenders')->onDelete('cascade');
            $table->foreignId('gm_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreignId('contractor_id')->references('id')->on('contractors')->onDelete('cascade');
            $table->foreignId('period_of_payment_id')->references('id')->on('period_of_payments')->onDelete('cascade');
            $table->longText('general_terms_conditions')->nullable();
            $table->longText('more_detail')->nullable();
            $table->boolean('is_gm_approved')->default(false);
            $table->boolean('is_organization_approved')->default(false);
            $table->boolean('is_contractor_approved')->default(false);
            $table->boolean('is_admin_approved')->default(false);
            $table->longText('admin_comments')->nullable();
            $table->longText('gm_comments')->nullable();
            $table->longText('organization_comments')->nullable();
            $table->longText('contractor_comments')->nullable();
            $table->boolean('is_awarded')->default(false);
            $table->boolean('is_admin_rejected')->nullable()->default(false);
            $table->boolean('is_gm_rejected')->nullable()->default(false);
            $table->boolean('is_organization_rejected')->nullable()->default(false);
            $table->boolean('is_contractor_rejected')->nullable()->default(false);
            $table->string('storage')->nullable();
            $table->string('tender_cost')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractor_tender_work_orders');
    }
};
