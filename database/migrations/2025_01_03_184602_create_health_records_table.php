<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('health_records', function (Blueprint $table) {
            $table->id('RecordID');
            $table->string('VaccinationStatus');
            $table->text('HealthRemarks')->nullable();
            $table->string('Sterilization')->nullable();
            $table->date('LastCheckupDate');
            $table->text('Diagnosis')->nullable(); 
            $table->text('Medicine')->nullable(); 
            $table->unsignedBigInteger('PetID'); 
            $table->foreign('PetID')->references('PetID')->on('pets')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('health_records');
    }
};
