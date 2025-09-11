<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('productId');
            $table->unsignedBigInteger('userId');
            $table->unsignedBigInteger('categoryId');
            $table->string('name');
            $table->double('price');
            $table->text('description');
            $table->string('image')->nullable();
            $table->string('condition');
            $table->enum('status', ['available', 'sold', 'reserved'])->default('available');
            $table->integer('quantity')->default(1);
            $table->boolean('is_official')->default(false);
            $table->string('location')->nullable();
            $table->timestamps();
            
            $table->foreign('userId')->references('userId')->on('users')->onDelete('cascade');
            $table->foreign('categoryId')->references('categoryId')->on('categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};