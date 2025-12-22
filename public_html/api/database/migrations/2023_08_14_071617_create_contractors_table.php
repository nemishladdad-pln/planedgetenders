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
        Schema::create('contractors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('cUID')->unique();
            $table->string('name');
            $table->string('company_name');
            $table->string('est_year');
            $table->string('director_name');
            $table->text('address');
            $table->date('director_dob')->nullable();
            $table->text('director_address')->nullable();
            $table->string('director_avatar')->nullable();
            $table->string('email');
            $table->string('mobile_no');
            $table->string('company_landline_no');
            $table->string('grade')->nullable();
            $table->foreignId('material_work_type_id')->references('id')->on('material_work_types')->onDelete('cascade');
            $table->foreignId('checked_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('verified_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('avp_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('director')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->text('admin_comment')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->integer('login_attempts')->default(0);
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('deleted_by')->nullable()->references('id')->on('users')->onDelete('cascade');

            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractors');
    }
};
