<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CovidExposure extends Model
{

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function facilityOfExposure()
    {
        return $this->belongsTo('App\Facility', 'facility_of_exposure_id');
    }

    public function getDateOfContactAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->isoFormat('MMM Do YYYY');
    }

    public function getPpeWornAttribute($value)
    {
        if ($value != null){
            return $value = 1 ? 'Yes' : 'No';
        }
        return $value;
    }

    public function getIpcTrainingAttribute($value)
    {
        if ($value != null){
            return $value = 1 ? 'Yes' : 'No';
        }
        return $value;
    }

//    public function getIpcTrainingPeriodAttribute($value)
//    {
//        if ($value != null){
//            $st = $value < 12 ? ' months ago' : ' years ago';
//            $duration = $value < 12 ? $value : $value/12;
//            return $duration .  $st;
//        }
//        return $value;
//    }

    public function getCovidSpecificTrainingAttribute($value)
    {
        if ($value != null){
            return $value = 1 ? 'Yes' : 'No';
        }
        return $value;
    }

    public function getRiskAssessmentPerformedAttribute($value)
    {
        if ($value != null){
            return $value = 1 ? 'Yes' : 'No';
        }
        return $value;
    }

    public function getPcrTestDoneAttribute($value)
    {
        if ($value != null){
            return $value = 1 ? 'Yes' : 'No';
        }
        return $value;
    }


    public function toArray() {
        $data = parent::toArray();
        $data['first_name'] = optional($this->user)->first_name;
        $data['surname'] = optional($this->user)->surname;
        $data['facility'] = optional(optional(optional($this->user)->hcw)->facility)->name;
        $data['facility_of_exposure'] = optional($this->facilityOfExposure)->name;
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
