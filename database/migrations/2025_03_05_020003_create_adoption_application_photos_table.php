<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('adoption_application_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ApplicationID');
            $table->string('PhotoPath');
            $table->foreign('ApplicationID')->references('ApplicationID')->on('adoption_applications')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('adoption_application_photos');
    }
};
