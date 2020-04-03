<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmeFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cme_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cme_id');
            $table->text('file');
            $table->timestamps();

            $table->foreign('cme_id')->references('id')->on('cmes');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cme_files');
    }
}
