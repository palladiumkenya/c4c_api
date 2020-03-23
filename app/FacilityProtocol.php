<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FacilityProtocol extends Model
{
    public function getFileAttribute($value)
    {
        return Storage::disk('public')->url($value);
    }
}
