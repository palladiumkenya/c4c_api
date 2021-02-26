<?php

namespace App\Http\Controllers\API;

use App\Disease;
use App\HealthCareWorker;
use App\PartnerUser;
use App\Http\Resources\GenericCollection;
use App\Immunization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

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

            if (sizeof($diseaseArray) > 0)
                array_push($array,["disease_id"=>$disease->id,"disease"=>$disease->name, "immunizations"=>$diseaseArray]);
        }

        return response()->json([
            'success' => true,
            'data' => $array
        ], 200);
    }

    public function facility_immunizations($id)
    {
        $hcws = HealthCareWorker::where('facility_id',$id)->pluck('user_id');

        Log::info("hcws ", json_decode($hcws));

        return new GenericCollection(Immunization::orderBy('id','desc')->whereIn('user_id',$hcws)->paginate(100));
    }

    public function partner_immunizations($id)
    {
        $hcws = PartnerUser::where('partner_id',$id)->pluck('user_id');

        Log::info("hcws ", json_decode($hcws));

        return new GenericCollection(Immunization::orderBy('id','desc')->whereIn('user_id',$hcws)->paginate(100));
    }

    public function facility_immunizations_by_disease($id, $disease_id)
    {
        $hcws = HealthCareWorker::where('facility_id',$id)->pluck('user_id');
        return new GenericCollection(Immunization::where('disease_id', $disease_id)->orderBy('id','desc')->whereIn('user_id',$hcws)->paginate(100));
    }

    public function partner_immunizations_by_disease($id, $disease_id)
    {
        $hcws = PartnerUser::where('partner_id',$id)->pluck('user_id');
        return new GenericCollection(Immunization::where('disease_id', $disease_id)->orderBy('id','desc')->whereIn('user_id',$hcws)->paginate(100));
    }

    public function all_immunizations()
    {
        return new GenericCollection(Immunization::orderBy('id','desc')->paginate(100));
    }

    public function all_immunizations_by_disease($id)
    {
        return new GenericCollection(Immunization::where('disease_id', $id)->orderBy('id','desc')->paginate(100));
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

        if (!is_null($request->second_dose)){
            $immunization = new Immunization();
            $immunization->user_id = \auth()->user()->id;
            $immunization->disease_id = $request->disease_id;
            $immunization->date = $request->second_dose;
            $immunization->saveOrFail();
        }

        if (!is_null($request->third_dose)){
            $immunization = new Immunization();
            $immunization->user_id = \auth()->user()->id;
            $immunization->disease_id = $request->disease_id;
            $immunization->date = $request->third_dose;
            $immunization->saveOrFail();
        }

        return response()->json([
            'success' => true,
            'message' => 'Immunization record added successfully'
        ], 200);


    }
}
