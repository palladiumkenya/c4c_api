<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CovidExposure extends Model
{

    public function user()
    {
        return $this->belongsTo('App\User');
    }


    public function toArray() {
        $data = parent::toArray();
        $data['first_name'] = optional($this->user)->first_name;
        $data['surname'] = optional($this->user)->surname;
        $data['facility'] = optional(optional(optional($this->user)->hcw)->facility)->name;
        $data['facility_id'] = optional(optional(optional($this->user)->hcw)->facility)->id;
        $data['facility_level'] = optional(optional(optional($this->user)->hcw)->facility)->keph_level;
        $data['dob'] = optional(optional($this->user)->hcw)->dob;
        $data['gender'] = optional($this->user)->gender;
        $data['cadre'] = optional(optional(optional($this->user)->hcw)->cadre)->name;
        $data['county'] = optional(optional(optional(optional($this->user)->hcw)->facility)->county)->name;
        $data['sub_county'] = optional(optional(optional(optional($this->user)->hcw)->facility)->sub_county)->name;


        return $data;
    }
}
