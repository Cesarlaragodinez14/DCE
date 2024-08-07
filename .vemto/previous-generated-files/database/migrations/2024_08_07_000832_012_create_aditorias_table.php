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
        Schema::create('aditorias', function (Blueprint $table) {
            $table->id();
            $table->string('clave_de_accion')->unique();
            $table->integer('cuenta_publica');
            $table->integer('entrega');
            $table->integer('auditoria_especial');
            $table->integer('tipo_de_auditoria');
            $table->bigInteger('siglas_auditoria_especial')->unsigned();
            $table->integer('siglas_dg_uaa');
            $table->string('titulo');
            $table->integer('ente_fiscalizado');
            $table->integer('numero_de_auditoria');
            $table->bigInteger('ente_de_la_accion')->unsigned();
            $table->bigInteger('clave_accion')->unsigned();
            $table->integer('siglas_tipo_accion');
            $table->bigInteger('dgseg_ef')->unsigned();
            $table->string('nombre_director_general');
            $table->string('direccion_de_area');
            $table->string('nombre_director_de_area');
            $table->string('sub_direccion_de_area');
            $table->string('nombre_sub_director_de_area');
            $table->string('jefe_de_departamento');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table
                ->foreign('cuenta_publica')
                ->references('id')
                ->on('cat_cuenta_publica')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table
                ->foreign('entrega')
                ->references('id')
                ->on('cat_entrega')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table
                ->foreign('auditoria_especial')
                ->references('id')
                ->on('cat_auditoria_especial')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table
                ->foreign('uaa')
                ->references('id')
                ->on('cat_uaa')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table
                ->foreign('tipo_de_auditoria')
                ->references('id')
                ->on('cat_tipo_de_auditoria')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table
                ->foreign('siglas_auditoria_especial')
                ->references('id')
                ->on('cat_siglas_auditoria_especial')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table
                ->foreign('ente_fiscalizado')
                ->references('id')
                ->on('cat_ente_fiscalizado')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table
                ->foreign('ente_de_la_accion')
                ->references('id')
                ->on('cat_ente_de_la_accion')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table
                ->foreign('clave_accion')
                ->references('id')
                ->on('cat_clave_accion')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table
                ->foreign('siglas_tipo_accion')
                ->references('id')
                ->on('cat_siglas_tipo_accion')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table
                ->foreign('dgseg_ef')
                ->references('id')
                ->on('cat_dgseg_ef')
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
        Schema::dropIfExists('aditorias');
    }
};
