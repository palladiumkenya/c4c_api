<?php

namespace App\Http\Controllers\API;

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
        return new GenericCollection(Immunization::where('user_id', \auth()->user()->id)->paginate(10));

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
