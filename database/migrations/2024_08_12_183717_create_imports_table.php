<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportsTable extends Migration
{
    public function up()
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->string('file_path');
            $table->integer('processed_rows')->default(0);
            $table->integer('total_rows')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('imports');
    }
}
