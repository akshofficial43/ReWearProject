<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id('messageId');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->unsignedBigInteger('product_id');
            $table->text('content');
            $table->boolean('read')->default(false);
            $table->timestamps();

            $table->foreign('sender_id')->references('userId')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('userId')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('productId')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};