<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BroadcastsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
}
