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

    public function getDateAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->isoFormat('MMM Do YYYY');
    }


    public function toArray() {
        $data = parent::toArray();
        $data['device'] = optional($this->device)->name;

        return $data;
    }


}
