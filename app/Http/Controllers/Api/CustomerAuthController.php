<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CustomerAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=> false,'message' => $validator->errors()->first()], 400);
        }

        $user = Customer::where('email', $request->email)->first();

        if(!$user)
        {
            return response()->json(['status'=> false,'message' => 'Email Not Registered'], 400);
        }


        if (!$user || !Hash::check($request->password, $user->password)) {

            if($user->status==false)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Your account is inactive.'
                ], 400);
            }
            return response()->json(['status'=> false,'message' => 'Invalid Email Or Password'], 400);
        }

        $token = $user->createToken('api##@customer##@@@bubble')->accessToken;

        return response()->json(['status'=> true,'token' => $token], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=> false,'message' => $validator->errors()->first()], 400);
        }

        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $customer->createToken('api##@customer##@@@bubble')->accessToken;

        return response()->json(['status'=>true,'message' => 'Customer registered successfully','token'=>$token], 201);
    }


    public function send_forgot_email_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:customers,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=> false,'message' => $validator->errors()->first()], 400);
        }

        $email = $request->email;
        // $otp = Str::random(6);
        $otp = "111111";

        Otp::where(['email' => $email, 'status' => 0])->update(['status' => 2]);

        $save_otp = new Otp();
        $save_otp->email = $email;
        $save_otp->otp = $otp;
        $save_otp->save();

        try {

            // Mail::raw('Your OTP is: ' . $otp, function ($message) use ($email) {
            //     $message->to($email)->subject('Password Reset OTP');
            // });

            return response()->json(['status'=> true,'message' => 'OTP sent to your email'], 200);
            
        } catch (\Exception $e) {

            return response()->json(['status'=> false,'message' => 'Failed to send OTP'], 500);
        }
    }


    public function verifyAndResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:customers,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['status'=> false,'message' => $validator->errors()->first()], 400);
        }

        $otpRecord = OTP::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('status', 0)
            ->first();

        if (!$otpRecord) {
            return response()->json(['status'=> false,'message' => 'Invalid OTP'], 400);
        }

        $customer = Customer::where('email', $request->email)->first();

        $customer->password = Hash::make($request->password);
        $customer->save();
        $otpRecord->update(['status' => 1]);

        $customer->tokens()->delete();

        return response()->json(['status'=> true,'message' => 'Password reset successfully'], 200);
    }
}
