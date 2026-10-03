<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('pet_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['article', 'video'])->index();
            $table->text('content')->nullable();
            $table->enum('category', ['pet_care', 'food_safety', 'training'])->index();
            $table->text('image_paths')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('UserID')->on('users')->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('pet_resources');
    }
};
