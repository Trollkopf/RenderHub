<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('works', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'en_progreso', 'esperando_confirmacion', 'finalizado'])->default('pendiente')->change();
        });
    }

    public function down()
    {
        Schema::table('works', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'en_progreso', 'finalizado'])->default('pendiente')->change();
        });
    }
};
