<?php

namespace App\Http\Controllers\API;

use App\BroadCast;
use App\Cadre;
use App\Cme;
use App\HealthCareWorker;
use App\Http\Resources\GenericCollection;
use App\Jobs\SendSMS;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class BroadcastsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function create_web_broadcast(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'cadres' => 'required',
            'message' => 'required',
        ],[
            'facility_id.exists' => 'Invalid facility ID',
        ]);

        foreach($request['cadres'] as $cadre_id) {
            $cadre = Cadre::find($cadre_id);

            if (is_null($cadre))
                continue;

            $hcws = HealthCareWorker::where('facility_id',$request->facility_id)->where('cadre_id', $cadre_id)->get();

            if ($hcws->count() == 0)
                continue;


            $broadCast = new BroadCast();
            $broadCast->facility_id = $request->facility_id;
            $broadCast->cadre_id = $cadre_id;
            $broadCast->created_by = auth()->user()->id;
            $broadCast->approved_by = auth()->user()->id; //auto approved
            $broadCast->approved = 1; //auto approved
            $broadCast->message = $request->message;
            $broadCast->audience = $hcws->count();
            $broadCast->saveOrFail();

            Log::info("looping through hcw users...");
            foreach ($hcws as $hcw){
                //schedule message job queue
                Log::info("getting mobile no...");
                Log::info($hcw->user->msisdn);
                Log::info("queueing sms...");
                SendSMS::dispatch($hcw->user, $request->message);
                Log::info("sms queued...");
            }
            Log::info("end of hcw users loop...");



        }
        return response()->json([
            'success' => true,
            'message' => 'Messages have been queued successfully'
        ], 200);


    }

    public function get_facility_broadcast_history($id)
    {
        return new GenericCollection(BroadCast::where('facility_id', $id)->orderBy('id','desc')->paginate(10));
    }

    public function get_all_broadcast_history()
    {
        return new GenericCollection(BroadCast::orderBy('id','desc')->paginate(10));
    }

    public function create_mobile_broadcast(Request $request)
    {
        $request->validate([
            'cadres' => 'required',
            'message' => 'required',
        ],[
            //'facility_id.exists' => 'Invalid facility ID',
        ]);

        $hcw = HealthCareWorker::where('user_id', auth()->user()->id)->first();
        if (is_null($hcw))
            return response()->json([
                'success' => false,
                'message' => 'You do not belong to a facility. Please contact system admin'
            ], 200);


        Log::info("hcw found ");
        Log::info(json_encode($hcw));

        foreach($request['cadres'] as $cadre_id) {

            Log::info("cadre id".$cadre_id);

            $cadre = Cadre::find($cadre_id);

            if (is_null($cadre))
                continue;

            Log::info("cadre found", json_decode($cadre));


            $hcws = HealthCareWorker::where('facility_id',$hcw->facility_id)->where('cadre_id', $cadre_id)->get();

            if ($hcws->count() == 0)
                continue;

            Log::info("hcws found ");
            Log::info(json_encode($hcws));


            $broadCast = new BroadCast();
            $broadCast->facility_id = $hcw->facility_id;
            $broadCast->cadre_id = $cadre_id;
            $broadCast->created_by = auth()->user()->id;
            $broadCast->message = $request->message;
            $broadCast->audience = $hcws->count();
            $broadCast->saveOrFail();

            Log::info("broadcast: ");
            Log::info(json_decode($broadCast));

        }
        return response()->json([
            'success' => true,
            'message' => 'Messages have been queued successfully'
        ], 200);


    }

    public function pending_mobile_broadcasts()
    {
        $hcw = HealthCareWorker::where('user_id', auth()->user()->id)->first();
        if (is_null($hcw))
            return response()->json([
                'success' => false,
                'message' => 'You do not belong to a facility. Please contact system admin'
            ], 200);


        return new GenericCollection(BroadCast::where('approved',0)->where('facility_id',$hcw->facility_id)->orderBy('id','desc')->paginate(10));
    }

    public function approved_mobile_broadcasts()
    {
        $hcw = HealthCareWorker::where('user_id', auth()->user()->id)->first();
        if (is_null($hcw))
            return response()->json([
                'success' => false,
                'message' => 'You do not belong to a facility. Please contact system admin'
            ], 200);


        return new GenericCollection(BroadCast::where('approved',1)->where('facility_id',$hcw->facility_id)->orderBy('id','desc')->paginate(10));
    }

    public function approve_mobile_broadcast(Request $request)
    {
        $request->validate([
            'broadcast_id' => 'required|exists:broad_casts,id',
        ],[
            'broadcast_id.exists' => 'Invalid broadcast',
        ]);


        $hcw = HealthCareWorker::where('user_id', auth()->user()->id)->first();
        if (is_null($hcw))
            return response()->json([
                'success' => false,
                'message' => 'You do not belong to a facility. Please contact system admin'
            ], 200);

        $broadcast = BroadCast::find($request->broadcast_id);

        if (auth()->user()->id == $broadcast->created_by)
            return response()->json([
                'success' => false,
                'message' => 'You can not approve the same broadcast you scheduled'
            ], 200);

        if ($broadcast->approved == "Yes")
            return response()->json([
                'success' => false,
                'message' => 'This broadcast has already been approved and sent'
            ], 200);

        $cadre = Cadre::find($broadcast->cadre_id);

        if (is_null($cadre))
            return response()->json([
                'success' => false,
                'message' => 'Invalid cadre. Please contact system admin'
            ], 200);


        //update broadcast
        $broadcast->approved_by = auth()->user()->id;
        $broadcast->approved = 1;
        $broadcast->update();


        $hcws = HealthCareWorker::where('facility_id',$broadcast->facility_id)->where('cadre_id', $broadcast->cadre_id)->get();

        Log::info("looping through hcw users...");
        foreach ($hcws as $hcw){
            //schedule message job queue
            Log::info("getting mobile no...");
            Log::info($hcw->user->msisdn);
            Log::info("queueing sms...");
            SendSMS::dispatch($hcw->user, $broadcast->message);
            Log::info("sms queued...");
        }
        Log::info("end of hcw users loop...");


        return response()->json([
            'success' => true,
            'message' => 'Messages have been queued for sending successfully'
        ], 200);

    }



}
