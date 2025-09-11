<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('paymentId');
            $table->unsignedBigInteger('orderId');
            $table->string('payment_type');
            $table->string('payment_status');
            $table->float('amount');
            $table->timestamps();
            
            $table->foreign('orderId')->references('orderId')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};