<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('volunteer_id');
            $table->string('title');
            $table->string('age_group')->nullable(); // e.g., "3-5 years", "6-12 years", "teenagers", "adults"
            $table->string('location');
            $table->time('workshop_time');
            $table->date('date');
            $table->string('target_audience');
            $table->string('status')->default('pending');
            $table->foreign('volunteer_id')->references('id')->on('volunteers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshops');
    }
};
