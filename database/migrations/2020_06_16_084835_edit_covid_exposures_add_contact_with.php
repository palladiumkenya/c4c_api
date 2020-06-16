<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCovidExposuresAddContactWith extends Migration
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
            $table->string('contact_with')->after('isolation_start_date');
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
            $table->dropColumn('covid_exposures');
        });
    }
}
