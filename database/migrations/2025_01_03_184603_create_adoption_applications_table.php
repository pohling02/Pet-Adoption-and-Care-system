<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('adoption_applications', function (Blueprint $table) {
            $table->bigIncrements('ApplicationID');
            $table->date('ApplicationDate')->useCurrent();
            $table->enum('AdoptionStatus', ['Pending', 'Approved', 'Rejected', 'Under Review', 'Cancelled'])->default('Pending');
            $table->unsignedBigInteger('AdopterID');
            $table->unsignedBigInteger('PetID');
            $table->string('FullName');
            $table->integer('Age');
            $table->enum('Gender', ['Male', 'Female', 'Other'])->nullable();
            $table->text('Address');
            $table->string('Postcode', 10);
            $table->string('State');
            $table->string('ContactNumber');
            $table->string('Email');
            $table->string('Occupation')->nullable();

            $table->text('HouseholdDetails')->nullable();
            $table->text('OtherPetsInfo')->nullable();

            $table->text('ReasonForAdoption')->nullable();
            $table->text('PetCarePlan')->nullable();
            $table->text('EmergencyPlan')->nullable();
            $table->boolean('SpayNeuterAgreement')->default(false);
            $table->boolean('ReturnAgreement')->default(false);
            $table->unsignedTinyInteger('ResubmissionCount')->default(0);
            $table->dateTime('LastRejectionDate')->nullable();
            $table->string('ReferenceName')->nullable();
            $table->string('ReferenceContact')->nullable();
            $table->string('ReferenceRelationship')->nullable();

            $table->text('CancellationReason')->nullable();

            $table->text('StaffNotes')->nullable();
            $table->date('EstimatedPickupDate')->nullable();
            $table->foreign('AdopterID')->references('UserID')->on('users')->onDelete('cascade');
            $table->foreign('PetID')->references('PetID')->on('pets')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.s
     */
    public function down(): void {
        Schema::dropIfExists('adoption_applications');
    }
};
