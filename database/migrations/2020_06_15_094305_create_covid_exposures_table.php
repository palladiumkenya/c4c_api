<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCovidExposuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_exposures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('id_no');
            $table->dateTime('date_of_contact');
            $table->boolean('ppe_worn');
            $table->string('ppes')->nullable();
            $table->boolean('ipc_training');
            $table->string('symptoms')->nullable();
            $table->string('pcr_test');
            $table->string('management');
            $table->dateTime('isolation_start_date');

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
        Schema::dropIfExists('covid_exposures');
    }
}
