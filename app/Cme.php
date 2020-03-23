<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Cme extends Model
{
    public function getFileAttribute($value)
    {
        return Storage::disk('public')->url($value);
    }
}
