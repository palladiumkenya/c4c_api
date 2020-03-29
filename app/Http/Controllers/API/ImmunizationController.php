<?php

namespace App\Http\Controllers\API;

use App\Disease;
use App\HealthCareWorker;
use App\Http\Resources\GenericCollection;
use App\Immunization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImmunizationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function immunizations()
    {
        $array = array();
        foreach (Disease::all() as $disease) {
            $diseaseArray = array();

            foreach (Immunization::where('disease_id', $disease->id)->where('user_id', \auth()->user()->id)->get() as $immunization){
                array_push($diseaseArray,$immunization->date);
            }

            array_push($array,["disease"=>$disease->name, "immunizations"=>$diseaseArray]);
        }

        return response()->json([
            'success' => true,
            'message' => $array
        ], 200);
    }


    public function facility_immunizations($id)
    {
        $hcws = HealthCareWorker::where('facility_id',$id)->get('user_id');
        return new GenericCollection(Immunization::orderBy('id','desc')->whereIn('user_id',$hcws)->paginate(10));
    }

    public function all_immunizations()
    {
        return new GenericCollection(Immunization::orderBy('id','desc')->paginate(10));
    }

    public function new_immunization(Request $request)
    {
        $request->validate([
            'disease_id' => 'required|numeric|exists:diseases,id',
            'date' => 'required',
        ],[
            'disease_id.required' => 'Please select a disease'
        ]);


        $immunization = new Immunization();
        $immunization->user_id = \auth()->user()->id;
        $immunization->disease_id = $request->disease_id;
        $immunization->date = $request->date;
        $immunization->saveOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Immunization record added successfully'
        ], 200);


    }
}
