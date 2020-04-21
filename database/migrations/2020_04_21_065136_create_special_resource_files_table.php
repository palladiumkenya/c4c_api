<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialResourceFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_resource_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('special_resource_id');
            $table->text('file');
            $table->timestamps();

            $table->foreign('special_resource_id')->references('id')->on('special_resources');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_resource_files');
    }
}
