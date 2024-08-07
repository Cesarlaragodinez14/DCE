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
            $table
                ->bigInteger('cuenta_publica')
                ->unsigned()
                ->after('jefe_de_departamento');
            $table
                ->foreign('cuenta_publica')
                ->references('id')
                ->on('cat_cuenta_publica')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
            $table->dropColumn('cuenta_publica');
            $table->dropForeign('aditorias_cuenta_publica_foreign');
        });
    }
};
