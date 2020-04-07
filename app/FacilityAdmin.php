<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacilityAdmin extends Model
{
    public function facility()
    {
        return $this->belongsTo('App\Facility');
    }

    public function admin()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function toArray() {
        $data = parent::toArray();
        $data['facility_name'] = optional($this->facility)->name;
        $data['mfl_code'] = optional($this->facility)->mfl_code;
        $data['first_name'] = optional($this->admin)->first_name;
        $data['surname'] = optional($this->admin)->surname;
        $data['msisdn'] = optional($this->admin)->msisdn;
        $data['email'] = optional($this->admin)->email;

        return $data;
    }

}
