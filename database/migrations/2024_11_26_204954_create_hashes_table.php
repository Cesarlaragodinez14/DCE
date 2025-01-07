<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_hashes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdfHashesTable extends Migration
{
    public function up()
    {
        Schema::create('pdf_hashes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auditoria_id');
            $table->string('hash')->unique();
            $table->string('email');
            $table->string('ip_address');
            $table->timestamp('generated_at');
            $table->timestamps();

            // Relaciones y claves foráneas
            $table->foreign('auditoria_id')->references('id')->on('aditorias')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pdf_hashes');
    }
}

