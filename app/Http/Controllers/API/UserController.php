<?php

namespace App\Http\Controllers\API;

use App\Cadre;
use App\CheckIn;
use App\Disease;
use App\Facility;
use App\FacilityAdmin;
use App\HealthCareWorker;
use App\PartnerUser;
use App\Partner;
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
        return new GenericCollection(User::orderBy('id','desc')->paginate(100));
    }

    public function all_hcw()
    {
        return new GenericCollection(HealthCareWorker::orderBy('id','desc')->paginate(100));
    }

    public function facility_hcw($id)
    {
        return new GenericCollection(HealthCareWorker::where('facility_id',$id)->paginate(100));
    }

    public function assign_facility_admin(Request $request)
    {
        $request->validate([
            'facility_id' => 'required',
            'user_id' => 'required'
        ]);

        $facility = Facility::find($request->facility_id);
        if (is_null($facility))
            abort(404, "Facility does not exist");

        $user = User::find($request->user_id);
        if (is_null($user))
            abort(404, "User does not exist");

        $fAdmin = FacilityAdmin::where('facility_id',$request->facility_id)->where('user_id', $request->user_id)->first();
        if (!is_null($fAdmin))
            abort(403, "User is already assigned as a facility admin");

        $facilityAdmin = new FacilityAdmin();
        $facilityAdmin->facility_id = $request->facility_id;
        $facilityAdmin->user_id = $request->user_id;
        $facilityAdmin->saveOrFail();

        return response()->json([
            'success' => true,
            'message' => 'User has been assigned as a facility admin successfully'
        ], 201);
    }

    public function get_facility_admin($id)
    {
        $facility = Facility::find($id);
        if (is_null($facility))
            abort(404, "Facility does not exist");

        return new GenericCollection(FacilityAdmin::where('facility_id', '=', $id)->paginate(10));
    }

    public function get_partner_users($id)
    {
        $partner = Partner::find($id);
        if (is_null($partner))
            abort(404, "Partner does not exist");

        return new GenericCollection(PartnerUser::where('partner_id', '=', $id)->paginate(100));
    }


    public function all_facility_admins()
    {
        return new GenericCollection(FacilityAdmin::orderBy('id','desc')->paginate(10));
    }


}
