<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCovidExposuresAddPlaceOfDiagnosis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('covid_exposures', function(Blueprint $table)
        {
            $table->string('place_of_diagnosis')->after('isolation_start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('covid_exposures', function(Blueprint $table)
        {
            $table->dropColumn('place_of_diagnosis');
        });
    }
}
