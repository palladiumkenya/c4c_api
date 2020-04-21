<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SpecialResourceFile extends Model
{
    public function toArray() {
        $data['link'] = Storage::disk('public')->url($this->file);
        $data['file_name'] = ltrim($this->file,"uploads/");
        return $data;
    }

}
