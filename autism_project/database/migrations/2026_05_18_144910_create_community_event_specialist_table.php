<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_event_specialist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('specialist_id');
            $table->unsignedBigInteger('community_event_id');
            $table->timestamps();

            // Foreign keys linking the two tables
            $table->foreign('specialist_id')->references('id')->on('specialists')->onDelete('cascade');
            $table->foreign('community_event_id')->references('id')->on('community_events')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_event_specialist');
    }
};
