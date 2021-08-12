<?php

namespace App\Http\Controllers\API;

use App\Cadre;
use App\Cme;
use App\CmeFile;
use App\County;
use App\Device;
use App\Disease;
use App\Facility;
use App\PartnerFacility;
use App\FacilityDepartment;
use App\FacilityProtocol;
use App\FacilityProtocolFile;
use App\Feedback;
use App\HealthCareWorker;
use App\Http\Resources\GenericCollection;
use App\Http\Resources\GenericResource;
use App\Immunization;
use App\SpecialResource;
use App\SpecialResourceFile;
use App\SubCounty;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResourcesController extends Controller
{
    public function __construct()
    {
        //        $this->middleware('auth:api');
    }

    public function diseases()
    {
        return new GenericCollection(Disease::all());
    }

    public function facilities()
    {

        //        $facilities = Cache::get('facilities', function () {
        //            return DB::table('facilities')->get();
        //        });

        $facilities = Facility::all();

        return response()->json([
            'success' => true,
            'data' => $facilities
        ], 200);

    }

    public function facilities_paginated()
    {

        $facilities = Facility::paginate(50);

        return response()->json([
            'success' => true,
            'data' => $facilities
        ], 200);

    }

    public function partner_facilities($id)
    {
        $hcws = PartnerFacility::where('partner_id',$id)->pluck('facility_id');

        Log::info("HCWs:". $hcws);

        return new GenericCollection(Facility::where('id', $hcws)->orderBy('id','desc')->paginate(1000));

    }



    public function add_facility_department(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|numeric|exists:facilities,id',
            'department_name' => 'required',
        ],[
            //            'facility_id.required' => 'Please select your facility',
            //            'facility_department_id.required' => 'Please select your department',
            //            'cadre_id.required' => 'Please select your cadre'
        ]);

        $fDepartment = new FacilityDepartment();
        $fDepartment->facility_id = $request->facility_id;
        $fDepartment->department_name = $request->department_name;

        $fDepartment->saveOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Facility department added successfully'
        ], 201);
    }

    public function cadres()
    {
        return new GenericCollection(Cadre::all());
    }

    public function facility_departments($_id)
    {
        //        $facility = Facility::find($_id);
        //
        //        if (is_null($facility))
        //            abort(404, "Facility does not exist");

        return new GenericCollection(FacilityDepartment::all());
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


        DB::transaction(function () use ($request) {
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

            Log::info("CME saved succesfully, uplosding files");

            if ($request->hasFile('cme_files')) {
                $files = $request->file('cme_files');

                foreach ($files as $key=>$file) {
                    $filenameUp = time().$file->getClientOriginalName();
                    $request->file('cme_files')[$key]->storeAs("public/uploads", $filenameUp);

                    Log::info("file uploaded");

                    $cmeFile = new CmeFile();
                    $cmeFile->cme_id = $cme->id;
                    $cmeFile->file = "uploads/".$filenameUp;;
                    $cmeFile->saveOrFail();

                    Log::info("CME file saved");
                }
            }else{
                Log::info("cme_files not found");

            }

        });

        return response()->json([
            'success' => true,
            'message' => 'CME added successfully'
        ], 201);
    }

    public function update_cme(Request $request)
    {
        $request->validate([
            'cme_id' => 'required',
            'title' => 'required',
            'body' => 'required',
            'image_file' => 'nullable|mimes:jpeg,jpg,png',
        ],[
        //            'facility_id.required' => 'Please select your facility',
        //            'facility_department_id.required' => 'Please select your department',
        //            'cadre_id.required' => 'Please select your cadre'
        ]);


        $cme = Cme::find($request->cme_id);

        if (is_null($cme))
            abort(404,"CME not found");

        DB::transaction(function () use ($request, $cme) {
            $cme->title = $request->title;
            $cme->body = $request->body;

            if ($request->hasFile('image_file')){
                $uploadedFile = $request->file('image_file');
                $filename = time().$uploadedFile->getClientOriginalName();

                $request->file('image_file')->storeAs("public/uploads", $filename);

                $cme->file = "uploads/".$filename;
            }

            $cme->update();

            Log::info("CME updated succesfully, uploading files");

            if ($request->hasFile('cme_files')) {
                $files = $request->file('cme_files');

                foreach ($files as $key=>$file) {
                    $filenameUp = time().$file->getClientOriginalName();
                    $request->file('cme_files')[$key]->storeAs("public/uploads", $filenameUp);

                    Log::info("file uploaded");

                    $cmeFile = new CmeFile();
                    $cmeFile->cme_id = $cme->id;
                    $cmeFile->file = "uploads/".$filenameUp;;
                    $cmeFile->saveOrFail();

                    Log::info("CME file saved");
                }
            }else{
                Log::info("cme_files not found");

            }

        });

        return response()->json([
            'success' => true,
            'message' => 'CME updated successfully'
        ], 201);
    }

    public function get_cmes()
    {
        return new GenericCollection(Cme::orderBy('id','desc')->paginate(20));
    }

    public function get_cme($id)
    {
        $cme = Cme::find($id);
        if (is_null($cme))
            abort(404, "CME does not exist");
        return new GenericResource($cme);
    }

    public function delete_cme($id){
        $cme = Cme::find($id);
        if (is_null($cme))
            abort(404, "CME does not exist");

        $cme->delete();

        return response()->json([
            'success' => true,
            'message' => 'CME has been deleted successfully'
        ], 200);
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


        DB::transaction(function () use ($request) {

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

            Log::info("protocol saved succesfully, uplosding files");

            if ($request->hasFile('protocol_files')) {
                $files = $request->file('protocol_files');

                foreach ($files as $key=>$file) {
                    $filenameUp = time().$file->getClientOriginalName();
                    $request->file('protocol_files')[$key]->storeAs("public/uploads", $filenameUp);

                    Log::info("file uploaded");

                    $protocolFile = new FacilityProtocolFile();
                    $protocolFile->facility_protocol_id = $protocol->id;
                    $protocolFile->file = "uploads/".$filenameUp;;
                    $protocolFile->saveOrFail();

                    Log::info("protocol file saved");
                }
            }else{
                Log::info("protocol_files not found");

            }

        });




        return response()->json([
            'success' => true,
            'message' => 'Protocol added successfully'
        ], 201);
    }

    public function update_protocol(Request $request)
    {
        $request->validate([
            'protocol_id' => 'required',
            'facility_id' => 'required|numeric|exists:facilities,id',
            'title' => 'required',
            'body' => 'required',
            'image_file' => 'nullable|mimes:jpeg,jpg,png',
        ],[
        //            'facility_id.required' => 'Please select your facility',
        //            'facility_department_id.required' => 'Please select your department',
        //            'cadre_id.required' => 'Please select your cadre'
        ]);

        $protocol = FacilityProtocol::find($request->protocol_id);
        if (is_null($protocol))
            abort(404, "Protocol not found");

        DB::transaction(function () use ($request,$protocol) {

            $protocol->facility_id = $request->facility_id;
            $protocol->title = $request->title;
            $protocol->body = $request->body;

            if ($request->hasFile('image_file')){
                $uploadedFile = $request->file('image_file');
                $filename = time().$uploadedFile->getClientOriginalName();

                $request->file('image_file')->storeAs("public/uploads", $filename);

                $protocol->file = "uploads/".$filename;
            }

            $protocol->update();

            Log::info("protocol updated succesfully, uplosding files");

            if ($request->hasFile('protocol_files')) {
                $files = $request->file('protocol_files');

                foreach ($files as $key=>$file) {
                    $filenameUp = time().$file->getClientOriginalName();
                    $request->file('protocol_files')[$key]->storeAs("public/uploads", $filenameUp);

                    Log::info("file uploaded");

                    $protocolFile = new FacilityProtocolFile();
                    $protocolFile->facility_protocol_id = $protocol->id;
                    $protocolFile->file = "uploads/".$filenameUp;;
                    $protocolFile->saveOrFail();

                    Log::info("protocol file saved");
                }
            }else{
                Log::info("protocol_files not found");

            }

        });




        return response()->json([
            'success' => true,
            'message' => 'Protocol updated successfully'
        ], 201);
    }

    public function get_all_protocols()
    {
        return new GenericCollection(FacilityProtocol::with('facility')->orderBy('id','desc')->paginate(20));
    }

    public function get_facility_protocols_dashboard($id)
    {
        return new GenericCollection(FacilityProtocol::with('facility')->orderBy('id','desc')->where('facility_id',$id)->paginate(20));
    }

    public function get_protocols_details($id)
    {
        $fp = FacilityProtocol::find($id);

        if (is_null($fp))
            abort(404, "Protocol does not exist");

        return new GenericResource($fp);
    }

    public function delete_facility_protocol($id){
        $fp = FacilityProtocol::find($id);

        if (is_null($fp))
            abort(404, "Protocol does not exist");

        $fp->delete();

        return response()->json([
            'success' => true,
            'message' => 'Protocol has been deleted successfully'
        ], 200);
    }




    public function get_partner_protocols($id)
    {
        $hcws = PartnerFacility::where('partner_id',$id)->pluck('facility_id')->first();;

        Log::info("HCWs:". $hcws);

        return new GenericCollection(FacilityProtocol::where('facility_id', '=',$hcws)->paginate(20));
    }

    public function get_hcw_partner_protocols()
    {
        $user = auth()->user();

        $hcw = HealthCareWorker::where('user_id', $user->id)->first();
        if (is_null($hcw))
            return response()->json([
                'success' => false,
                'message' => 'You do not belong to a facility. Please contact system admin'
            ], 200);


        return new GenericCollection(FacilityProtocol::orderBy('id','desc')->where('facility_id',$hcw->facility_id)->paginate(20));
    }

    public function get_facility_protocols()
    {
        $user = auth()->user();

        $hcw = HealthCareWorker::where('user_id', $user->id)->first();
        if (is_null($hcw))
            return response()->json([
                'success' => false,
                'message' => 'You do not belong to a facility. Please contact system admin'
            ], 200);


        return new GenericCollection(FacilityProtocol::orderBy('id','desc')->where('facility_id',$hcw->facility_id)->paginate(20));
    }


    public function counties()
    {
        return new GenericCollection(County::all());
    }

    public function subcounties($id)
    {
        return new GenericCollection(SubCounty::where('county_id', $id)->get());
    }



    public function create_special_resource(Request $request)
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


        DB::transaction(function () use ($request) {
            $specialResource = new SpecialResource();
            $specialResource->title = $request->title;
            $specialResource->body = $request->body;

            if ($request->hasFile('image_file')){
                $uploadedFile = $request->file('image_file');
                $filename = time().$uploadedFile->getClientOriginalName();

                $request->file('image_file')->storeAs("public/uploads", $filename);

                $specialResource->file = "uploads/".$filename;
            }

            $specialResource->saveOrFail();

            Log::info("specialResource saved succesfully, uplosding files");

            if ($request->hasFile('resource_files')) {
                $files = $request->file('resource_files');

                foreach ($files as $key=>$file) {
                    $filenameUp = time().$file->getClientOriginalName();
                    $request->file('resource_files')[$key]->storeAs("public/uploads", $filenameUp);

                    Log::info("file uploaded");

                    $specialFile = new SpecialResourceFile();
                    $specialFile->special_resource_id = $specialResource->id;
                    $specialFile->file = "uploads/".$filenameUp;;
                    $specialFile->saveOrFail();

                    Log::info(" special resource file saved");
                }
            }else{
                Log::info("resource_files not found");

            }

        });

        return response()->json([
            'success' => true,
            'message' => 'Special resource added successfully'
        ], 201);
    }

    public function update_special_resource(Request $request)
    {
        $request->validate([
            'special_resource_id' => 'required',
            'title' => 'required',
            'body' => 'required',
            'image_file' => 'nullable|mimes:jpeg,jpg,png',
        ],[
//            'facility_id.required' => 'Please select your facility',
//            'facility_department_id.required' => 'Please select your department',
//            'cadre_id.required' => 'Please select your cadre'
        ]);


        $specialResource = SpecialResource::find($request->special_resource_id);
        if (is_null($specialResource))
            abort(404, "Special resource not found");

        DB::transaction(function () use ($request,$specialResource) {

            $specialResource->title = $request->title;
            $specialResource->body = $request->body;

            if ($request->hasFile('image_file')){
                $uploadedFile = $request->file('image_file');
                $filename = time().$uploadedFile->getClientOriginalName();

                $request->file('image_file')->storeAs("public/uploads", $filename);

                $specialResource->file = "uploads/".$filename;
            }

            $specialResource->update();

            Log::info("specialResource updated succesfully, uplosding files");

            if ($request->hasFile('resource_files')) {
                $files = $request->file('resource_files');

                foreach ($files as $key=>$file) {
                    $filenameUp = time().$file->getClientOriginalName();
                    $request->file('resource_files')[$key]->storeAs("public/uploads", $filenameUp);

                    Log::info("file uploaded");

                    $specialFile = new SpecialResourceFile();
                    $specialFile->special_resource_id = $specialResource->id;
                    $specialFile->file = "uploads/".$filenameUp;;
                    $specialFile->saveOrFail();

                    Log::info(" special resource file saved");
                }
            }else{
                Log::info("resource_files not found");

            }

        });

        return response()->json([
            'success' => true,
            'message' => 'Special resource updated successfully'
        ], 201);
    }

    public function get_special_resources()
    {
        return new GenericCollection(SpecialResource::orderBy('id','desc')->paginate(20));
    }

    public function get_special_resource($id)
    {
        $specialResource = SpecialResource::find($id);
        if (is_null($specialResource))
            abort(404, "Special resource does not exist");
        return new GenericResource($specialResource);
    }

    public function delete_special_resource($id)
    {
        $specialResource = SpecialResource::find($id);
        if (is_null($specialResource))
            abort(404, "Special resource does not exist");

        $specialResource->delete();

        return response()->json([
            'success' => true,
            'message' => 'Special resource has been deleted successfully'
        ], 200);
    }





}
