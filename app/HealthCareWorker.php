<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HealthCareWorker extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function facility()
    {
        return $this->belongsTo('App\Facility');
    }

    public function facility_department()
    {
        return $this->belongsTo('App\FacilityDepartment');
    }

    public function cadre()
    {
        return $this->belongsTo('App\Cadre');
    }




    public function toArray() {
        $data = parent::toArray();
        $data['first_name'] = optional($this->user)->first_name;
        $data['surname'] = optional($this->user)->surname;
        $data['gender'] = optional($this->user)->gender;
        $data['msisdn'] = optional($this->user)->msisdn;
        $data['email'] = optional($this->user)->email;
        $data['facility_name'] = optional($this->facility)->name;
        $data['facility'] = $this->facility;
        $data['department'] = optional($this->facility_department)->department_name;
        $data['cadre'] = $this->cadre;

        return $data;
    }
}
