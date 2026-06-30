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
        Schema::table('specialists', function (Blueprint $table) {
            if (!Schema::hasColumn('specialists', 'years_of_experience')) {
                $table->integer('years_of_experience')->nullable();
            }
            if (!Schema::hasColumn('specialists', 'bio')) {
                $table->text('bio')->nullable();
            }
            if (!Schema::hasColumn('specialists', 'location')) {
                $table->string('location')->nullable();
            }
            if (!Schema::hasColumn('specialists', 'status')) {
                $table->string('status')->default('pending');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('specialists', function (Blueprint $table) {
            $table->dropColumn(['years_of_experience', 'bio', 'location', 'status']);
        });
    }
};
