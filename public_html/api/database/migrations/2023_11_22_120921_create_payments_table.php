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
        Schema::create('payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->integer('model_id')->nullable();
            $table->string('model')->nullable();
            $table->string('transaction_id');
            $table->double('amount')->default(0);
            $table->string('currency')->nullable();
            $table->string('status')->nullable();
            $table->string('order_id')->nullable();
            $table->string('method')->nullable();
            $table->string('amount_refunded')->nullable();
            $table->string('bank')->nullable();
            $table->string('wallet')->nullable();
            $table->string('entity')->nullable();
            $table->string('refund_Date')->nullable();
            $table->string('bank_transaction_id')->nullable();
            $table->string('refund_id')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
