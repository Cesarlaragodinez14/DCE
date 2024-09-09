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
        Schema::table('aditorias', function (Blueprint $table) {
            $table->string('jd')->nullable();
        });
    }

    public function down()
    {
        Schema::table('aditorias', function (Blueprint $table) {
            $table->dropColumn('jd');
        });
    }
};
