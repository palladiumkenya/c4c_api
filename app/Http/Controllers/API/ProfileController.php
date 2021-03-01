<?php

namespace App\Http\Controllers\API;

use App\Cadre;
use App\CheckIn;
use App\Disease;
use App\HealthCareWorker;
use App\PartnerUser;
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


class ProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function complete_profile(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|numeric|exists:facilities,id',
            'facility_department_id' => 'required|numeric|exists:facility_departments,id',
            'cadre_id' => 'required|numeric|exists:cadres,id',
            'dob' => 'required',
        ],[
            'facility_id.required' => 'Please select your facility',
            'facility_department_id.required' => 'Please select your department',
            'cadre_id.required' => 'Please select your cadre'
        ]);


        DB::transaction(function() use ($request) {

            $user = \auth()->user();

            $hcw = HealthCareWorker::where('user_id',$user->id)->first();

            if (is_null($hcw)){
                $hcw = new HealthCareWorker();
                $hcw->user_id = $user->id;
                $hcw->facility_id = $request->facility_id;
                $hcw->facility_department_id = $request->facility_department_id;
                $hcw->cadre_id = $request->cadre_id;
                $hcw->dob = $request->dob;
                $hcw->id_no = $request->id_no;
                $hcw->saveOrFail();

                $hcw_partner = new PartnerUser();
                $hcw_partner->partner_id = $request->partner_id;
                $hcw_partner->user_id = $user->id;
                $hcw_partner->saveOrFail();

            }else{

                $hcw->facility_id = $request->facility_id;
                $hcw->facility_department_id = $request->facility_department_id;
                $hcw->cadre_id = $request->cadre_id;
                $hcw->dob = $request->dob;
                $hcw->id_no = $request->id_no;
                $hcw->update();

            }



            $user->profile_complete = 1;
            $user->update();


            if ($request->hepatitis_1 != null){
                $immunization = new Immunization();
                $immunization->user_id = \auth()->user()->id;
                $immunization->disease_id = 1; //hepatitis B
                $immunization->date = $request->hepatitis_1;
                $immunization->saveOrFail();
            }
            if ($request->hepatitis_2 != null){
                $immunization = new Immunization();
                $immunization->user_id = \auth()->user()->id;
                $immunization->disease_id = 1; //hepatitis B
                $immunization->date = $request->hepatitis_2;
                $immunization->saveOrFail();
            }

            if ($request->hepatitis_3 != null){
                $immunization = new Immunization();
                $immunization->user_id = \auth()->user()->id;
                $immunization->disease_id = 1; //hepatitis B
                $immunization->date = $request->hepatitis_3;
                $immunization->saveOrFail();
            }

        });

        return response()->json([
            'success' => true,
            'message' => 'Profile completed successfully.'
        ], 201);
    }

    public function update_profile(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|numeric|exists:facilities,id',
            'facility_department_id' => 'required|numeric|exists:facility_departments,id',
            'cadre_id' => 'required|numeric|exists:cadres,id',
            'first_name' => 'required',
            'surname' => 'required',
            //'gender' => 'required',
            'email' => 'nullable|unique:users,email,'.\auth()->user()->id,
            'msisdn' => 'required|string|unique:users,msisdn,'.\auth()->user()->id,
            //'dob' => 'required',
        ],[
            'facility_id.required' => 'Please select your facility',
            'facility_department_id.required' => 'Please select your department',
            'cadre_id.required' => 'Please select your cadre',
            'msisdn.required' => 'Please enter your phone number'
        ]);


        DB::transaction(function() use ($request) {

            $user = \auth()->user();
            $user->first_name = $request->first_name;
            $user->surname = $request->surname;
            //$user->gender = $request->gender;
            $user->email = $request->email;
            $user->msisdn = $request->msisdn;
            $user->update();

            $hcw = $user->hcw;

            if (!is_null($hcw)){
                $hcw->facility_id = $request->facility_id;
                $hcw->facility_department_id = $request->facility_department_id;
                $hcw->cadre_id = $request->cadre_id;
                //$hcw->dob = $request->dob;
                $hcw->id_no = $request->id_no;
                $hcw->update();
            }

        });

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.'
        ], 200);
    }

    public function check_in(Request $request)
    {
        $request->validate([
            'lat' => 'required',
            'lng' => 'required'
        ]);

        $checkin = new CheckIn();
        $checkin->user_id = \auth()->user()->id;
        $checkin->approved_by = \auth()->user()->id;
        $checkin->lat = $request->lat;
        $checkin->lng = $request->lng;
        $checkin->saveOrFail();

        return response()->json([
            'success' => true,
            'message' => 'You have successfully checked in'
        ], 201);
    }

    public function approve_check_in(Request $request)
    {
        $request->validate([
            'check_in_id' => 'required',
        ]);

        $checkin = CheckIn::find($request->check_in_id);

        if (is_null($checkin))
            abort(404,"Check in not found");


        if ($checkin->approved){
            return response()->json([
                'success' => false,
                'message' => 'Checked in has already been approved'
            ], 200);
        }else{
            $checkin->approved = true;
            $checkin->approved_by = \auth()->user()->id;
            $checkin->update();

            return response()->json([
                'success' => true,
                'message' => 'Checked in has been approved'
            ], 201);
        }
    }

    public function check_in_history()
    {
        return new GenericCollection(CheckIn::where('user_id', \auth()->user()->id)->orderBy('id','desc')->paginate(10));
    }

    public function check_in_history_by_facility($id)
    {
        $hcws = HealthCareWorker::where('facility_id',$id)->pluck('user_id');
        return new GenericCollection(CheckIn::whereIn('user_id',$hcws)->orderBy('id','desc')->paginate(10));
    }

}
