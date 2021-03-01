<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerUser extends Model
{
    public function partner()
    {
        return $this->belongsTo('App\Partner');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function toArray() {
        $data = parent::toArray();
        $data['partner'] = optional($this->partner)->name;
        $data['mfl_code'] = optional($this->facility)->mfl_code;
        $data['first_name'] = optional($this->user)->first_name;
        $data['surname'] = optional($this->user)->surname;
        $data['msisdn'] = optional($this->user)->msisdn;
        $data['email'] =  optional($this->user)->email; 
        $data['gender'] =  optional($this->user)->gender;  
        $data['dob'] =  optional(optional($this->user)->hcw)->dob;     
        $data['sub_county'] = optional(optional(optional(optional($this->user)->hcw)->facility)->sub_county)->name;
        $data['facility_name'] = optional(optional(optional($this->user)->hcw)->facility)->name;
        $data['county'] = optional(optional(optional(optional($this->user)->hcw)->facility)->county)->name;
        $data['department'] = optional(optional(optional($this->user)->hcw)->facility_department)->department_name;
        $data['cadre'] = optional(optional(optional($this->user)->hcw)->cadre)->name;
                                                                                                                                                                                                                                                     optional($this->user)->email;

        return $data;
    }
}
