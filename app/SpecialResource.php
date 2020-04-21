<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class SpecialResource extends Model
{

    use SoftDeletes;

    public function getFileAttribute($value)
    {
        if ($value == null)
            return null;
        else
            return Storage::disk('public')->url($value);
    }

    public function files()
    {
        return $this->hasMany('App\SpecialResourceFile');
    }

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->isoFormat('MMM Do YYYY');
    }

    public function toArray() {
        $data = parent::toArray();
        $data['files'] = $this->files;

        return $data;
    }
}
