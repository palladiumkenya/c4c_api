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
            $table->string('transmission_mode');
            $table->unsignedBigInteger('facility_of_exposure_id')->nullable();
            $table->string('procedure_perfomed')->nullable();
            $table->string('contact_with');
            $table->boolean('direct_covid_environment_contact'); // return yes or no
            $table->boolean('ppe_worn'); // return yes or no
            $table->text('ppes')->nullable();
            $table->boolean('ipc_training'); // return yes or no
            $table->integer('ipc_training_period')->nullable(); //return this with 'months or years ago' if not null
            $table->boolean('covid_specific_training'); // return yes or no
            $table->string('covid_training_period')->nullable();
            $table->text('symptoms')->nullable();
            $table->boolean('risk_assessment_performed'); // return yes or no
            $table->string('risk_assessment_outcome')->nullable();
            $table->string('risk_assessment_recommendation')->nullable();
            $table->dateTime('risk_assessment_decision_date')->nullable();
            $table->boolean('pcr_test_done'); // return yes or no
            $table->string('pcr_test_results')->nullable();
            $table->string('exposure_management');
            $table->dateTime('isolation_start_date');
            $table->string('final_outcome')->nullable();
            $table->dateTime('isolation_end_date')->nullable();
            $table->dateTime('return_to_work_date')->nullable();
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
