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
        Schema::create('specialists', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('specialization')->nullable();
            $table->string('license')->nullable();
            $table->integer('years_of_experience')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialists');
    }
};
