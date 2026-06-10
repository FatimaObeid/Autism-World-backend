<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dropping the table first ensures a clean slate when resetting
        Schema::dropIfExists('daily_progress');

        Schema::create('daily_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->onDelete('cascade');
            $table->date('date');
            
            // Your Flutter UI Fields
            $table->integer('mood_level'); // 1 to 5 slider value
            $table->boolean('sensory_play')->default(false); // Checkbox 1
            $table->boolean('social_interaction')->default(false); // Checkbox 2
            $table->text('notes')->nullable(); // Parent notes field
            
            $table->timestamps();

            // Enforces that a child only gets one log per calendar day
            $table->unique(['child_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_progress');
    }
};