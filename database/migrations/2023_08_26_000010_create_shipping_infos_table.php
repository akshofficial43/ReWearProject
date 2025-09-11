<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shipping_infos', function (Blueprint $table) {
            $table->id('shippingId');
            $table->unsignedBigInteger('orderId');
            $table->string('address');
            $table->string('status');
            $table->timestamps();
            
            $table->foreign('orderId')->references('orderId')->on('orders')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipping_infos');
    }
};