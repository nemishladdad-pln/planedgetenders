<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTenders extends Migration
{
    public function up()
    {
        Schema::table('tenders', function (Blueprint $table) {
            // add subcategory to header tasks (RCC etc.)
            if (!Schema::hasColumn('tenders', 'subcategory')) {
                $table->string('subcategory')->nullable()->after('category');
            }

            // budget: type (lumpsum/file), amount, file path
            if (!Schema::hasColumn('tenders', 'budget_type')) {
                $table->string('budget_type')->nullable()->after('description');
            }
            if (!Schema::hasColumn('tenders', 'budget_amount')) {
                $table->decimal('budget_amount', 15, 2)->nullable()->after('budget_type');
            }
            if (!Schema::hasColumn('tenders', 'budget_file')) {
                $table->string('budget_file')->nullable()->after('budget_amount');
            }

            // signed work order upload
            if (!Schema::hasColumn('tenders', 'signed_work_order')) {
                $table->string('signed_work_order')->nullable()->after('budget_file');
            }

            // due date for calendar
            if (!Schema::hasColumn('tenders', 'due_date')) {
                $table->timestamp('due_date')->nullable()->after('signed_work_order');
            }
        });
    }

    public function down()
    {
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropColumn(['subcategory','budget_type','budget_amount','budget_file','signed_work_order','due_date']);
        });
    }
}
