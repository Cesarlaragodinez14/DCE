<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aditorias', function (Blueprint $table) {
            $table->renameColumn('siglas_dg_uaa', 'uaa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aditorias', function (Blueprint $table) {
            $table->renameColumn('uaa', 'siglas_dg_uaa');
        });
    }
};
