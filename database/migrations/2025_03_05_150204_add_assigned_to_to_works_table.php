<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('works', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Admin asignado
            $table->date('due_date')->nullable(); // Fecha límite de entrega
        });
    }

    public function down()
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['assigned_to', 'due_date']);
        });
    }
};
