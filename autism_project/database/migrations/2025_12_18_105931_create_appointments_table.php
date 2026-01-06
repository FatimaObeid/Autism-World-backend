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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('specialist_id');
            $table->dateTime('appointment_time');
            $table->string('status')->default('pending');
            $table->foreign('parent_id')->references('id')->on('parent_profiles')->onDelete('cascade');
            $table->foreign('specialist_id')->references('id')->on('specialists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
