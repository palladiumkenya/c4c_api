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
        $data['facility_level'] = optional(optional(optional($this->user)->hcw)->facility)->keph_level;
        $data['facility_name'] = optional(optional(optional($this->user)->hcw)->facility)->name;
        $data['county'] = optional(optional(optional(optional($this->user)->hcw)->facility)->county)->name;
        $data['sub_county'] = optional(optional(optional(optional($this->user)->hcw)->facility)->sub_county)->name;


        return $data;
    }

}
