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
        Schema::create('pet_images', function (Blueprint $table) {
            $table->id('ImageID');
            $table->unsignedBigInteger('PetID');
            $table->string('ImagePath'); 
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('PetID')->references('PetID')->on('pets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pet_images');
    }
};
