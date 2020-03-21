<?php

namespace App\Http\Controllers\API;

use App\Cadre;
use App\CheckIn;
use App\Disease;
use App\HealthCareWorker;
use App\Http\Resources\GenericCollection;
use App\Immunization;
use App\Otp;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function all_users(Request $request)
    {
        return new GenericCollection(User::orderBy('id','desc')->paginate(10));
    }

    public function all_hcw()
    {
        return new GenericCollection(HealthCareWorker::orderBy('id','desc')->paginate(10));
    }

    public function facility_hcw($id)
    {
        return new GenericCollection(HealthCareWorker::where('facility_id',$id)->paginate(10));
    }

}
