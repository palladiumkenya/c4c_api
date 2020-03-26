<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Exposure extends Model
{
    public function device()
    {
        return $this->belongsTo('App\Device');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getDateAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->isoFormat('MMM Do YYYY');
    }


    public function toArray() {
        $data = parent::toArray();
        $data['device'] = optional($this->device)->name;
        $data['first_name'] = optional($this->user)->first_name;
        $data['surname'] = optional($this->user)->surname;
        $data['facility'] = optional(optional(optional($this->user)->hcw)->facility)->name;

        return $data;
    }


}
