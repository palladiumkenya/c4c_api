<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    public function departments() {
        return $this->hasMany(FacilityDepartment::class);
    }

}
