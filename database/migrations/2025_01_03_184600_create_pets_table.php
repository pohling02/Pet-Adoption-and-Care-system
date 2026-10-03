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
        Schema::create('pets', function (Blueprint $table) {
            $table->id('PetID');
            $table->string('PetCode')->nullable();
            $table->string('PetName');
            $table->string('Species');
            $table->string('Breed');
            $table->string('ManualBreed')->nullable();
            $table->string('Color');
            $table->string('Gender');
            $table->date('DateOfBirth');
            $table->text('Personality')->nullable();
            $table->text('Background')->nullable();
            $table->text('SpecialNeed')->nullable();
            $table->string('AdoptionStatus');
            $table->string('CurrentAddress');
            $table->string('CurrentLocation');
            $table->string('VaccinationStatus');
            $table->string('HealthCondition')->nullable();
            $table->boolean('Allergy')->default(false);
            $table->text('AllergyDetails')->nullable();
            $table->boolean('Neutering')->default(false);
            $table->unsignedTinyInteger('EnergyLevel')->default(3);
            $table->unsignedTinyInteger('Appetite')->default(3);
            $table->unsignedTinyInteger('Friendliness')->default(3);
            $table->unsignedTinyInteger('Adaptability')->default(3);
            $table->unsignedTinyInteger('BarkingLevel')->default(3);
            $table->unsignedTinyInteger('SheddingLevel')->default(3);
            $table->unsignedTinyInteger('Ideal_Environment')->default(3)->comment('1=apartment, 2=landed, 3=both');
            $table->unsignedBigInteger('ShelterID');
            $table->unsignedBigInteger('AdopterID')->nullable();
            $table->softDeletes();

            $table->foreign('ShelterID')->references('UserID')->on('users')->onDelete('cascade');
            $table->foreign('AdopterID')->references('UserID')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn('PetCode');
        });
    }
};
