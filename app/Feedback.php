<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Feedback extends Model
{
    public function getFileAttribute($value)
    {
        if ($value == null)
            return null;
        else
            return Storage::disk('public')->url($value);
    }

}
