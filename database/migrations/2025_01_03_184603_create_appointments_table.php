<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('AppointmentID');
            $table->dateTime('AppointmentDate');
            $table->string('Purpose');
            $table->string('Status')->default('Confirmed');
            $table->boolean('is_rescheduled')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->unsignedBigInteger('AdopterID')->nullable();
            $table->unsignedBigInteger('ShelterStaffID')->nullable();
            $table->unsignedBigInteger('PetID');
            $table->unsignedBigInteger('DoctorID');
            $table->unsignedBigInteger('timeslot_id')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->unsignedBigInteger('last_modified_by_user_id')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('AdopterID')->references('UserID')->on('users')->onDelete('cascade');
            $table->foreign('ShelterStaffID')->references('UserID')->on('users')->onDelete('cascade');
            $table->foreign('PetID')->references('PetID')->on('pets')->onDelete('cascade');
            $table->foreign('DoctorID')->references('DoctorID')->on('doctors')->onDelete('cascade');
            $table->foreign('timeslot_id')->references('ScheduleID')->on('doctor_schedules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('appointments');
    }
};
