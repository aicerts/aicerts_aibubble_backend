<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function profile(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        return response()->json(['status' => true, 'data' => $customer], 200);
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }

        $customer = Auth::guard('customer')->user();
        $user = Customer::find($customer->id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 400);
        }

        if ($request->has('name')) {
            $user->name = $request->input('name');
        }

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('profile'), $imageName);
            $user->profile_image = 'profile/' . $imageName;
        }

        $user->save();

        return response()->json(['status' => true, 'message' => 'User profile updated successfully', 'user' => $user], 200);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 400);
        }

        $customer = Auth::guard('customer')->user();
        $user = Customer::find($customer->id);
        if (!Hash::check($request->input('old_password'), $user->password)) {
            return response()->json(['status' => false, 'message' => 'Incorrect old password'], 400);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json(['status' => true, 'message' => 'Password updated successfully'], 200);
    }


    public function logout(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        if ($customer && $customer->token()) {
            $customer->token()->revoke();
        }
        return response()->json(['status' => true, 'message' => 'Successfully logged out'], 200);
    }
}
