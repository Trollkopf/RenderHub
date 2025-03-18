<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->unsignedInteger('change_count')->default(1); // Se inicia en 1 al crear el trabajo
        });
    }

    public function down()
    {
        Schema::table('change_requests', function (Blueprint $table) {
            $table->dropColumn('change_count');
        });
    }
};
