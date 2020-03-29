<?php

namespace App\Http\Controllers\API;

use App\Cadre;
use App\Cme;
use App\Device;
use App\Disease;
use App\Facility;
use App\FacilityDepartment;
use App\FacilityProtocol;
use App\Feedback;
use App\HealthCareWorker;
use App\Http\Resources\GenericCollection;
use App\Immunization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ResourcesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function diseases()
    {
        return new GenericCollection(Disease::all());
    }

    public function facilities()
    {
        return new GenericCollection(Facility::all());
    }

    public function cadres()
    {
        return new GenericCollection(Cadre::all());
    }

    public function facility_departments($_id)
    {
        $facility = Facility::find($_id);

        if (is_null($facility))
            abort(404, "Facility does not exist");

        return new GenericCollection($facility->departments);
    }

    public function post_feedback(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'type' => 'required|in:COMPLIMENT,COMPLAINT,SUGGESTION',
            'feedback' => 'required',
        ],[
//            'facility_id.required' => 'Please select your facility',
//            'facility_department_id.required' => 'Please select your department',
//            'cadre_id.required' => 'Please select your cadre'
        ]);


        $user = auth()->user();

        $hcw = HealthCareWorker::where('user_id', $user->id)->first();
        if (is_null($hcw))
            abort(403, "Please complete your profile to create feedback");


        $feedback = new Feedback();
        $feedback->user_id = $user->id;
        $feedback->facility_id = $hcw->facility_id;
        $feedback->category = $request->category;
        $feedback->type = $request->type;
        $feedback->feedback = $request->feedback;
        $feedback->anonymous = $request->anonymous;
//        $masterfile->dob = Carbon::createFromFormat('Y-m-d', $request->dob);  ;

        if ($request->hasFile('file')){

            $uploadedFile = $request->file('file');
            $filename = time().$uploadedFile->getClientOriginalName();

            $request->file('file')->storeAs("public/uploads", $filename);

            $feedback->file = "uploads/".$filename;
        }

        $feedback->saveOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback, our team will take it up'
        ], 201);


    }

    public function get_feedback()
    {
        return new GenericCollection(Feedback::orderBy('id', 'desc')->paginate(10));
    }


    public function devices()
    {
        $user = auth()->user();

        $hcw = HealthCareWorker::where('user_id', $user->id)->first();
        if (is_null($hcw))
            abort(403, "Please complete your profile to report exposure");

        return new GenericCollection(Device::where('facility_id', $hcw->facility_id)->get());
    }

    public function create_cme(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'body' => 'required',
            'image_file' => 'nullable|mimes:jpeg,jpg,png',
        ],[
//            'facility_id.required' => 'Please select your facility',
//            'facility_department_id.required' => 'Please select your department',
//            'cadre_id.required' => 'Please select your cadre'
        ]);

        $cme = new Cme();
        $cme->title = $request->title;
        $cme->body = $request->body;

        if ($request->hasFile('image_file')){
            $uploadedFile = $request->file('image_file');
            $filename = time().$uploadedFile->getClientOriginalName();

            $request->file('image_file')->storeAs("public/uploads", $filename);

            $cme->file = "uploads/".$filename;
        }

        $cme->saveOrFail();

        return response()->json([
            'success' => true,
            'message' => 'CME added successfully'
        ], 201);
    }

    public function create_protocol(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|numeric|exists:facilities,id',
            'title' => 'required',
            'body' => 'required',
            'image_file' => 'nullable|mimes:jpeg,jpg,png',
        ],[
//            'facility_id.required' => 'Please select your facility',
//            'facility_department_id.required' => 'Please select your department',
//            'cadre_id.required' => 'Please select your cadre'
        ]);

        $protocol = new FacilityProtocol();
        $protocol->facility_id = $request->facility_id;
        $protocol->title = $request->title;
        $protocol->body = $request->body;

        if ($request->hasFile('image_file')){
            $uploadedFile = $request->file('image_file');
            $filename = time().$uploadedFile->getClientOriginalName();

            $request->file('image_file')->storeAs("public/uploads", $filename);

            $protocol->file = "uploads/".$filename;
        }

        $protocol->saveOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Protocol added successfully'
        ], 201);
    }

    public function get_cmes()
    {
        return new GenericCollection(Cme::orderBy('id','desc')->paginate(10));
    }

    public function get_facility_protocols($id)
    {
        return new GenericCollection(FacilityProtocol::orderBy('id','desc')->where('facility_id',$id)->paginate(10));
    }

    public function get_hcw_facility_protocols()
    {
        $user = auth()->user();

        $hcw = HealthCareWorker::where('user_id', $user->id)->first();
        if (is_null($hcw))
            return response()->json([
                'success' => false,
                'message' => 'You do not belong to a facility. Please contact system admin'
            ], 200);


        return new GenericCollection(FacilityProtocol::orderBy('id','desc')->where('facility_id',$hcw->facility_id)->paginate(10));
    }


}
