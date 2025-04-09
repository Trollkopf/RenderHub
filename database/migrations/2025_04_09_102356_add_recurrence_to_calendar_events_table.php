<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->string('recurrence')->nullable(); // daily, weekly, monthly, quarterly, yearly
            $table->integer('repeat_until_days')->nullable(); // duración en días para calcular repeticiones
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_events', function (Blueprint $table) {
            $table->dropColumn('recurrence');
            $table->dropColumn('repeat_until_days');
        });
    }
};
