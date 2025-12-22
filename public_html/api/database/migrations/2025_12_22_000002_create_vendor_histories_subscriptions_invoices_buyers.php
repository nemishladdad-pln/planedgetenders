<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorHistoriesSubscriptionsInvoicesBuyers extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('vendor_histories')) {
            Schema::create('vendor_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id')->index();
                $table->string('event')->nullable();
                $table->json('data')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('type')->default('yearly');
                $table->timestamp('start')->nullable();
                $table->timestamp('end')->nullable();
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->decimal('amount', 15, 2)->default(0);
                $table->timestamp('registration_date')->nullable();
                $table->timestamp('due_date')->nullable();
                $table->string('status')->default('pending');
                $table->json('meta')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('buyers')) {
            Schema::create('buyers', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('email')->nullable()->index();
                $table->string('status')->default('pending'); // pending -> approved
                $table->json('data')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('vendor_histories');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('buyers');
    }
}
