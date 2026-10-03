<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('shelter_staff_profiles', function (Blueprint $table) {
            $table->id('ProfileID');
            $table->unsignedBigInteger('UserID');
            $table->foreign('UserID')->references('UserID')->on('users')->onDelete('cascade');
            $table->string('phone_number')->nullable();
            $table->string('shelter_name')->nullable();
            $table->text('shelter_address')->nullable();
            $table->string('position')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Prefer not to say'])->nullable();
            $table->text('bio')->nullable();
            $table->string('business_license')->nullable();
            $table->string('profile_picture')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('shelter_staff_profiles');
    }
};

