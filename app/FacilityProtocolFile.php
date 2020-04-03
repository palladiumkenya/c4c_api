<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FacilityProtocolFile extends Model
{
    public function getFileAttribute($value)
    {
        if ($value == null)
            return null;
        else
            return Storage::disk('public')->url($value);
    }

    public function toArray() {
        return $this->file;
    }

}
