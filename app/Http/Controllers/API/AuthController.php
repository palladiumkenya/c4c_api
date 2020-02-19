<?php

namespace App\Http\Controllers\API;

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
            'role_id' => 'required|numeric:exists:roles,id',
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
                'msisdn' => $request->msisdn,
                'password' => bcrypt($request->password),
            ]);
            $user->save();

            //$user->sendEmailVerificationNotification();


        });

        //$user->notify(new SignupActivate($user));

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. Please login to continue'
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
                'message' => 'Invalid credentials, please check your email and password'
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
                'message' => 'Phone number is not registered'
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
            //send_sms($request->msisdn, $message);

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
            $user->password = bcrypt($request->password);
            $user->update();

           // send_sms($request->msisdn, "Your password was reset successfully");

            return response()->json([
                'success' => true,
                'message' => 'Your password was reset successfully'
            ], 200);
        }

    }

    public function generateOtp(){
        return rand(1000, 9999);
    }
}
