<?php

namespace App\Http\Controllers\API;

use App\Exposure;
use App\HealthCareWorker;
use App\Http\Resources\GenericCollection;
use App\Immunization;
use App\Jobs\SendSMS;
use App\NewExposure;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

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

        Log::info("HCWs:". $hcws);

        return new GenericCollection(NewExposure::whereIn('user_id',$hcws)->orderBy('id','desc')->paginate(10));

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

        send_sms(auth()->user()->msisdn,  "Hello ".auth()->user()->first_name.", the exposure is regrettable. Kindly prevent infection by visiting your PEP care provider immediately. After 72 hours’ prevention is not effective.");

        SendSMS::dispatch(auth()->user(),"Hello ".auth()->user()->first_name.", it is 24 hours since you were exposed. Have you visited your PEP care provider? YES/NO. MOH")->delay(now()->addHours(24));

        return response()->json([
            'success' => true,
            'message' => 'Exposure has been reported successfully '
        ], 201);


    }
}
