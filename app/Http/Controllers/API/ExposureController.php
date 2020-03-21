<?php

namespace App\Http\Controllers\API;

use App\Exposure;
use App\HealthCareWorker;
use App\Http\Resources\GenericCollection;
use App\Immunization;
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
        return new GenericCollection(Exposure::where('user_id', \auth()->user()->id)->orderBy('id','desc')->paginate(10));

    }

    public function all_exposures()
    {
        return new GenericCollection(Exposure::orderBy('id','desc')->paginate(10));

    }

    public function facility_exposures($id)
    {
        $hcws = HealthCareWorker::where('facility_id',$id)->get('user_id');
        return new GenericCollection(Exposure::orderBy('id','desc')->whereIn('user_id',$hcws)->paginate(10));

    }

    public function new_exposure(Request $request)
    {
        $request->validate([
            'device_id' => 'required|numeric|exists:devices,id',
            'type' => 'required',
            'location' => 'required',
            'date' => 'required',
            'description' => 'required',
            'previous_exposures' => 'required',
            'patient_hiv_status' => 'required|in:POSITIVE,NEGATIVE,Not Specified',
            'patient_hbv_status' => 'required|in:POSITIVE,NEGATIVE,Not Specified',
            'device_purpose' => 'required',
            'pep_initiated' => 'required',
        ],[
            'device_id.required' => 'Please select the device in use during exposure'
        ]);


        $exposure = new Exposure();
        $exposure->user_id = \auth()->user()->id;
        $exposure->device_id = $request->device_id;
        $exposure->date = $request->date;
        $exposure->type = $request->type;
        $exposure->location = $request->location;
        $exposure->description = $request->description;
        $exposure->previous_exposures	 = $request->previous_exposures	;
        $exposure->patient_hiv_status = $request->patient_hiv_status;
        $exposure->patient_hbv_status = $request->patient_hbv_status;
        $exposure->pep_initiated = $request->pep_initiated;
        $exposure->device_purpose = $request->device_purpose;
        $exposure->saveOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Exposure has been reported, '
        ], 201);


    }
}
