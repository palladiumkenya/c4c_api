<?php

namespace App\Http\Controllers\API;

use App\Exposure;
use App\HealthCareWorker;
use App\Http\Resources\GenericCollection;
use App\Immunization;
use App\NewExposure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExposureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function exposures()
    {
        return new GenericCollection(NewExposure::where('user_id', \auth()->user()->id)->orderBy('id','desc')->paginate(10));

    }

    public function all_exposures()
    {
        return new GenericCollection(NewExposure::orderBy('id','desc')->paginate(10));

    }

    public function facility_exposures($id)
    {
        $hcws = HealthCareWorker::where('facility_id',$id)->get('user_id');
        return new GenericCollection(NewExposure::orderBy('id','desc')->whereIn('user_id',$hcws)->paginate(10));

    }

    public function new_exposure(Request $request)
    {
        $request->validate([
            'exposure_date' => 'required',
            'exposure_location' => 'required',
            'exposure_type' => 'required',
            'patient_hiv_status' => 'required|in:POSITIVE,NEGATIVE,UNKNOWN',
            'patient_hbv_status' => 'required|in:POSITIVE,NEGATIVE,UNKNOWN',
            'previous_exposures' => 'required',
            'previous_pep_initiated' => 'required',
        ],[
//            'device_id.required' => 'Please select the device in use during exposure'
        ]);


        $exposure = new NewExposure();
        $exposure->user_id = \auth()->user()->id;
        $exposure->exposure_date = $request->exposure_date;
        $exposure->pep_date = $request->pep_date;
        $exposure->exposure_location = $request->exposure_location;
        $exposure->exposure_type = $request->exposure_type;
        $exposure->device_used = $request->device_used;
        $exposure->result_of	 = $request->result_of	;
        $exposure->device_purpose = $request->device_purpose;
        $exposure->exposure_when = $request->exposure_when;
        $exposure->exposure_description = $request->exposure_description;
        $exposure->patient_hiv_status = $request->patient_hiv_status;
        $exposure->patient_hbv_status = $request->patient_hbv_status;
        $exposure->previous_exposures = $request->previous_exposures;
        $exposure->previous_pep_initiated = $request->previous_pep_initiated;
        $exposure->saveOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Exposure has been reported successfully '
        ], 201);


    }
}
