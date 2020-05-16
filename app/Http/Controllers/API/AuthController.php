<?php

namespace App\Http\Controllers\API;

use App\Cadre;
use App\Facility;
use App\FacilityDepartment;
use App\HealthCareWorker;
use App\Jobs\SendSMS;
use App\Otp;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $request->validate([
            'role_id' => 'required|numeric|exists:roles,id',
            'first_name' => 'required|string',
            'surname' => 'required|string',
            'gender' => 'required|string',
            'msisdn' => 'required|string|unique:users',
            'email' => 'nullable|string|email|unique:users',
            'password' => 'required|string|min:4|confirmed'
        ],[
            'email.unique' => 'The email is already registered, please log in to continue',
            'msisdn.required' => 'Your phone number is required',
            'msisdn.unique' => 'The phone number has already been taken. '
        ]);


        DB::transaction(function() use ($request) {
            $user = new User([
                'role_id' => $request->role_id,
                'first_name' => $request->first_name,
                'surname' => $request->surname,
                'gender' => $request->gender,
                'email' => $request->email,
                'consent' => $request->consent ? $request->consent : 1,
                'msisdn' => $request->msisdn,
                'password' => bcrypt($request->password),
            ]);
            $user->save();
            send_sms($request->msisdn, $request->message ? $request->message : "Welcome ".$request->first_name." to Care For the Carer (C4C) SMS Platform. You have been successfully registered. Messages sent and received are not charged. MOH");

            SendSMS::dispatch($user,"C4C provides health care workers with communication & resource center with information on COVID-19, Occupational PEP & other health promotion services. MOH")->delay(now()->addMinutes(3));;
            //$user->sendEmailVerificationNotification();


        });

        //$user->notify(new SignupActivate($user));

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. Please login to continue'
        ], 201);
    }

    public function upload_bulk_users(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'surname' => 'required|string',
            'gender' => 'required|string',
            'msisdn' => 'required|string|unique:users',
            'email' => 'nullable|string|email|unique:users',



            'facility_id' => 'required|numeric|exists:facilities,id',
            'facility_department' => 'required|exists:facility_departments,department_name',
            'cadre' => 'required|exists:cadres,name',
            'dob' => 'required',
        ],[
            'email.unique' => 'The email is already registered',
            'msisdn.required' => 'Phone number is required',
            'msisdn.unique' => 'The phone number has already been taken. ',


            'facility_id.required' => 'Please select a facility',
            'facility_department_id.required' => 'Please enter a department',
            'cadre_id.required' => 'Please enter a cadre'
        ]);


        DB::transaction(function() use ($request) {
            $user = new User([
                'role_id' => 3, //hcw
                'first_name' => $request->first_name,
                'surname' => $request->surname,
                'gender' => $request->gender,
                'email' => $request->email,
                'msisdn' => $request->msisdn,
                'password' => bcrypt($request->msisdn),
            ]);
            $user->save();
            send_sms($request->msisdn, $request->message ? $request->message : "Welcome ".$request->first_name." to Care For the Carer (C4C) SMS Platform. You have been successfully registered. Messages sent and received are not charged. MOH");

            SendSMS::dispatch($user,"C4C provides health care workers with communication & resource center with information on COVID-19, Occupational PEP & other health promotion services. MOH")->delay(now()->addMinutes(3));;
            //$user->sendEmailVerificationNotification();

            $fDepartment = FacilityDepartment::where('department_name', $request->facility_department)->first();
            $fCadre = Cadre::where('name', $request->cadre)->first();

            $hcw = new HealthCareWorker();
            $hcw->user_id = $user->id;
            $hcw->facility_id = $request->facility_id;
            $hcw->facility_department_id = is_null($fDepartment) ? 1 : $fDepartment->id;
            $hcw->cadre_id = is_null($fCadre) ? 1 : $fCadre->id;
            $hcw->dob = $request->dob;
            $hcw->id_no = $request->id_no;
            $hcw->saveOrFail();

            $user->profile_complete = 1;
            $user->update();


        });

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.'
        ], 201);
    }

    public function login(Request $request)
    {

        $request->validate([
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);

        if ($request->email == null){
            $credentials = request(['msisdn', 'password']);
        }
        else{
            $credentials = request(['email', 'password']);
        }

//        $credentials['active'] = 1;
//        $credentials['deleted_at'] = null;


        if(!Auth::attempt($credentials))
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials, please check your phone number and password'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
//        if ($request->remember_me)
//            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'success' => true,
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'user' => $request->user()
        ]);

    }


    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }

    public function user(Request $request)
    {
        return new UserResource($request->user());
    }

    public function send_reset_otp(Request $request)
    {
        $request->validate([
            'msisdn' => 'required'
        ]);

        $user = User::where('msisdn',$request->msisdn)->first();

        if (is_null($user)){
            return response()->json([
                'success' => false,
                'message' => 'Phone number is not registered',
                'errors' => 'Please sign up first to reset your password'
            ], 200);
        }else{
            $code = $this->generateOtp();

            Otp::where('msisdn', '=', $request->msisdn)
                ->update(array('verified' => 'yes','verification_date'=>Carbon::now()));


            $otp = new Otp();
            $otp->msisdn = $request->msisdn;
            $otp->verification_code = $code;
            $otp->verified = "no";
            $otp->saveOrFail();

            $message = "Use this OTP to reset your password: ".$code;
            Log::info("Generate OTP for ".$request->msisdn.": ".$code);
            send_sms($request->msisdn, $message);

            return response()->json([
                'success' => true,
                'message' => 'Check your phone inbox for OTP'
            ], 200);

        }

    }

    public function verify_otp(Request $request)
    {
        $request->validate([
            'msisdn' => 'required',
            'otp' => 'required'
        ]);

        $otp = OTP::where('msisdn',$request->msisdn)->where('verification_code',$request->otp)->orderBy('id','desc')->first();

        if (is_null($otp)){
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP'
            ], 200);
        }else{
            if ($otp->verified == 'yes'){
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has already expired'
                ], 200);
            }else{
                $otp->verified = 'yes';
                $otp->verification_date = Carbon::now();
                $otp->update();

                return response()->json([
                    'success' => true,
                    'message' => 'OPT verified. Please set new password'
                ], 200);

            }
        }
    }

    public function reset_password(Request $request)
    {
        $request->validate([
            'otp' => 'required',
            'msisdn' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('msisdn',$request->msisdn)->first();

        if (is_null($user)){
            return response()->json([
                'success' => false,
                'message' => 'Invalid user'
            ], 200);
        }else{

            $otp = OTP::where('msisdn',$request->msisdn)->where('verification_code',$request->otp)->orderBy('id','desc')->first();

            if (is_null($otp)){
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP'
                ], 200);
            }else{
                if ($otp->verified == 'yes'){
                    return response()->json([
                        'success' => false,
                        'message' => 'OTP has already expired'
                    ], 200);
                }else{
                    $otp->verified = 'yes';
                    $otp->verification_date = Carbon::now();
                    $otp->update();

                    $user->password = bcrypt($request->password);
                    $user->update();

                     send_sms($request->msisdn, "Your password was reset successfully");

                    return response()->json([
                        'success' => true,
                        'message' => 'Your password was reset successfully. Please log in to continue'
                    ], 200);
                }
            }
        }

    }

    public function generateOtp(){
        return rand(1000, 9999);
    }
}
