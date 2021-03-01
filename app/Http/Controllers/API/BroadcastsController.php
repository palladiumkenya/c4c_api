<?php

namespace App\Http\Controllers\API;

use App\BroadCast;
use App\Cadre;
use App\Cme;
use App\HealthCareWorker;
use App\PartnerFacility;
use App\Http\Resources\GenericCollection;
use App\Jobs\SendDirectSMS;
use App\Jobs\SendSMS;
use App\Outbox;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class BroadcastsController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth:api');
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


    public function create_web_direct_broadcast(Request $request)
    {
        $request->validate([
            'phone_numbers' => 'required',
            'message' => 'required',
        ],[
//            'facility_id.exists' => 'Invalid facility ID',
        ]);


        $myArray = explode(',', $request->phone_numbers);


        Log::info("looping through direct messages...");
        foreach ($myArray as $msisdn){
            //schedule message job queue
            Log::info("getting mobile no...");
            Log::info($msisdn);


            Log::info("queueing sms...");

            SendDirectSMS::dispatch($msisdn, $request->message);
            Log::info("sms queued...");
        }
        Log::info("end of direct message loop...");


        return response()->json([
            'success' => true,
            'message' => 'Messages have been queued successfully'
        ], 200);


    }

    public function get_facility_broadcast_history($id)
    {
        return new GenericCollection(BroadCast::where('facility_id', $id)->orderBy('id','desc')->paginate(100));
    }

    public function get_partner_broadcast_history($id)
    {
        $hcws = PartnerFacility::where('partner_id',$id)->pluck('facility_id');

        Log::info("HCWs:". $hcws);

        return new GenericCollection(BroadCast::where('facility_id', $hcws)->orderBy('id','desc')->paginate(100));
    }

    public function get_all_broadcast_history()
    {
        return new GenericCollection(BroadCast::orderBy('id','desc')->paginate(100));
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

            Log::info("cadre id: ".$cadre_id);

            $cadre = Cadre::find($cadre_id);

            if (is_null($cadre))
                continue;

            Log::info("cadre found");
            Log::info(json_decode( json_encode($cadre), true));


            $hcws = HealthCareWorker::where('facility_id',$hcw->facility_id)->where('cadre_id', $cadre_id)->get();

            if ($hcws->count() == 0)
                continue;

            Log::info("hcws found ");
            Log::info(json_decode( json_encode($hcws), true));


            $broadCast = new BroadCast();
            $broadCast->facility_id = $hcw->facility_id;
            $broadCast->cadre_id = $cadre_id;
            $broadCast->created_by = auth()->user()->id;
            $broadCast->message = $request->message;
            $broadCast->audience = $hcws->count();
            $broadCast->saveOrFail();

            Log::info("broadcast: ");
            Log::info(json_decode( json_encode($broadCast), true));

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



    public function create_nascop_broadcast(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'instant' => 'required',
            'data' => 'required',
        ],[
//            'facility_id.exists' => 'Invalid facility ID',
        ]);

        if ($request->instant == 1){

            foreach($request['data'] as $recipient) {


                Log::info("queueing NASCOP sms...");
                SendDirectSMS::dispatch($recipient['to'], $recipient['message']);
                Log::info("NASCOP sms queued...");

            }

        }else{

            $scheduled = Carbon::createFromFormat('Y-m-d H:i', $request->date.' '.$request->time);

            $diff_in_minutes = $scheduled->diffInMinutes(Carbon::now());
//            print_r($scheduled);
//            print_r(Carbon::now());
//            print_r($diff_in_minutes);


            foreach($request['data'] as $recipient) {


                Log::info("queueing NASCOP sms...");
                SendDirectSMS::dispatch($recipient['to'], $recipient['message'])->delay(now()->addMinutes($diff_in_minutes));
                Log::info("NASCOP sms queued...");

            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Messages have been queued successfully'
        ], 200);

    }




}
