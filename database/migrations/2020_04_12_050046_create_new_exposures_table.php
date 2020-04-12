<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewExposuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_exposures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->dateTime('exposure_date');
            $table->dateTime('pep_date')->nullable();
            $table->string('exposure_location');
            $table->string('exposure_type');
            $table->string('device_used')->nullable();
            $table->string('result_of')->nullable();
            $table->string('device_purpose')->nullable();
            $table->string('exposure_when')->nullable();
            $table->string('exposure_description')->nullable();
            $table->enum('patient_hiv_status',['POSITIVE','NEGATIVE','UNKNOWN']);
            $table->enum('patient_hbv_status',['POSITIVE','NEGATIVE','UNKNOWN']);
            $table->integer('previous_exposures');
            $table->string('previous_pep_initiated');


            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_exposures');
    }
}
