<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    public function departments() {
        return $this->hasMany(FacilityDepartment::class);
    }

    public function county() {
        return $this->belongsTo(County::class);
    }

    public function sub_county() {
        return $this->belongsTo(SubCounty::class, 'Sub_County_ID');
    }

    public function toArray() {
        //$data = parent::toArray();
        $data['id'] = $this->id;
        $data['name'] = $this->name;
        $data['mfl_code'] = $this->code;
        $data['county'] = optional($this->county)->name;
        $data['sub_county'] = optional($this->sub_county)->name;
        $data['level'] = $this->keph_level;

        return $data;
    }

}
