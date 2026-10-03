<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('messages', function (Blueprint $table) {
            $table->id('MessageID');
            $table->unsignedBigInteger('SenderID'); // Sender (adopter or shelter)
            $table->unsignedBigInteger('ReceiverID'); // Receiver (adopter or shelter)
            $table->text('Content');
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->string('file_name')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Foreign keys
            $table->foreign('SenderID')->references('UserID')->on('users')->onDelete('cascade');
            $table->foreign('ReceiverID')->references('UserID')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('messages');
    }
};
