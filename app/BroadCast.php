<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BroadCast extends Model
{
    public function facility()
    {
        return $this->belongsTo('App\Facility');
    }

    public function cadre()
    {
        return $this->belongsTo('App\Cadre');
    }

    public function creator()
    {
        return $this->belongsTo('App\User','created_by');
    }

    public function approver()
    {
        return $this->belongsTo('App\User','approved_by');
    }

    public function getApprovedAttribute($value)
    {
        return $value == 1 ? "Yes" : "No";
    }

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->isoFormat('MMM Do YYYY, h:mm:ss a');
    }

    public function getUpdatedAtAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->isoFormat('MMM Do YYYY, h:mm:ss a');
    }

    public function toArray() {
        $data = parent::toArray();
        $data['created_by'] = optional($this->creator)->first_name.' '.optional($this->creator)->surname;
        $data['approved_by'] = optional($this->approver)->first_name.' '.optional($this->approver)->surname;
        $data['facility'] = $this->facility;
        $data['cadre'] = $this->cadre;

        return $data;
    }
}
