<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExposuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exposures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('device_id');
            $table->dateTime('date');
            $table->string('type');
            $table->string('location');
            $table->text('description');
            $table->integer('previous_exposures');
            $table->enum('patient_hiv_status',['POSITIVE','NEGATIVE','Not Specified']);
            $table->enum('patient_hbv_status',['POSITIVE','NEGATIVE','Not Specified']);
            $table->boolean('pep_initiated');
            $table->string('device_purpose');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('device_id')->references('id')->on('devices');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exposures');
    }
}
