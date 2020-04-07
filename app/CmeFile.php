<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CmeFile extends Model
{
//    public function getFileAttribute($value)
//    {
//        if ($value == null)
//            return null;
//        else
//            return Storage::disk('public')->url($value);
//    }

    public function toArray() {
        $data['link'] = Storage::disk('public')->url($this->file);
        $data['file_name'] = ltrim($this->file,"uploads/");
        return $data;
    }

}
