<?php

namespace App\Http\Controllers\API;

use App\HealthCareWorker;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActionsController extends Controller
{
    public function complete_profiles(Request $request)
    {
        $request->validate([
            'msisdns' => 'required',
            'facility_id' => 'required',
            'facility_department_id' => 'required',
            'cadre_id' => 'required',
        ],[
//            'facility_id.required' => 'Please select your facility',
//            'facility_department_id.required' => 'Please select your department',
//            'cadre_id.required' => 'Please select your cadre'
        ]);


        $msisdnArray = explode(',', $request->msisdns);

        $savedHcws = 0;

        foreach ($msisdnArray as $msisdn) {
            $user = User::where('msisdn',$msisdn)->first();

            if (!is_null($user)){
                $hcw = HealthCareWorker::where('user_id', $user->id)->first();
                if (is_null($hcw)){
                    $newHcw = new HealthCareWorker();
                    $newHcw->user_id = $user->id;
                    $newHcw->facility_id = $request->facility_id;
                    $newHcw->facility_department_id = $request->facility_department_id;
                    $newHcw->cadre_id = $request->cadre_id;
                    $newHcw->saveOrFail();

                    $savedHcws++;
                }
            }

        }
        return response()->json([
            'success' => true,
            'message' => $savedHcws.' HCWs have been mapped to facilities'
        ], 201);


    }

}
