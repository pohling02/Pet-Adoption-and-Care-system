<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up() {
        Schema::create('pet_health_record_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('RecordID');
            $table->string('ImagePath');
            $table->foreign('RecordID')->references('RecordID')->on('health_records')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('pet_health_record_images');
    }
};
