<?php

namespace App\Http\Controllers\API;

use App\Disease;
use App\HealthCareWorker;
use App\PartnerUser;
use App\Http\Resources\GenericCollection;
use App\Immunization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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

    public function update_immunization(Request $request)
    {
        $request->validate([
            'disease_id' => 'required|numeric|exists:diseases,id',
            'date' => 'required',
        ],[
            'disease_id.required' => 'Please select a disease'
        ]);

        $disease = Immunization::where('user_id', $request->user_id)->where('disease_id', $request->disease_id )->first();

        $disease1 = Immunization::where('user_id', $request->user_id)->where('disease_id', $request->disease_id )->skip(1)->first();

        $disease2 = Immunization::where('user_id', $request->user_id)->where('disease_id', $request->disease_id )->skip(2)->first();

        if(is_null($disease))
            abort(404, "Immunization disease not found");

        DB::transaction(function () use ($request, $disease, $disease1, $disease2) {
            $disease->user_id = \auth()->user()->id;
            $disease->disease_id = $request->disease_id;
            $disease->date = $request->date;
            $disease->update();

            if (!is_null($request->second_dose)){
                if(is_null($disease1)) {
                    $immunization = new Immunization();
                    $immunization->user_id = \auth()->user()->id;
                    $immunization->disease_id = $request->disease_id;
                    $immunization->date = $request->second_dose;
                    $immunization->saveOrFail();
                } else {
                    $disease1->user_id = \auth()->user()->id;
                    $disease1->disease_id = $request->disease_id;
                    $disease1->date = $request->second_dose;
                    $disease1->update();
                }
            }
    
            if (!is_null($request->third_dose)){
                if(is_null($disease2)) {
                    $immunization = new Immunization();
                    $immunization->user_id = \auth()->user()->id;
                    $immunization->disease_id = $request->disease_id;
                    $immunization->date = $request->third_dose;
                    $immunization->saveOrFail();
                } else {
                    $disease2->user_id = \auth()->user()->id;
                    $disease2->disease_id = $request->disease_id;
                    $disease2->date = $request->third_dose;
                    $disease2->update();
                }
            }

            Log::info("Immunization updated succesfully");

        });

        return response()->json([
            'success' => true,
            'message' => 'Immunization updated succesfully'
        ], 201);

    }

}
