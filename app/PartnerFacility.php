<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerFacility extends Model
{
    public function partner()
    {
        return $this->belongsTo('App\Partner');
    }

    public function facility()
    {
        return $this->belongsTo('App\Facility','facility_id');
    }

    public function toArray() {
        $data = parent::toArray();
        $data['partner_name'] = optional($this->partner)->name;
        $data['mfl_code'] = optional($this->facility)->mfl_code;
        $data['facility_name'] = optional($this->facility)->name;                                                                                                                                                                                                                                                           optional($this->user)->email;

        return $data;
    }
}
