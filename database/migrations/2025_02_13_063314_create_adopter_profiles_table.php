<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('adopter_profiles', function (Blueprint $table) {
            $table->id('ProfileID');
            $table->unsignedBigInteger('UserID');
            $table->foreign('UserID')->references('UserID')->on('users')->onDelete('cascade');
            $table->string('phone_number')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Prefer not to say'])->nullable();
            $table->text('address')->nullable();
            $table->string('occupation')->nullable();
            $table->string('pet_preference')->nullable();
            $table->string('preferred_location')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('activity_level')->nullable();
            $table->string('home_type')->nullable();
            $table->string('other_pets')->nullable();
            $table->string('allergies')->nullable();
            $table->integer('hours_per_day')->nullable();
            $table->string('personality_preference')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adopter_profiles');
    }
};
