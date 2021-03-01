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
        $data['partner_name'] = optional($this->partner)->name;
        $data['mfl_code'] = optional($this->facility)->mfl_code;
        $data['first_name'] = optional($this->user)->first_name;
        $data['surname'] = optional($this->user)->surname;
        $data['msisdn'] = optional($this->user)->msisdn;
        $data['email'] =  optional($this->user)->email;                                                                                                                                                                                                                                                            optional($this->user)->email;

        return $data;
    }
}
