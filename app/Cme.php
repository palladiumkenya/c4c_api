<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Cme extends Model
{
    public function getFileAttribute($value)
    {
        if ($value == null)
            return null;
        else
            return Storage::disk('public')->url($value);
    }

    public function getCreatedAtAttribute($value)
    {
        $date = Carbon::parse($value);
        return $date->isoFormat('MMM Do YYYY');
    }
}
