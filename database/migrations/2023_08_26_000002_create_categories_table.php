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
        Schema::create('categories', function (Blueprint $table) {
            $table->id('categoryId');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['approved', 'pending', 'rejected'])->default('approved');
            $table->unsignedBigInteger('parent_id')->nullable(); // For hierarchical categories
            $table->timestamps();
            
            // Foreign key to self for hierarchical structure
            $table->foreign('parent_id')->references('categoryId')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};