<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id('ScheduleID');
            $table->unsignedBigInteger('DoctorID');
            $table->date('AvailableDate'); // The date doctor is available
            $table->string('Timeslot'); // Example: "09:00 AM - 10:00 AM"
            $table->boolean('IsBooked')->default(false); // False by default, true if booked
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('DoctorID')->references('DoctorID')->on('doctors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('doctor_schedules');
    }
};
