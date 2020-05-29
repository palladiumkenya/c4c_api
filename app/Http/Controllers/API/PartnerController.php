<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\GenericCollection;
use App\NewExposure;
use App\Partner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function all_partners()
    {
        return new GenericCollection(Partner::orderBy('id','desc')->paginate(20));

    }
}
