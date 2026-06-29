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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            // Remove ->constrained() if you don't have a conversations table yet
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_type'); // 'specialist' or 'parent'
            $table->unsignedBigInteger('recipient_id'); // Added to match your model
            $table->string('recipient_type'); // Added to match your model
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('child_id')->nullable();
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
