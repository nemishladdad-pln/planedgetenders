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
        Schema::create('tenders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreignId('project_building_id')->references('id')->on('project_buildings')->onDelete('cascade');
            $table->string('tender_reference_number')->unique();
            $table->string('tender_uid')->unique();
            $table->foreignId('tender_type_id')->references('id')->on('tender_types')->onDelete('cascade');
            $table->foreignId('grade_id')->nullable()->references('id')->on('grades')->onDelete('cascade');
            $table->foreignId('form_contract_id')->references('id')->on('form_contracts')->onDelete('cascade');
            $table->foreignId('tender_category_id')->references('id')->on('tender_categories')->onDelete('cascade');
            $table->boolean('general_technical_evaluation')->default(false);
            $table->boolean('item_wise_evaluation_allowed')->default(false);
            $table->boolean('allow_two_stage_bidding')->default(false);
            $table->string('tender_fee')->default(0);
            $table->boolean('is_paid')->default(false);
            $table->integer('emd_amount')->default(0);
            $table->boolean('emd_exemption_allowed')->default(false);
            $table->foreignId('emd_fee_type_id')->references('id')->on('emd_fee_types')->onDelete('cascade');
            $table->string('emd_percentage')->default(0);
            $table->foreignId('material_work_type_id')->references('id')->on('material_work_types')->onDelete('cascade');
            $table->string('work_title')->nullable();
            $table->text('work_description')->nullable();
            $table->text('pre_qualification')->nullable();
            $table->text('remarks')->nullable();
            $table->string('tender_value')->nullable();
            $table->string('location')->nullable();
            $table->integer('pin_code')->nullable();
            $table->integer('bid_validity_days')->default(0);
            $table->string('period_of_works')->nullable();
            $table->foreignId('pre_bid_meeting_place_id')->references('id')->on('pre_bid_meeting_places')->onDelete('cascade');
            $table->date('pre_bid_meeting_date')->nullable();
            $table->date('pre_bid_opening_date')->nullable();
            $table->string('pre_bid_opening_place')->nullable();
            $table->date('published_date')->nullable();
            $table->date('bid_opening_date')->nullable();
            $table->date('document_download_sale_start_date')->nullable();
            $table->date('document_download_sale_end_date')->nullable();
            $table->date('clarification_start_date')->nullable();
            $table->date('clarification_end_date')->nullable();
            $table->date('bid_submission_start_date')->nullable();
            $table->date('bid_submission_end_date')->nullable();
            $table->string('authorized_name')->nullable();
            $table->text('authorized_address')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('is_work_order_generated')->nullable()->default(0);
            $table->foreignId('approved_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('awarded_to')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->date('awarded_on')->nullable();
            $table->enum('status', ['active', 'inactive', 'cancelled', 'upcoming', 're_tender'])->default('active')->nullable();
            $table->boolean('is_under_budget')->nullable()->default(false);
            $table->string('tender_password')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
