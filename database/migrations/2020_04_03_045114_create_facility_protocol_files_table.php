<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilityProtocolFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_protocol_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('facility_protocol_id');
            $table->text('file');
            $table->timestamps();

            $table->foreign('facility_protocol_id')->references('id')->on('facility_protocols');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('facility_protocol_files');
    }
}
