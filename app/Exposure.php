<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exposure extends Model
{
    public function device()
    {
        return $this->belongsTo('App\Device');
    }

    public function toArray() {
        $data = parent::toArray();
        $data['device'] = optional($this->device)->name;

        return $data;
    }


}
