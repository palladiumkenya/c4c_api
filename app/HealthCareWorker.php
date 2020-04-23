<?php

namespace App;

use Carbon\Carbon;
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

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->isoFormat('YYYY-MM-D HH:mm:ss ');
    }




    public function toArray() {
        //$data = parent::toArray();
        $data['user_id'] = $this->user_id;
        $data['first_name'] = optional($this->user)->first_name;
        $data['surname'] = optional($this->user)->surname;
        $data['gender'] = optional($this->user)->gender;
        $data['msisdn'] = optional($this->user)->msisdn;
        $data['email'] = optional($this->user)->email;
        $data['facility_name'] = optional($this->facility)->name;
        $data['facility_level'] = optional($this->facility)->keph_level;
        $data['facility_id'] = $this->facility_id;
        $data['department'] = optional($this->facility_department)->department_name;
        $data['facility_department_id'] = $this->facility_department_id;
        $data['cadre'] = optional($this->cadre)->name;
        $data['cadre_id'] = $this->cadre_id;
        $data['dob'] = $this->dob;
        $data['id_no'] = $this->id_no;
        $data['created_at'] = $this->created_at;
        $data['county'] = optional(optional($this->facility)->county)->name;
        $data['sub_county'] = optional(optional($this->facility)->sub_county)->name;


        return $data;
    }
}
