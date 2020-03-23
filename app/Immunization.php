<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Immunization extends Model
{


    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function disease()
    {
        return $this->belongsTo('App\Disease');
    }

    public function getDateAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->isoFormat('MMM Do YYYY');
    }

    public function toArray() {
        $data = parent::toArray();
        $data['disease'] = optional($this->disease)->name;
        $data['first_name'] = optional($this->user)->first_name;
        $data['surname'] = optional($this->user)->surname;
        $data['gender'] = optional($this->user)->gender;
        $data['msisdn'] = optional($this->user)->msisdn;

        return $data;
    }

}
