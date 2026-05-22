<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('children', function (Blueprint $table) {

            $table->string('diagnosis')->nullable()->after('description');
            $table->string('therapy_type')->nullable()->after('diagnosis');
            $table->string('session_frequency')->nullable()->after('therapy_type');

            $table->text('last_session')->nullable()->after('session_frequency');
            $table->text('next_plan')->nullable()->after('last_session');


            $table->text('current_goals')->nullable()->after('next_plan');


            $table->text('recent_progress')->nullable()->after('current_goals');



            $table->text('important_notes')->nullable()->after('recent_progress');
        });
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropColumn([
                'diagnosis',
                'therapy_type',
                'session_frequency',
                'last_session',
                'next_plan',
                'current_goals',
                'recent_progress',
                'important_notes',

            ]);
        });
    }
};
